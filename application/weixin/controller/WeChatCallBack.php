<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/3 0003
 * Time: 11:06
 */

namespace app\weixin\controller;


use think\Request;
use app\weixin\model\WeChatCallBack as WeChatCallBackModel;

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
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $this->logger("R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case 'event':
                    $result = $this->receiveSubscribe($postObj);
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
    //回复关注后的事件
    private function receiveSubscribe($object){
        if(strtolower($object->Event == 'subscribe')){
            //实例化模型
            $content = '欢迎关注纺织网微信公众账号！';
            $indexModel = new WeChatCallBackModel;
            $indexModel->responseSubscribe($object,$content);
        }
    }
    //接收文本消息
    private function receiveText($object){
        if(trim($object->Content)=='5'){
            //从数据库中查询得到的
            $arr = array(
                array(
                    'title'=>'imooc',
                    'description'=>"imooc is very cool",
                    'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                    'url'=>'http://www.imooc.com',
                ),
                array(
                    'title'=>'hao123',
                    'description'=>"hao123 is very cool",
                    'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                    'url'=>'http://www.hao123.com',
                ),
                array(
                    'title'=>'qq',
                    'description'=>"qq is very cool",
                    'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                    'url'=>'http://www.qq.com',
                ),
            );
            //实例化模型
            $indexModel = new WeChatCallBackModel;
            $indexModel->responseNews($object,$arr);
        }else{
            switch( trim($object->Content) ){
                case 1:
                    $content = '您输入的数字是1';break;
                case 2:
                    $content = '您输入的数字是2';break;
                case 3:
                    $content = '您输入的数字是3';break;
                case 4:
                    $content = "<a href='http://www.baidu.com'>百度</a>";break;
                default:
                    $content = '输入1-5之间的数字';break;
            }
            //实例化模型
            $indexModel = new WeChatCallBackModel;
            $indexModel->responseText($object,$content);
        }
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array('MediaId'=>$object->MediaId);
        //实例化模型
        $indexModel = new WeChatCallBackModel;
        $indexModel->responseImage($object,$content);
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的位置，经度为：".$object->Location_Y."；纬度为".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        //实例化模型
        $indexModel = new WeChatCallBackModel;
        $indexModel->responseText($object,$content);
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        //实例化模型
        $indexModel = new WeChatCallBackModel;
        $indexModel->responseText($object,$content);
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