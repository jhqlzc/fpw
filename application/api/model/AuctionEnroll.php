<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 16:27
 */

namespace app\api\model;


use app\lib\enum\PayStatusEnum;

class AuctionEnroll extends BaseModel
{

    public static function isEnroll($uid, $goodsID)
    {
        $isEnroll = self::where(['user_id' => $uid, 'goods_id' => $goodsID, 'pay_status' => PayStatusEnum::PAYDEPOSIT])->find();
        if ($isEnroll) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function isPay($uid, $goodsID)
    {
        $payInfo = self::where(['user_id' => $uid, 'goods_id' => $goodsID])->find();
        return $payInfo;
    }

    public static function getPayStatus($uid, $goodsID)
    {
        $payStatus = self::where(['user_id' => $uid, 'goods_id' => $goodsID])->field('pay_status')->find();
        return $payStatus;
    }

    public static function getEnrollCount($goodsID)
    {
        $enrollCount = self::where(['goods_id'=>$goodsID, 'pay_status'=>PayStatusEnum::PAYDEPOSIT])->count();
        return $enrollCount;
    }

}