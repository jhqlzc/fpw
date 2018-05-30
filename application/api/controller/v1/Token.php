<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/24 0024
 * Time: 16:18
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\LoginToken;
use app\api\service\WeiXinToken;
use app\api\validate\LoginTokenGet;
use app\weixin\controller\OAuth;
use think\Request;

class Token extends BaseController
{
    public function getLoginToken($mobile = '', $pwd = '')
    {
        (new LoginTokenGet())->goCheck();
        $log_reg = new LoginToken();
        $token = $log_reg->get($mobile, $pwd);
        return json([
            'error_code' => 'ok',
            'token'=> $token
        ]);
    }


    public function getWeiXinToken()
    {
        $oAuth = new OAuth();
        $request = Request::instance();
        $param = $request->param();
        if (!isset($param['code'])) {
            $redirect_url = config('setting.domain')."/api/".config('setting.version')."/token/weixin";
            $jumpUrl = $oAuth->oAuthAuthorize($redirect_url, "snsapi_userinfo", 'fpw');
            header("Location:$jumpUrl");
        } else {
            $accessToken = $oAuth->oAuthAccessToken($param['code']);
            if (array_key_exists('errcode', $accessToken)) {
                $url = config('setting.domain')."/api/".config('setting.version')."/token/weixin";
                header("Location:$url");
            } else {
                $userInfo = $oAuth->oAuthGetUserInfo($accessToken['access_token'], $accessToken['openid']);
                $wxt = new WeiXinToken();
                $token = $wxt->get($userInfo);
//                $vueUrl = config('setting.domain')."/#/author/".$token;
                $vueUrl = "http://192.168.1.110:8080/#/author/".$token;
                header("Location:$vueUrl");
            }
        }
    }

}