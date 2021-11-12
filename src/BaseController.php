<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    //
    protected $isAjax = false;
    public function __construct()
    {
        $h = request()->header('Accept');

        if (request()->expectsJson()){
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

    public function handleAjaxIndex($request,$model){
        if (isset($request->start) || isset($request->len)){
            $data = $model->offset($request->start??0)->limit($request->len??10)->get();
        }
        else {
            $data = $model->all();
        }
        return [
            'list'=>$data,
            'count'=>$model->all()->count()
        ];
    }

    public function backMsg($request,$msg){
        $request->flash();
        return back()->with('danger',$msg);
    }

    public function saveFile($request,$name,$msg){
        $img = $request->file($name);
        if (!$img) return $this->backMsg($request,$msg);//没有上传文件
        $ex=$img->getClientOriginalExtension();//扩展名
        $path=$img->getRealPath();//绝对路径
        $filename=date('Ymdhis').str_random(5).'.'.$ex;
        $b = \Storage::disk('public')->put($filename,file_get_contents($path));
        if (!$b)return $this->backMsg($request,'上传失败');
        return \Storage::disk('public')->url($filename);
    }

    public function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备
        if (isset ($_SERVER['HTTP_VIA']))
        {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT']))
        {
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT']))
        {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                return true;
            }
        }
        return false;
    }

    public function isWeixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
