<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 16:30
 */

namespace app\api\service;

use app\api\model\User as UserModel;
use app\lib\exception\LogAndRegException;
use app\lib\exception\TokenException;

class LoginToken extends Token
{
    public function get($mobile, $pwd, $login_or_register = 'login', $id = null)
    {

        if ('login' == $login_or_register) {
            //如果是登陆
            $phone = UserModel::checkMobile($mobile);
            if (!$phone) {
                throw new LogAndRegException([
                    'msg' => '手机号还未注册',
                    'errorCode' => 20005,
                ]);
            } else {
                $user = UserModel::checkUser($mobile, $pwd);
                if (!$user) {
                    throw new LogAndRegException([
                        'msg' => '手机号或密码不正确',
                        'errorCode' => 20003
                    ]);
                } else {
                    $uid = $user->id;
                }
            }
        } else if ('register' == $login_or_register) {
            //如果是注册
            $uid = $id;
        }
        $values = [
            'uid' => $uid,
        ];
        $token = $this->saveToCache($values);
        return $token;
    }

    private function saveToCache($values){
        $token = self::generateToken();
        $expire_in = config('secure.token_expire_in');
        $result = cache($token, json_encode($values), $expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10002
            ]);
        }
        return $token;
    }
}