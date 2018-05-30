<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/28 0028
 * Time: 11:02
 */

namespace app\api\model;

use app\api\model\AuctionEnroll as AuctionEnrollModel;
use app\api\model\GoodsHits as GoodsHitsModel;
use app\lib\enum\GoodsCheckStatusEnum;
use app\lib\enum\GoodsRecycleEnum;
use app\lib\enum\GoodsRemindStatusEnum;
use app\lib\enum\PayStatusEnum;
use app\api\model\GoodsRemind as GoodsRemindModel;

class Goods extends BaseModel
{

    protected $autoWriteTimestamp = true;

    protected $hidden = ['update_time', 'create_time', 'recycle'];

    public function getCheckStatusAttr($value)
    {
        $status = [0 => '未审核', 1=> '审核不通过', 2=> '审核通过'];
        return $status[$value];
    }

    public function getGoodsStatusAttr($value, $data)
    {
//        $status = [1 => '即将开始', 2 => '热拍中', 3 => '已经结束'];
        $timenow = time();
        if ($timenow < $data['start_time']) {
            return 1;
        } else if ($data['start_time'] < $timenow && $timenow < $data['end_time']) {
            return 2;
        } else {
            return 3;
        }
    }

    public function mainImg()
    {
        return $this->hasMany('GoodsMainImages', 'goods_id', 'id');
    }

    public function detailImg()
    {
        return $this->hasMany('GoodsDetailImages', 'goods_id', 'id');
    }

    public function goodsHits()
    {
        return $this->hasMany('GoodsHits', 'goods_id', 'id');
    }

    public function auctionEnroll()
    {
        return $this->hasMany('AuctionEnroll', 'goods_id', 'id');
    }

    public function incrementsRecord()
    {
        return $this->hasMany('IncrementsRecord', 'goods_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public static function getAllByUser($uid, $page, $size)
    {
        $pagingData = self::with(['mainImg', 'detailImg'])->where('user_id', '=', $uid)->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

    public static function getAll($page, $size)
    {
        $pagingData = self::with(['mainImg', 'detailImg'])->where(['recycle'=>GoodsRecycleEnum::UNRECYCLE,'check_status'=>GoodsCheckStatusEnum::CHECKPASS])->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

    public static function getGoodsDetail($goodsID, $uid = '')
    {
        $goods = self::with([
            'mainImg' => function($query){
                $query->field(['img_url'=>'image', 'goods_id', 'id', 'img_from', 'order', 'create_time', 'update_time']);
            }
        ])->with([
            'incrementsRecord' => function($que){
                $que->with([
                    'user' => function($qu){
                        $qu->field(['id', 'nickname', 'username']);
                    }
                ])->order('quoted_price', 'desc');
            }
        ])->with([
            'user' => function($q){
                $q->field(['id', 'mobile']);
            }
        ])->with(['detailImg'])->find($goodsID);
        $goodsDetail = $goods;


        //围观数
        $goodsCount = GoodsHitsModel::getClickCount($goodsID);
        $goodsDetail['click_count'] = $goodsCount;
        //报名数
        $goodsEnrollCount = AuctionEnrollModel::getEnrollCount($goodsID);
        $goodsDetail['enroll_count'] = $goodsEnrollCount;
        //提醒数
        $goodsRemindCount = GoodsRemindModel::getRemindCount($goodsID);
        $goodsDetail['remind_count'] = $goodsRemindCount;

        if (!empty(trim($uid))) {
            //检查支付状态
            $payStatus = AuctionEnrollModel::getPayStatus($uid, $goodsID);
            if (!$payStatus) {
                $goodsDetail['pay_status'] = PayStatusEnum::UNPAYALL;
            } else {
                $goodsdetail['pay_status'] = $payStatus;
            }
            //检查设置提醒状态
            $remindStatus = GoodsRemindModel::getRemindStatus($uid, $goodsID);
            if (!$remindStatus) {
                $goodsDetail['remind_status'] = GoodsRemindStatusEnum::UNREMIND;
            } else if ($remindStatus % 2 == 0) {
                $goodsDetail['remind_status'] = GoodsRemindStatusEnum::UNREMIND;
            } else {
                $goodsDetail['remind_status'] = GoodsRemindStatusEnum::REMIND;
            }

        } else {
            $goodsDetail['pay_status'] = PayStatusEnum::UNPAYALL;
            $goodsDetail['remind_status'] = GoodsRemindStatusEnum::UNREMIND;
        }

        return $goodsDetail;
    }

    public static function getGoodsInfoDetail($goodsID)
    {
        $goods = self::with([
            'mainImg' => function($query){
                $query->field(['id', 'img_url'=>'url', 'goods_id', 'img_from']);
            }
        ])->with([
            'detailImg' => function($que){
                $que->field(['id', 'img_url'=>'url', 'goods_id', 'img_from']);
            }
        ])->find($goodsID);
        return $goods;
    }

    public static function getGoodsInfo($id, $array = [])
    {
        $goods = self::field($array)->find($id);
        return $goods;
    }

    public static function getGoodsByName($name, $page, $size)
    {
        $pagingData = self::with(['mainImg', 'detailImg'])->where(['recycle'=>GoodsRecycleEnum::UNRECYCLE,'check_status'=>GoodsCheckStatusEnum::CHECKPASS])->where('goods_name', 'like', '%'.$name.'%')->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

}