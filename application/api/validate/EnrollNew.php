<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/9 0009
 * Time: 10:18
 */

namespace app\api\validate;


class EnrollNew extends BaseValidate
{
    protected $rule = [
        'goods_id' => 'require|isPostiveInteger',
        'address_id' => 'require|isPostiveInteger',
    ];

    protected $message = [
        'goods_id' => '商品id必须为整数',
        'address_id' => '地址id必须为整数',
    ];
}