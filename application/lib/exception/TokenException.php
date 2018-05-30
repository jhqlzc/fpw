<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 16:52
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 10001;
}