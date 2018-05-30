<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/4 0004
 * Time: 17:21
 */

namespace app\api\validate;


class WeiXinTokenGet extends BaseValidate
{
    protected $rule = [
        'token' => 'require|isNotEmpty'
    ];

    protected $message = [
        'token' => '没有token怎么知道你的注册信息呢！'
    ];
}