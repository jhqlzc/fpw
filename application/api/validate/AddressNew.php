<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/29 0029
 * Time: 12:48
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'district' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty',
    ];

    protected $message = [
        'name' => '联系人不能为空',
        'mobile' => '手机号不能为空',
        'province' => '地址不完整',
        'city' => '地址不完整',
        'district' => '地址不完整',
        'detail' => '地址不完整',
    ];
}