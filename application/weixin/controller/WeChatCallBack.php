<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/3 0003
 * Time: 11:06
 */

namespace app\weixin\controller;


use think\Request;

class WeChatCallBack extends BaseController
{
    private $token;

    public function __construct()
    {
        $this->token = config('weixin.token');
    }

    public function index()
    {
        $request = Request::instance();
        $param = $request->param();
        if (!isset($param['echostr'])) {
            $callback = $this->responseMsg();
            echo $callback;
        } else {
            $echoStr = $this->valid($param);
            echo $echoStr;
        }
    }

    //验证签名
    private function valid($param)
    {
        $echoStr = $param['echostr'];
        $signature = $param['signature'];
        $timestamp = $param['timestamp'];
        $nonce = $param['nonce'];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return $echoStr;
            exit;
        }
    }


    private function responseMsg()
    {

//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $this->logger("R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case 'event':

                    break;
                case 'text':
                    $result = $this->receiveText($postObj);
                    break;
                case 'image':
                    $result = $this->receiveImage($postObj);
                    break;
                case 'location':
                    $result = $this->receiveLocation($postObj);
                    break;
                case 'voice':

                    break;
                case 'link':
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknow msg type:". $RX_TYPE;
                    break;
            }
            $this->logger("T \r\n".$result);
            return $result;
        } else {
            return '';
            exit;
        }
    }

    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        if (strstr($keyword, "文本")) {
            $content = "这是个文本消息";
        } else if (strstr($keyword, "表情")) {
            $content = "中国国旗：".$this->utf8Bytes(0x1F1E8).$this->utf8Bytes(0x1F1F3)."\n".
                "美国国旗".$this->utf8Bytes(0x1F1FA).$this->utf8Bytes(0x1F1F8)."\n".
                "男女牵手".$this->utf8Bytes(0x1F46B)."\n".
                "仙人掌".$this->utf8Bytes(0x1F335)."\n".
                "电话机".$this->utf8Bytes(0x260E)."\n".
                "药丸".$this->utf8Bytes(0x1F48A);
        } else if (strstr($keyword, "单图文")) {
            $content = array();
            $content[] = array("Title" => "", "Description" => "", "PicUrl" => "", "Url" => "");
        } else if (strstr($keyword, "图文") || strstr($keyword, "多图文")) {
            $content = array();
            $content[] = array("Title" => "", "Description" => "", "PicUrl" => "", "Url" => "");
        } else if (strstr($keyword, "音乐")) {
            $content = array();
            $content = array("Title" => "嗨骑之歌（伴奏）", 'Description' => '歌手：高颖', 'MusicUrl' => "http://www.5d1.top/music/haiqizhige.mp3", "HQMusicUrl" => 'http://www.5d1.top/music/haiqizhige.mp3');
        } else {
            $content = date("Y-m-d H:i:s",time())."\nOpenID:".$object->FromUserName."\n技术支持 糯米蛟";
        }

        if (is_array($content)) {
            if (isset($content[0])) {

            } else if (isset($content['MusicUrl'])) {
                $result = $this->transmitMusic($object, $content);
            }
        } else {
            $result = $this->transmitText($object, $content);
        }
        return $result;

    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array('MediaId'=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的位置，经度为：".$object->Location_Y."；纬度为".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)) {
            return "";
        }
        $xmlTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[text]]></MsgType>
                   <Content><![CDATA[%s]]></Content>
                   </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
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
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
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
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //添加日志
    private function logger($log_content)
    {
        $max_size = 100000;
        $log_filename = LOG_PATH. "log.xml";
        if (file_exists($log_filename) && (abs(filesize($log_filename)) > $max_size)) {
            unlink($log_filename);
        }
        file_put_contents($log_filename, date("Y-m-d H:i:s")." ".$log_content."\r\n", FILE_APPEND);
    }

    //ASCII转码， 回复表情
    private function utf8Bytes($cp)
    {
        if ($cp > 0x10000) {
            # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)).
                chr(0x80 | (($cp & 0x3F000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x800) {
            # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)).
                chr(0x80 | (($cp & 0xFC0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } else if ($cp > 0x80) {
            # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)).
                chr(0x80 | ($cp & 0x3F));
        } else {
            # 1 bytes
            return chr($cp);
        }
    }
}