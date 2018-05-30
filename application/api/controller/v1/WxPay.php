<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/9 0009
 * Time: 14:51
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify as WxNotifyService;
use app\api\service\WxPay as WxPayService;
use app\api\validate\IDMustBePostiveInt;

class WxPay extends BaseController
{
    public function getEnrollPreOrder($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new WxPayService($id, 'enroll');
        return $pay->pay();
    }

    public function getFinalPreOrder($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new WxPayService($id, 'final');
        return $pay->pay();
    }

    public function receiveNotify()
    {
        //通知频率为15/15/30/180/1800/1800/1800/1800/3600,单位：秒
        //1. 检查库存量，超卖
        //2. 更新这个订单的status状态
        //如果成功处理，我们返回微信成功处理的信息。否则，我们需要返回没有成功处理。
        //特点：post；xml格式；不会携带参数
        $notify = new WxNotifyService();
        $notify->Handle();
    }

}