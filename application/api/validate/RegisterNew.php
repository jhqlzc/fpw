<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/20 0020
 * Time: 13:17
 */

namespace app\api\validate;


class RegisterNew extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isMobile',
        'pwd' => 'require|isNotEmpty',
        'code' => 'require|isCode',
    ];

    protected $message = [
        'mobile' => '请输入正确的手机号',
        'pwd' => '密码不能为空',
        'code' => '验证码为6位数字',
    ];

    public function isCode($value)
    {
        $rule = '/^\d{'.config('aliyun.sms_KL').'}$/';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}