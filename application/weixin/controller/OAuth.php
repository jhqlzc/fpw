<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/3 0003
 * Time: 15:58
 */

namespace app\weixin\controller;


class OAuth extends BaseController
{
    private $appID;
    private $appSecret;

    public function __construct()
    {
        $this->appID = config('weixin.appID');
        $this->appSecret = config('weixin.appSecret');
    }

    //生成OAuth2.0的URL
    public function oAuthAuthorize($redirectUrl, $scope, $state = NULL)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appID."&redirect_uri=".$redirectUrl."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        return $url;
    }

    public function oAuthAccessToken($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appID."&secret=".$this->appSecret."&code=".$code."&grant_type=authorization_code";
        $accessToken = $this->httpRequest($url);
        return json_decode($accessToken, true);
    }

    public function oAuthGetUserInfo($accessToken, $openID)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$accessToken."&openid=".$openID."&lang=zh_CN";
        $userInfo = $this->httpRequest($url);
        return $userInfo;
    }

}