<?php

namespace App\Http\Controllers;

use App\LoginToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    //
    protected $isAjax = false;
    protected $error = [];

    public function __construct()
    {
        $h = request()->header('Accept');

        if (request()->expectsJson()) {
            $this->isAjax = true;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorCode()
    {
        return $this->error[0];
    }

    public function getErrorMsg()
    {
        return $this->error[1];
    }

    public function setError($code, $msg)
    {
        $this->error[0] = $code;
        $this->error[1] = $msg;
    }

    public function responseSuccess($data)
    {
        if ($this->getError()) {
            //有错误拦截
            return $this->responseJson([], $this->getErrorMsg(), $this->getErrorCode());
            $this->error = [];
        }
        return response()->json([
            'code' => 200,
            'message' => '成功',
            'data' => $data,
        ], 200);
    }

    public function responseError($msg='',$code=403){
        return $this->responseJson([],$msg,$code);
    }

    public function responseIllegal()
    {
        return response()->json([
            'code' => 403,
            'message' => '非法访问',
            'data' => [],
        ], 200);
    }

    public function responseJson($data, $msg, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ], 200);
    }

    public function handleAjaxIndex($request, $model)
    {

        $search = [];
        foreach ($request->all() as $key => $item) {
            if (stristr($key, 'search_')) {
                $search[str_replace('search_', '', $key)] = $item;
            }
        }
        if (count($search))
            return $this->handleAjaxSearchIndex($request, $model, $search);

        if (isset($request->start) || isset($request->len)) {
            $data = $model->offset($request->start ?? 0)->limit($request->len ?? 10)->get();
        } else {
            $data = $model->all();
        }
        return [
            'list' => $data,
            'count' => $model->count(),
        ];
    }

    public function handleAjaxSearchIndex($request, $model, $search)
    {
        try {
            if (isset($request->start) || isset($request->len)) {
                $data = $model->where($search)->offset($request->start ?? 0)->limit($request->len ?? 10)->get();
            } else {
                $data = $model->where($search)->get();
            }
            return [
                'list' => $data,
                'count' => $model->where($search)->count(),
            ];
        } catch (\Exception $exception) {
            if ($exception->getCode() == '42S22') {
                return $this->setError(400, '请求参数有误');
            }
        }
    }

    public function backMsg($request, $msg)
    {
        if ($this->isAjax) {
            return $this->responseJson([], $msg, 400);
        } else {
            $request->flash();
            return back()->with('danger', $msg);
        }
    }

    public function getRequesFile($request, $name)
    {
        $img = $request->file($name);
        return $img;
    }

    public function saveFile($request, $name, $msg = '', $prefix = '')
    {
        $img = $this->getRequesFile($request, $name);
        if (!$img) return $this->backMsg($request, $msg);//没有上传文件
        $ex = $img->getClientOriginalExtension();//扩展名
        $path = $img->getRealPath();//绝对路径
        $filename = date('Ymdhis') . str_random(5) . '.' . $ex;
        $b = \Storage::disk('public')->put($prefix . '/' . $filename, file_get_contents($path));
        if (!$b) return $this->backMsg($request, '文件上传失败');
        $l = \Storage::disk('public')->url('');
        return $l . ($prefix ? $prefix . '/' : '') . $filename;
    }

    public function delFile($url, $prefix = '')
    {
        $disk = \Storage::disk('public');
        $path = str_replace($disk->url(''), '', $url);
        echo(($prefix ? $prefix . '/' : $prefix) . $path);
        dd($disk->delete(($prefix ? $prefix . '/' : $prefix) . $path));
        return $disk->delete(($prefix ? $prefix . '/' : $prefix) . $path);
    }

    public function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
        // 如果via信息含有wap则一定是移动设备
        if (isset ($_SERVER['HTTP_VIA'])) return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为true
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel',
                'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini',
                'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) return true;
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    public function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser()
    {
        try {
            if ($this->isAjax) {
//            $token = \request()->headers->get('Authorization');
//            $token = str_replace("Bearer ","",$token);
//            return (new LoginToken())->getModel($token);
            } else return Auth::user();
        } catch (\Exception $exception) {
            if ($this->isAjax) return $this->responseJson([], '服务器错误:获取不到用户信息', 400);
            else  dd("服务器错误:获取不到用户信息");
        }
    }
}
