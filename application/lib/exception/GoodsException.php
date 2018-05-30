<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/26 0026
 * Time: 13:56
 */

namespace app\lib\exception;


class GoodsException extends BaseException
{
    public $msg = '指定的商品不存在，请检查商品参数';
    public $errorCode = 30001;
}