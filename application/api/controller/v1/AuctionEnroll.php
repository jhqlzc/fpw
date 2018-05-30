<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 18:42
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Enroll as EnrollService;
use app\api\service\Token as TokenService;
use app\api\validate\EnrollNew;
use app\api\validate\IDMustBePostiveInt;


class AuctionEnroll extends BaseController
{
    //报名
    public function enroll($goods_id = '', $address_id)
    {
        (new EnrollNew())->goCheck();
        $uid = TokenService::getCurrentUid();
        $enrollOrder = new EnrollService();
        $enroll = $enrollOrder->placeEnroll($uid, $goods_id, $address_id);
        return json([
            'error_code' => 'ok',
            'enroll' => $enroll,
        ]);
    }

    //尾款订单生成
    public function finalPrice($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $finalOrder = new EnrollService();
        $final = $finalOrder->placeFinal($uid, $id);
        return json([
            'error_code' => 'ok',
            'final' => $final,
        ]);
    }
}