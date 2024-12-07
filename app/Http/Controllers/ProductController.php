<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CommonService\ResourceControllerService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use HttpResponses;

    protected $resourceService;
    public function __construct(ResourceControllerService $resourceService, Request $request)
    {
        $this->resourceService = $resourceService;
        $this->resourceService->setvalue($request, new Product());
    }




    public function index(Request $request)
    {
        return $this->resourceService->doIndex();
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation error', 422, ['errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            $product = Product::create($validator->validated());
            DB::commit();
            return $this->success(['data' => $product], 'Product created successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = Product::find($id);
            if ($product) {
                return $this->success(['data' => $product], 'Product found.', 200);
            } else {
                return $this->error('Not found', 404, ['error' => 'Product not found.']);
            }
        } catch (\Exception $e) {
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation error', 422, ['errors' => $validator->errors()]);
        }
        DB::beginTransaction();
        try {
            $product = Product::find($id);
            if ($product) {
                $product->update($validator->validated());
                DB::commit();
                return $this->success(['data' => $product], 'Product updated successfully.', 200);
            } else {
                return $this->error('Not found', 404, ['error' => 'Product not found.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::find($id);
            if ($product) {
                $product->delete();
                return $this->success(['data' => $product], 'Product deleted successfully.', 200);
            } else {
                return $this->error('Not found', 404, ['error' => 'Product not found.']);
            }
        } catch (\Exception $e) {
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }
    }
}
