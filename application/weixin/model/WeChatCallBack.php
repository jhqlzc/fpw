<?php

namespace app\weixin\model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1
 * Time: 14:48
 */
class WeChatCallBack
{
    //回复多图文
    public function responseNews($postObj,$arr){
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".count($arr)."</ArticleCount>
					<Articles>";
        foreach($arr as $k=>$v){
            $template .="<item>
						<Title><![CDATA[".$v['title']."]]></Title>
						<Description><![CDATA[".$v['description']."]]></Description>
						<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
						<Url><![CDATA[".$v['url']."]]></Url>
						</item>";
        }
        $template .="</Articles>
					</xml> ";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
        //注意：进行多图文发送时，子图文个数不能超过10个
    }
    //回复文本消息
    public function responseText($postObj,$content){
        $template = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
        //注意模板中的中括号 不能少 也不能多
        $fromUser = $postObj->ToUserName;
        $toUser   = $postObj->FromUserName;
        $time     = time();
        $msgType  = 'text';
        echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
    }
    //回复关注
    public function responseSubscribe($postObj,$content){
        $this->responseText($postObj,$content);
    }
    //回复图片消息
    public function responseImage($postObj, $imageArray)
    {
        $itemTpl = "<Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>";
        $itemStr = sprintf($itemTpl, $imageArray['MediaId']);
        $xmlTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[image]]></MsgType>
                   $itemStr
                   </xml>";
        echo  sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time());
    }
    //回复音乐消息
    public function responseMusic($postObj, $musicArray)
    {
        if (!is_array($musicArray)) {
            return "";
        }
        $itemTpl = "<Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>";
        $itemStr = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
        $xmlTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[music]]></MsgType>
                   $itemStr
                   </xml>";
        echo sprintf($xmlTpl, $postObj->FromUserName, $postObj->ToUserName, time());
    }

}