<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/20 0020
 * Time: 13:15
 */

namespace app\api\model;


class User extends BaseModel
{

    protected $autoWriteTimestamp = true;

    public function getUploadimgurlAttr($value, $data)
    {
        return config('setting.domain').$value;
    }

    public function address()
    {
        return $this->hasMany('Address', 'user_id', 'id');
    }

    public static function checkMobile($mobile)
    {
        $user = self::where('mobile', '=', $mobile)->find();
        return $user;
    }

    public static function checkUser($mobile, $pwd)
    {
        $user = self::where(['mobile'=>$mobile, 'password'=>md5($pwd)])->find();
        return $user;
    }

}