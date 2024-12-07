<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

                if($validator->fails()){
                  return $this->error('validation error', 422, ['errors' => $validator->errors()]);
                }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->success($success, 'User register successfully.', 201);
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validator->fails()){
                return $this->error('validation error', 422, ['errors' => $validator->errors()]);
            }


            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                  $user = Auth::user();
                $token = $user->createToken("API TOKEN")->plainTextToken;
                return $this->success(['token' => $token, 'type' => $user->role], 'User Logged In Successfully');

            } else {
                return $this->error('Unauthorised', 401, ['error' => 'Unauthorised']);
            }
        }
        catch (\Exception $e) {
            return $this->error('Error', 401, ['error' => $e->getMessage()]);
        }


    }
}
