<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/29 0029
 * Time: 11:37
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use app\api\model\Address as AddressModel;

class Address extends BaseController
{
    public function createAddress()
    {
        $validate = new AddressNew();
        $request = $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        $dataArray = $validate->getDataByRule($request->post());
        $user->address()->save($dataArray);
        throw new SuccessMessage();
    }

    public function deleteAddress($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $this->checkAddressValid($id);
        AddressModel::destroy($id);
        throw new SuccessMessage();
    }


    public function setDefault($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $uid = $this->checkAddressValid($id);
        AddressModel::selectDefaultById($uid, $id);
        throw new SuccessMessage();
    }

    public function getAllAddressByUser()
    {
        $uid = TokenService::getCurrentUid();
        $allAddress = AddressModel::getAll($uid);
        if ($allAddress->isEmpty()) {
            throw new UserException([
                'msg' => '用户还未添加地址',
                'errorCode' => 40003
            ]);
        }
        return json(['error_code'=>'ok', 'allAddress'=>$allAddress]);
    }

    private function checkAddressValid($id)
    {
        $address = AddressModel::get($id);
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
        } else {
            return $user_id;
        }
    }
}