<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/27 0027
 * Time: 17:32
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $msg = '用户不存在';
    public $errorCode = 40001;
}