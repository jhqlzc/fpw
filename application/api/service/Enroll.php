<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/9 0009
 * Time: 10:09
 */

namespace app\api\service;

use app\api\model\AuctionEnroll;
use app\lib\enum\PayStatusEnum;
use app\lib\exception\BidException;
use app\lib\exception\GoodsException;
use app\api\model\AuctionEnroll as AuctionEnrollModel;
use app\api\model\Goods as GoodsModel;
use app\api\model\Address as AddressModel;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\api\service\Token as TokenService;
use app\api\model\IncrementsRecord as IncrementsRecordModel;

class Enroll
{
    protected $uid;
    protected $address_id;
    protected $goods_id;

    public function placeEnroll($uid, $goods_id, $address_id)
    {
        $this->address_id = $address_id;
        $this->uid = $uid;
        $this->goods_id = $goods_id;
        //检查商品时间是否已过
        $goods = $this->checkGoods('enroll');

        //检查是否交过保证金
        $status = $this->checkIsEnroll();

        if ($status['pass']) {
            //开始创建报名订单
            $enrollSnap = $this->snapEnroll($goods);
            $enroll = $this->createEnroll($enrollSnap);
        } else {
            $enroll = [
                'enroll_id' => $status['enroll_id'],
                'enroll_order_no' => $status['enroll_order_no'],
            ];
        }
        return $enroll;
    }

    public function placeFinal($uid, $goodsID)
    {
        $this->uid = $uid;
        $this->goods_id = $goodsID;
        //检查商品是否已过拍卖时间
        $this->checkGoods('final');
        //检查是否符合交尾款
        $isPayFinal = IncrementsRecordModel::checkBidValid($uid, $goodsID);
        if (!$isPayFinal) {
            throw new BidException([
                'msg' => '你没有拍到该商品，不用交尾款',
                'errorCode' => 50007,
            ]);
        }
        //检查是否已经交过尾款
        $status = $this->checkIsFinal();
        if ($status['pass']) {
            //开始创建尾款订单（更新报名订单）
            $final = $this->createFinal($isPayFinal);
        } else {
            $final = [
                'enroll_id' => $status['enroll_id'],
                'final_order_no' => $status['final_order_no'],
            ];
        }
        return $final;
    }

    private function snapEnroll($goods)
    {
        $snap = [
            'enroll_price' => '',
            'snap_name' => '',
            'snap_address' => '',
            'snap_address_mobile' => '',
            'snap_items' => '',
        ];
        $snap['enroll_price'] = $goods->deposit;
        $snap['snap_name'] = $goods->goods_name;
        $snap['snap_address'] = $this->getUserAddress()->address;
        $snap['snap_address_mobile'] = $this->getUserAddress()->mobile;
        $snap['snap_items'] = json($goods);
        return $snap;
    }

    private function createEnroll($snap)
    {
        $enrollOrderNo = $this->makeOrderNo();
        $enroll = new AuctionEnrollModel();
        $enroll->user_id = $this->uid;
        $enroll->goods_id = $this->goods_id;
        $enroll->enroll_price = $snap['enroll_price'];
        $enroll->snap_name = $snap['snap_name'];
        $enroll->snap_address = $snap['snap_address'];
        $enroll->snap_address_mobile = $snap['snap_address_mobile'];
        $enroll->snap_items = $snap['snap_items'];
        $enroll->pay_status = PayStatusEnum::UNPAYALL;
        $enroll->enroll_order_no = $enrollOrderNo;
        $enroll->save();
        $enrollID = $enroll->id;
        return [
            'enroll_id' => $enrollID,
            'enroll_order_no' => $enrollOrderNo,
        ];
    }

    private function createFinal($finalPrice)
    {
        $finalOrderNo = $this->makeOrderNo();
        $final = AuctionEnrollModel::where(['user_id'=>$this->uid, 'goods_id'=>$this->goods_id])->update(['final_price'=>$finalPrice, 'final_order_no'=>$finalOrderNo, 'pay_status'=>PayStatusEnum::PAYONLYDEPOSIT]);
        return [
            'enroll_id' => $final->id,
            'final_order_no' => $final->final_order_no,
        ];
    }

    private function checkGoods($type)
    {
        $goods = GoodsModel::get($this->goods_id);
        if (!$goods) {
            throw new GoodsException();
        }
        if ('enroll' == $type) {
            //检查商品是否已过时间
            if (time() > $goods->end_time) {
                throw new BidException([
                    'msg' => '商品拍卖时间已过',
                    'errorCode' => 50002,
                ]);
            }
        } else if ('final' == $type) {
            //检查商品是否已过时间
            if (time() < $goods->end_time) {
                throw new BidException([
                    'msg' => '商品拍卖时间还未过',
                    'errorCode' => 50008,
                ]);
            }
        }

        return $goods->hidden(['id', 'user_id', 'check_status']);
    }

    private function checkIsEnroll()
    {
        $status = [
            'pass' => true,
            'enroll_id' => '',
            'enroll_order_no' => '',
        ];
        $enroll = AuctionEnrollModel::isPay($this->uid, $this->goods_id);
        if (!$enroll) {
            $status['pass'] = true;
        } else if ($enroll->pay_status == PayStatusEnum::PAYDEPOSIT) {
            throw new BidException([
                'msg' => '已经交过保证金',
                'errorCode' => 50004,
            ]);
        } else if ($enroll->pay_status == PayStatusEnum::UNPAYALL) {
            $status['pass'] = false;
            $status['enroll_id'] = $enroll->id;
            $status['enroll_order_no'] = $enroll->enroll_order_no;
        } else if ($enroll->pay_status > PayStatusEnum::PAYDEPOSIT) {
            throw new BidException([
                'msg' => '商品拍卖时间已过',
                'errorCode' => 50002,
            ]);
        }
        return $status;
    }

    private function checkIsFinal()
    {
        $status = [
            'pass' => true,
            'enroll_id' => '',
            'final_order_no' => '',
        ];
        $final = AuctionEnrollModel::isPay($this->uid, $this->goods_id);
        if (!$final['final_price']) {
            $status['pass'] = true;
        } else if ($final->pay_status == PayStatusEnum::PAYONLYDEPOSIT) {
            $status['pass'] = false;
            $status['enroll_id'] = $final->id;
            $status['final_order_no'] = $final->final_order_no;
        } else if ($final->pay_status == PayStatusEnum::PAYALL) {
            throw new BidException([
                'msg' => '已经交过尾款',
                'errorCode' => 50009,
            ]);
        } else if ($final->pay_status < PayStatusEnum::PAYONLYDEPOSIT) {
            throw new BidException([
                'msg' => '你还没有拍到该商品，不用交尾款',
                'errorCode' => 50007,
            ]);
        }
        return $status;
    }

    private function getUserAddress()
    {
        $address = AddressModel::get($this->address_id);
        if (!$address) {
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 40002,
            ]);
        }
        $user_id = TokenService::isValidOperate($address->user_id);
        if (!$user_id) {
            throw new TokenException([
                'msg' => '地址与用户不匹配',
                'errorCode' => 10003,
            ]);
        }
        $finalAddress = $address;
        $finalAddress['address'] = $address['province'].$address['city'].$address['district'].$address['detail'];
        return $finalAddress;
    }

    private function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'E', 'F', 'H', 'K', 'M', 'N', 'R');
        $orderSn = $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}