<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 17:11
 */

namespace app\api\model;


class IncrementsRecord extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    protected $hidden = ['id', 'user_id', 'goods_id'];

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public static function checkBidValid($uid, $goodsID)
    {
        $bid = self::where('goods_id', '=', $goodsID)->where('quoted_price', '=', function($query)use($uid, $goodsID) {
            $query->name('increments_record')->where(['goods_id' => $goodsID])->field('quoted_price')->order('quoted_price', 'desc')->limit(1);
        })->find();
        if(!$bid) {
            return false;
        } else if ($bid->user_id == $uid) {
            return $bid->quoted_price;
        } else {
            return false;
        }
    }
}