<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 10:24
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $msg = 'OK';
    public $errorCode = 'ok';
}