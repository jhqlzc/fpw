<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 20:41
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Token as TokenService;
use app\api\model\IncrementsRecord as IncrementsRecordModel;
use app\api\model\Goods as GoodsModel;
use app\lib\exception\BidException;
use app\lib\exception\GoodsException;
use think\Db;
use think\Exception;
use app\api\model\AuctionEnroll as AuctionEnrollModel;

class Bid extends BaseController
{
    public function bid($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        //获取商品的信息
        $goods = GoodsModel::getGoodsInfo($id, ['increments_price', 'end_time']);
        if (!$goods) {
            throw new GoodsException();
        }
        if (time() > $goods->end_time) {
            throw new BidException([
                'msg' => '商品时间已过',
                'errorCode' => 50002,
            ]);
        }
        //检查有没有报名即交保证金
        $enroll = AuctionEnrollModel::isEnroll($uid, $id);
        if (!$enroll) {
            throw new BidException([
                'msg' => '还未交保证金',
                'errorCode' => 50003,
            ]);
        }

        //检查自己是不是最高价
        $bidValid = IncrementsRecordModel::checkBidValid($uid, $id);
        if ($bidValid) {
            throw new BidException();
        }

        Db::startTrans();
        try {
            $current_price = GoodsModel::field('current_price')->lock(true)->find($id);
            $quoted_price = $current_price->current_price + $goods->increments_price;
            $dataArray = [
                'user_id' => $uid, 'goods_id' => $id, 'quoted_price' => $quoted_price,
            ];
            IncrementsRecordModel::create($dataArray);
            GoodsModel::update(['id'=>$id, 'current_price'=>$quoted_price]);
            Db::commit();
        } catch(Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }
}