<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 15:20
 */

namespace app\api\validate;


class CompanyNew extends BaseValidate
{
    protected $rule = [
        'contact'=>'require',
        'phone'=>'require|isMobile'
    ];
    protected $message = [
        'contact'=>'联系人必须填写',
        'phone'=>'手机号必须填写'
    ];
}