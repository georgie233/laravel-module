<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
    public function responseSuccess($data){
        return response()->json([
            'code' => 200,
            'message' => '成功',
            'data' => $data,
        ], 200);
    }
}
