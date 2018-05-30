<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/8 0008
 * Time: 10:19
 */

namespace app\lib\exception;


class BidException extends BaseException
{
    public $msg = '当前已经是最高价';
    public $errorCode = 50001;
}