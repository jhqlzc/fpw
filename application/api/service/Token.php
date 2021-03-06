<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 16:30
 */

namespace app\api\service;


use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken()
    {
        //32个字符组成一组随机字符串
        $randChars = self::getRandChar(32);
        //用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (!array_key_exists('uid', $vars)) {
                throw new TokenException([
                    'msg' => '微信临时账号，填写手机号码完成注册',
                    'errorCode' => 10004,
                ]);
            }
            if (array_key_exists($key, $vars)) {
                cache($token, json_encode($vars), config('secure.token_expire_in'));
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    private static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }

    public static function isValidOperate($checkUID)
    {
        if (!$checkUID) {
            throw new Exception('必须传入一个被检查的UID');
        }
        $currentOperateUid = self::getCurrentUid();
        if ($currentOperateUid == $checkUID) {
            return $currentOperateUid;
        } else {
            return false;
        }
    }
}