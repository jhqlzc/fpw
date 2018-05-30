<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 13:38
 */

namespace app\api\model;


class GoodsHits extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    public static function checkIp($goodsID, $ip)
    {
        $ip = self::whereTime('create_time', 'today')->where(['goods_id' => $goodsID, 'ip' => $ip])->find();
        return $ip;
    }

    public static function getClickCount($goodsID)
    {
        $goodsCount = self::where('goods_id', '=', $goodsID)->count();
        return $goodsCount;
    }

}