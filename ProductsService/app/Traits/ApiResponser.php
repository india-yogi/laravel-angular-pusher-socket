<?php

namespace App\Traits;

trait ApiResponser
{
    /**
     * Success Response
     * @param $data
     * @param int $code
     * @return $this
     */
    public function successResponse($data, $message = '', $code = \Illuminate\Http\Response::HTTP_OK)
    {
        $success = [
                    'status'    => 'Success',
                    'message'   => $message,
                    'code'      => $code,
                    'data'      => $data
                   ];

        return response($success, $code)->header('Content-Type', 'application/json');
    }

    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code)->header('Content-Type', 'application/json');
    }

    public function errorMessage($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }
}