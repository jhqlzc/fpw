<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/20 0020
 * Time: 13:15
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\RegisterNew;
use app\api\model\User as UserModel;
use app\api\validate\WeiXinTokenGet;
use app\lib\enum\SmsCodeTypeEnum;
use app\lib\exception\LogAndRegException;
use app\api\model\SmsCode as SmsCodeModel;
use app\api\service\LoginToken;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use think\Cache;

class Register extends BaseController
{
    public function register($mobile = '', $pwd = '', $code = '')
    {
        //注册流程
        //1. 判断手机号码是否已被注册
        //2.判断验证码是否正确
        //3.账号密码加密新增数据库
        //4.注册成功，即生成token返回给客户端，保存登陆状态
        (new RegisterNew())->goCheck();
        //检查手机号码是否被注册
        $user = UserModel::checkMobile($mobile);
        if ($user) {
            throw new LogAndRegException();
        }
        //检查验证码是否正确
        $codeInfo = SmsCodeModel::checkCode($mobile, $code, SmsCodeTypeEnum::ToRegister);
        if (!$codeInfo || $codeInfo['code'] != $code || $codeInfo['expire_time'] < time() || 1 == $codeInfo['is_use']) {
            throw new LogAndRegException([
                'msg' => '验证码不匹配或已过期',
                'errorCode' => 20002,
            ]);
        } else {
            $timenow = time();
            //修改验证码使用状态
            SmsCodeModel::changeStatus($mobile, $code, SmsCodeTypeEnum::ToRegister, $timenow);
            //新增用户数据库
            $dataArray = [
                'mobile' => $mobile, 'password' => md5($pwd), 'last_login' => $timenow,
                'username' => "fpw_".self::getRandChar(6), 'uploadimgurl' => '/images/moren.jpg',
            ];
            $user = UserModel::create($dataArray);
            if ($user->id) {
                $reg = new LoginToken();
                $token = $reg->get($mobile, $pwd, 'register', $user->id);
                return json([
                    'error_code' => 'ok',
                    'token' => $token,
                ]);
            }
        }
    }

    public function wxRegister($mobile = '', $pwd = '', $code = '')
    {
        $request = (new WeiXinTokenGet())->goCheck('wx_register');
        (new RegisterNew())->goCheck();
        $token = $request->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        }
        if (!is_array($vars)) {
            $vars = json_decode($vars, true);
        }
        //检查验证码是否正确
        $codeInfo = SmsCodeModel::checkCode($mobile, $code, SmsCodeTypeEnum::ToRegister);
        if (!$codeInfo || $codeInfo['code'] != $code || $codeInfo['expire_time'] < time() || 1 == $codeInfo['is_use']) {
            throw new LogAndRegException([
                'msg' => '验证码不匹配或已过期',
                'errorCode' => 20002,
            ]);
        }
        $cachedValue = $vars;
        //检查手机号状态
        $user = UserModel::checkMobile($mobile);
        if ($user) {
            if (!empty($user->openid)) {
                throw new LogAndRegException([
                    'msg' => '该手机号已绑定微信号',
                    'errorCode' => 20006,
                ]);
            } else {
                $cachedValue['uid'] = $user->id;
                $timenow = time();
                //修改验证码使用状态
                SmsCodeModel::changeStatus($mobile, $code, SmsCodeTypeEnum::ToRegister, $timenow);
                $dataArray = [
                    'nickname' => $vars['nickname'], 'sex' => $vars['sex'], 'province' => $vars['province'], 'city' => $vars['city'],
                    'country' => $vars['country'], 'headimgurl' => $vars['headimgurl'], 'openid' => $vars['openid'], 'password' => md5($pwd),
                ];
                $user->save($dataArray);
                cache($token, json_encode($cachedValue), config('secure.token_expire_in'));
                throw new SuccessMessage();
            }
        } else {
            $timenow = time();
            //修改验证码使用状态
            SmsCodeModel::changeStatus($mobile, $code, SmsCodeTypeEnum::ToRegister, $timenow);
            $dataArray = [
                'mobile' => $mobile, 'password' => md5($pwd), 'last_login' => $timenow, 'openid' => $vars['openid'],
                'nickname' => $vars['nickname'], 'sex' => $vars['sex'], 'province' => $vars['province'], 'city' => $vars['city'],
                'country' => $vars['country'], 'headimgurl' => $vars['headimgurl'],
            ];
            $user = UserModel::create($dataArray);
            if ($user->id) {
                $cachedValue['uid'] = $user->id;
                cache($token, json_encode($cachedValue), config('secure.token_expire_in'));
                throw new SuccessMessage();
            }
        }
    }

    private static function getRandChar($length)
    {
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }
}