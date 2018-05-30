<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/20 0020
 * Time: 14:20
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $msg = '参数错误';
    public $errorCode = 10000;
}