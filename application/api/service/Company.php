<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 17:26
 */

namespace app\api\service;


class Company
{
    public static function checkData($user){
        $arr = [];
        if($user->logo){
            $logo = explode('/',$user->logo);
            $arr['logo_name'] = $logo[2];
            $arr['logo'] = config('setting.domain').$user->logo;
        }else{
            $arr['logo'] = $user->headimgurl;
        }
        if($user->license){
            $license = explode('/',$user->license);
            $arr['license_name'] = $license[2];
            $arr['license'] = config('setting.domain').$user->license;
        }
        //规定只要填写了联系人或者手机号就查找出来所有信息
        if($user->contact || $user->phone){
            $arr['contact'] = $user->contact;
            $arr['phone'] = $user->phone;
            $arr['company_name'] = $user->company_name;
            $arr['company_desc'] = $user->company_desc;
            return $arr;
        }else if($user->logo || $user->license || $user->headimgurl){
            return $arr;
        }else{
            return $arr;
        }
    }
}