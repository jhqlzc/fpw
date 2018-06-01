<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1
 * Time: 15:28
 */

namespace app\weixin\controller;


class WeChatMenu extends WeChatAPI
{
    public function definedMenu()
    {
        $arr = $this->menuParameters();
        $access_token = $this->accessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $postArr = array(
            'button'=>array(
                array(
                    'name'=>urlencode('关于我们'),
                    'type'=>'click',
                    'key'=>$arr['item1'],
                ),
                array(
                    'name'=>urlencode('历史消息'),
                    'sub_button'=>array(
                        array(
                            'name'=>urlencode('歌曲'),
                            'type'=>'click',
                            'key'=>$arr['item2'],
                        ),
                        array(
                            'name'=>urlencode('百度'),
                            'type'=>'view',
                            'url'=>'http://www.baidu.com',
                        ),
                    ),
                ),
                array(
                    'name'=>urlencode('会员中心'),
                    'type'=>'view',
                    'url'=>'http://www.ycxfun.com'
                )
            ),
        );
        $postJson = urldecode(json_encode($postArr));
        $res = $this->httpRequest($url,$postJson);
        var_dump($res);
    }
}