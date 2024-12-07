<?php
namespace App\Traits;

trait HttpResponses
{
    public function success(array $data=[], $message = 'Success', int  $statusCode = 200)
    {

        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
        ], $data), $statusCode);
    }

    public function error($message = 'Error',int  $statusCode = 500, $errors = [])
    {
        return response()->json(array_merge([
            'status' => false,
            'message' => $message,
        ],$errors), $statusCode);
    }
}