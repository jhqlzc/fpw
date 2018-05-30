<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/4 0004
 * Time: 17:26
 */

namespace app\api\service;


use app\lib\exception\TokenException;
use think\Exception;
use app\api\model\User as UserModel;

class WeiXinToken extends Token
{
    public function get($userInfo)
    {
        $userInfo = json_decode($userInfo, true);
        if (empty($userInfo)) {
            throw new Exception('获取session_key即openID时异常，微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $userInfo);
            if ($loginFail) {
                throw new Exception($userInfo['errmsg']);
            } else {
                return $this->grantToken($userInfo);
            }
        }
    }

    private function grantToken($userInfo)
    {
        $openID = $userInfo['openid'];
        $user = UserModel::getByOpenid($openID);
        if ($user) {
            $uid = $user->id;
            $cachedValue = $this->prepareCachedValue($userInfo, $uid);
        } else {
            $cachedValue = $this->prepareCachedValue($userInfo);
        }
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function prepareCachedValue($userInfo, $uid = '')
    {
        $cachedValue = $userInfo;
        if (!empty(trim($uid))) {
            $cachedValue['uid'] = $uid;
        }
        return $cachedValue;
    }

    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('secure.token_expire_in');

        $request = cache($key, $value, $expire_in);
        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10002
            ]);
        }
        return $key;
    }

}