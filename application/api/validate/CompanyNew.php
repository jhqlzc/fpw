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
        'phone'=>'require',
        'company_name' => 'require|isNotEmpty',
        'company_desc'=>'require|isNotEmpty'
    ];
    protected $message = [
        'contact'=>'联系人必须填写',
        'phone'=>'手机号必须填写',
        'company_name'=>'公司名称必须填写',
        'company_desc'=>'公司简介必须填写'
    ];
}