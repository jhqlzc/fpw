<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 16:20
 */

namespace app\api\validate;


class LoginTokenGet extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isMobile',
        'pwd' => 'require|isNotEmpty'
    ];

    protected $message = [
        'mobile' => '请填写正确的手机号',
        'pwd' => '密码不能为空',
    ];
}