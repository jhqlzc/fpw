<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/23 0023
 * Time: 8:24
 */

namespace app\api\model;


class SmsCode extends BaseModel
{

    public function setExpireTimeAttr($value, $data)
    {
        return ($data['create_time'] + config('aliyun.sms_code_expire'));
    }

    public static function checkByMobile($mobile,$type)
    {
        $mobile_count = self::whereTime('create_time', 'today')->where(['mobile'=>$mobile, 'code_type'=>$type])->count();
        return $mobile_count;
    }

    public static function checkByIP($ip,$type)
    {
        $ip_count = self::whereTime('create_time', 'today')->where(['ip'=>$ip, 'code_type'=>$type])->count();
        return $ip_count;
    }

    public static function checkCode($mobile, $code, $type)
    {
        $codeInfo = self::where(['mobile'=>$mobile, 'code'=>$code, 'code_type'=>$type])->order('id', 'desc')->limit(1)->find();
        return $codeInfo;
    }

    public static function changeStatus($mobile, $code, $type, $timenow)
    {
        self::where(['mobile'=>$mobile, 'code'=>$code, 'code_type'=>$type])->order('id', 'desc')->limit(1)->update(['is_use'=>1, 'using_time'=>$timenow]);
    }
}