<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/7 0007
 * Time: 20:33
 */

namespace app\lib\enum;


class PayStatusEnum
{
    //未支付保证金
    const UNPAYALL = 1;

    //保证金已支付
    const PAYDEPOSIT = 2;

    //保证金已退款
    const DEPOSITREFUND = 3;

    //保证金已支付，拍卖尾款未支付
    const PAYONLYDEPOSIT = 4;

    //保证金已支付，拍卖尾款已支付
    const PAYALL = 5;
}