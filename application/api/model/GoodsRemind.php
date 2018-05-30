<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/24 0024
 * Time: 14:55
 */

namespace app\api\model;


use app\lib\enum\GoodsRemindStatusEnum;

class GoodsRemind extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    public static function setRemind($goodsID, $uid)
    {
        $remind = self::where(['goods_id'=>$goodsID, 'user_id'=>$uid])->find();
        if ($remind) {
            self::where(['goods_id'=>$goodsID, 'user_id'=>$uid])->setInc('remind_status');
            if ($remind->remind_status % 2 == 0) {
                return GoodsRemindStatusEnum::REMIND;
            } else {
                return GoodsRemindStatusEnum::UNREMIND;
            }
        } else {
            self::create(['goods_id'=>$goodsID, 'user_id'=>$uid, 'remind_status'=>1]);
            return GoodsRemindStatusEnum::REMIND;
        }
    }

    public static function getRemindCount($goodsID)
    {
        $remindCount = self::where(['goods_id'=>$goodsID])->where('remind_status mod 2 = 1')->count();
        return $remindCount;
    }

    public static function getRemindStatus($goodsID, $uid)
    {
        $remindStatus = self::where(['goods_id'=>$goodsID, 'user_id'=>$uid])->field('remind_status')->find();
        return $remindStatus;
    }

}