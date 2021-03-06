<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/7
 * Time: 8:56
 */

namespace app\api\validate;


class CompanyImg extends BaseValidate
{
    protected $rule = [
        'pic_type' => 'require|in:logo,license',
    ];
    protected $message = [
        'pic_type' => 'pic_type值必须是logo或license',
    ];
}