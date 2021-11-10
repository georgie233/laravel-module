<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    //
    protected $isAjax = false;
    public function __construct()
    {
        if (request()->ajax()){
            $this->isAjax = true;
        }
    }

    public function responseSuccess($data){
        return response()->json([
            'code' => 200,
            'message' => '成功',
            'data' => $data,
        ], 200);
    }
    public function responseIllegal(){
        return response()->json([
            'code' => 403,
            'message' => '非法访问',
            'data' => [],
        ], 200);
    }
}
