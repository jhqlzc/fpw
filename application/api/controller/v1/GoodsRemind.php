<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/24 0024
 * Time: 14:52
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Token as TokenService;
use app\api\model\GoodsRemind as GoodsRemindModel;

class GoodsRemind extends BaseController
{
    public function setRemind($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $uid = TokenService::getCurrentUid();
        $remind = GoodsRemindModel::setRemind($id, $uid);
        return json(['error_code'=>'ok', 'remind'=>$remind]);
    }
}