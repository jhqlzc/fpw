<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/23 0023
 * Time: 14:02
 */

namespace app\lib\exception;


class LogAndRegException extends BaseException
{
    public $msg = '手机号已被注册';
    public $errorCode = 20001;

}