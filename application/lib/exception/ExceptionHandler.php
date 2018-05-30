<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/20 0020
 * Time: 14:40
 */

namespace app\lib\exception;


use think\exception\Handle;
use Exception;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{

    private $msg;
    private $errorCode;

    //需要返回客户端当前请求的路径

    //重写父类方法render
    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            //如果是自定义异常
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            //Config::get('app_debug');
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->msg = "服务器内部错误，不想告诉你";
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result, 200);
    }

    public function recordErrorLog(Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}