<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
     /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message,$code = 200)
    {
    	$response = [
            'statusCode' => $code,
            'message' => $message,
            'data'    => $result,
        ];
        return response()->json($response);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error = [],$errorMessages,$code = 404)
    {
        $response = [
            'statusCode' => $code,
            'message' => $errorMessages,
        ];
        if(!empty($error)){
            $response['data'] = $error;
        }
        return response()->json($response); 
    }

   
}
