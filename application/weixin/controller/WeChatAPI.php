<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/25 0025
 * Time: 14:34
 */

namespace app\weixin\controller;


class WeChatAPI extends BaseController
{
    private $appID;
    private $appSecret;
    private $accessToken;

    //构造方法获取Access Token
    public function __construct()
    {
        $this->appID = config('weixin.appID');
        $this->appSecret = config('weixin.appSecret');

        $res = file_get_contents(config('weixin.accessTokenUrl'));
        $result = json_decode($res, true);
        $this->accessToken = $result['access_token'];
        $expires_time = $result['expires_time'];
        if (time() > $expires_time) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appID."&secret=".$this->appSecret;
            $res = $this->httpRequest($url);
            $result = json_decode($res, true);
            $this->accessToken = $result['access_token'];
            $expires_time = time() + 7000;
            $json = '{"access_token":"'.$this->accessToken.'", "expires_time":"'.$expires_time.'"}';
            file_put_contents(config('weixin.accessTokenUrl'), $json);
        }
    }

    public function ac()
    {
        return $this->accessToken;
    }


}