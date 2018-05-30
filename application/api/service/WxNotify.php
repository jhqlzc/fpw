<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/10 0010
 * Time: 8:45
 */

namespace app\api\service;


use app\api\model\AuctionEnroll as AuctionEnrollModel;
use app\lib\enum\PayStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{

    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];
            $attach = $data['attach'];
            if ('enroll' == $attach) {
                //支付保证金回调
                
                Db::startTrans();
                try {
                    $enrollOrder = AuctionEnrollModel::where('enroll_order_no', '=', $orderNo)->lock(true)->find();
                    if ($enrollOrder->pay_status == PayStatusEnum::UNPAYALL) {
                        AuctionEnrollModel::where('enroll_order_no', '=', $orderNo)->update(['pay_status'=>PayStatusEnum::PAYDEPOSIT, 'enroll_pay_time'=>time()]);
                    }
                    Db::commit();
                    return true;
                } catch (Exception $ex) {
                    Db::rollback();
                    Log::error($ex);
                    return false;
                }
                

            } else if ('final' == $attach) {
                //支付尾款回调
                Db::startTrans();
                try {
                    $enrollOrder = AuctionEnrollModel::where('final_order_no', '=', $orderNo)->lock(true)->find();
                    if ($enrollOrder->pay_status == PayStatusEnum::PAYONLYDEPOSIT) {
                        AuctionEnrollModel::where('final_order_no', '=', $orderNo)->update(['pay_status'=>PayStatusEnum::PAYALL, 'final_pay_time'=>time()]);
                    }
                    Db::commit();
                    return true;
                } catch (Exception $ex) {
                    Db::rollback();
                    Log::error($ex);
                    return false;
                }

            }
        } else {
            return true;
        }
    }
}