<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/23 0023
 * Time: 7:53
 */

namespace app\api\validate;


class SmsCode extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isMobile',
    ];

    protected $message = [
        'mobile' => '请输入正确的手机号',
    ];
}