<?php
namespace App\Services\CommonService;
use App\Traits\HttpResponses;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class ResourceControllerService
{
    use HttpResponses; // Import the HttpResponses trait for HTTP response handling.

    /**
     * The HTTP request object.
     *
     * @var mixed $request
     */
    private $request;

    /**
     * The name of the table associated with the model.
     *
     * @var string|null $table_name
     */
    private $table_name;

    /**
     * The model object to operate on.
     *
     * @var mixed|null $model
     */
    private $model;

    /**
     * An additional object, if provided, for query operations.
     *
     * @var mixed|null $object
     */
    private $object;


    /**
     * The constructor method to initialize the class properties.
     *
     * @param mixed $request
     * @param mixed $model
     */
    function setvalue($request, $model)
    {
        // Assign the provided $request to the class property $this->request, or null if not provided.
        $this->request = $request ?? null;

        // Assign the provided $model to the class property $this->model, or null if not provided.
        $this->model = $model ?? null;

        // Retrieve the table name associated with the model and assign it to the class property $this->table_name.
        $this->table_name = $this->model->getTable();
    }

    public function doIndex($message = null, $object = null)
    {
        $this->object = $object;
        $this->model=$this->object?->getModel() ?? $this->model;
        // Retrieve the fillable column names from the model.
        $fillableColumns = $this->model->getFillable();

        // Retrieve the hidden column names from the model.
        $hiddenColumns = $this->model->getHidden();
        // Calculate the difference between fillable and hidden columns to get only visible columns.
        $colmun = array_diff($fillableColumns, $hiddenColumns);
        $colmun=array_merge($colmun, $this->model->operationalColumns??[]);
        array_push($colmun, "id");
        $colmun=array_unique($colmun);
        // Retrieve requested fields and sort parameter
        $fields = array_map('strtolower', explode(',', $this->request->input('fields')));
        $sortParam = $this->request->input('sort');
        $searchWithLike = $this->request->input('search');

        // Merge requested fields into the request
        $this->request->merge(['fields' => $fields, 'param' => array_keys($this->request->query())]);

        // Define accepted parameters
        $param = ['page', 'fields', 'paginate', 'sort','search'];

        // Retrieve search parameters
        $searchParams = $this->request->only($colmun);
        // Validate request parameters
        $validateUser = Validator::make(
            $this->request->all(),
            [
                'page' => 'integer',
                'paginate' => 'integer',
                'param.*' => [Rule::in(array_merge($param, array_map('strtolower', $colmun)))],
                'fields.*' => [Rule::in(array_map('strtolower', $colmun))],
                'sort' => ['regex:/^[a-zA-Z_]+:(asc|desc)(,[a-zA-Z_]+:(asc|desc))*$/'],
            ]
        );

        // Return error response if validation fails
        if ($validateUser->fails()) {
            return $this->error('validation error', 422, ['errors' => $validateUser->errors()]);
        }

        /**
         * Selected Custom squery id
         */
        // // Initialize query with provided object or default model
        $query = $this->object ?? $this->model::query();
        // Order the query by default (if not specified)
        $pivotTable = null;
        if(isset($this->object))
        {

            if (method_exists($this->object, 'getRelated')&& $this->object->exists()) {
                $pivotTable = $this->object->getRelated()->getTable();
                $query = $this->object->getQuery();
            }

        }

         $pivotTable ? $query->latest($pivotTable.'.created_at'): $query->latest();

        // Apply dynamic search parameters
        /**
         * example {{host}}/user?email=admin@admin.com&name=admin
         */
        foreach ($searchParams as $field => $value) {
            // Adjust the condition based on your needs
            $pivotTable ? $query->where($pivotTable .".".$field, $value): $query->where($field, $value);
        }
        // Apply sorting if specified
        /**
         * example {{host}}/user?sort=id:desc
         */
        if ($sortParam) {
            foreach (explode(',', $sortParam) as $field) {
                list($fieldName, $sortOrder) = explode(':', $field);
                $pivotTable ? $query->orderBy($pivotTable . "." .$fieldName, $sortOrder): $query->orderBy($fieldName, $sortOrder);
            }
        }

        /**
         * example {{host}}/user?search=id:3,phone:01617777010
         */
        if ($searchWithLike) {
            foreach (explode(',', $searchWithLike) as $field) {
                list($searchFieldName, $searchFieldValue) = explode(':', $field);
                 $pivotTable ? $query->where($pivotTable . "." .$searchFieldName, 'like', "%$searchFieldValue%"):$query->where($searchFieldName, 'like', "%$searchFieldValue%");
            }
        }


        // Retrieve paginated data based on fields and pagination parameters
        return $this->success(
            [
                'data' => $query->select($fields[0] == null ? '*' : $fields)->paginate($this->request->input('paginate') ?? 10)
            ],
            $message ?? str_replace("_", " ", ucfirst($this->table_name)) . ' list get successfully'
        );
    }
    public function doCreate()
    {

    }
    public function doStore($message=null,$request=null,array $moreData=[]){
        $data = $this->model::create($request??$this->request->toArray());
        /**
         * Return response
         */
        return $this->success(array_merge(['data' => $data],$moreData), $message ?? str_replace("_"," ", ucfirst($this->table_name)) . ' store successfully');

    }
    public function doShow($message = null, $data=null){
        return $this->success(['data' => $data], $message ?? str_replace("_", " ", ucfirst($this->table_name)) . ' view successfully');
    }
    public function doEdit(){

    }
    public function doUpdate($message = null, $data = null,$request=null){
       $data->update($request??$this->request->toArray());
        return $this->success(["data"=>$data], $message ?? str_replace("_", " ", ucfirst($this->table_name)) . ' updated successfully');
    }
    public function doDestroy($message = null, $data=null){
        isset($data)? $data->delete():null;
        return $this->success([], $message ?? str_replace("_", " ", ucfirst($this->table_name)) . ' deleted successfully');
    }
    // Method to generate regex pattern for search parameter
    private function generateSearchRegexPattern($columns)
    {
        return '/^(' . implode('|', array_map(function ($key) {
            return $key . ':\d+';
        }, $columns)) . ')(,(' . implode('|', array_map(function ($key) {
            return $key . ':\d+';
        }, $columns)) . '))*$/';
    }

}

