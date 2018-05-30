<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/26 0026
 * Time: 14:02
 */

namespace app\api\validate;


class GoodsNew extends BaseValidate
{
    protected $rule;
    protected $message;
    protected $actionType;
    function __construct($actionType)
    {
        $this->actionType = $actionType;
        if ('upload' == $actionType) {
            $this->rule = [
                'main_img_url' => 'require|array',
                'detail_img_url' => 'require|array',
                'goods_number' => 'require|isNotEmpty',
                'goods_name' => 'require|isNotEmpty',
                'category' => 'notRequire',
                'starting_price' => 'require|number',
                'increments_price' => 'require|number',
                'deposit' => 'require|number',
                'gg' => 'require|isNotEmpty',
                'gy' => 'require|isNotEmpty',
                'cf' => 'require|isNotEmpty',
                'mf' => 'require|isNotEmpty',
                'kz' => 'require|isNotEmpty',
                'zz' => 'require|isNotEmpty',
                'jm' => 'require|isNotEmpty',
                'wm' => 'require|isNotEmpty',
                'hd' => 'notRequire',
                'tl' => 'notRequire',
                'rr' => 'notRequire',
                'tq' => 'notRequire',
                'purpose' => 'notRequire',
                'detail' => 'notRequire',
                'start_time' => 'require|isTimeStamp',
                'end_time' => 'require|isTimeStamp',
            ];
            $this->message = [
                'main_img_url.array' => '商品主图数据不符要求',
                'detail_img_url.array' => '详情图片数据不符要求',
                'goods_number' => '货号不能为空',
                'goods_name' => '品名不能为空',
                'starting_price.number' => '起拍价数值不符要求',
                'increments_price.number' => '竞价幅度数值不符要求',
                'deposit.number' => '保证金数值不符要求',
                'gg' => '规格不能为空',
                'gy' => '工艺不能为空',
                'cf' => '成分不能为空',
                'mf' => '门幅不能为空',
                'kz' => '克重不能为空',
                'zz' => '组织不能为空',
                'jm' => '经密不能为空',
                'wm' => '纬密不能为空',
                'start_time.isTimeStamp' => '开始时间数据不符要求',
                'end_time.isTimeStamp' => '结束时间数据不符要求',
            ];
        } else if ('update' == $actionType) {
            $this->rule = [
                'goods_number' => 'require|isNotEmpty',
                'goods_name' => 'require|isNotEmpty',
                'category' => 'notRequire',
                'starting_price' => 'require|number',
                'increments_price' => 'require|number',
                'deposit' => 'require|number',
                'gg' => 'require|isNotEmpty',
                'gy' => 'require|isNotEmpty',
                'cf' => 'require|isNotEmpty',
                'mf' => 'require|isNotEmpty',
                'kz' => 'require|isNotEmpty',
                'zz' => 'require|isNotEmpty',
                'jm' => 'require|isNotEmpty',
                'wm' => 'require|isNotEmpty',
                'hd' => 'notRequire',
                'tl' => 'notRequire',
                'rr' => 'notRequire',
                'tq' => 'notRequire',
                'purpose' => 'notRequire',
                'detail' => 'notRequire',
                'start_time' => 'require|isTimeStamp',
                'end_time' => 'require|isTimeStamp',
            ];
            $this->message = [
                'goods_number' => '货号不能为空',
                'goods_name' => '品名不能为空',
                'starting_price.number' => '起拍价数值不符要求',
                'increments_price.number' => '竞价幅度数值不符要求',
                'deposit.number' => '保证金数值不符要求',
                'gg' => '规格不能为空',
                'gy' => '工艺不能为空',
                'cf' => '成分不能为空',
                'mf' => '门幅不能为空',
                'kz' => '克重不能为空',
                'zz' => '组织不能为空',
                'jm' => '经密不能为空',
                'wm' => '纬密不能为空',
                'start_time.isTimeStamp' => '开始时间数据不符要求',
                'end_time.isTimeStamp' => '结束时间数据不符要求',
            ];
        }
    }

    public function isTimeStamp($value)
    {
        if (strtotime(date('Y-m-d H:i:s', $value)) == $value) {
            return true;
        } else {
            return false;
        }
    }

    public function notRequire($value)
    {
        return true;
    }
}