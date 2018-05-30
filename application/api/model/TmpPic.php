<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/26 0026
 * Time: 13:25
 */

namespace app\api\model;


use app\lib\enum\UploadImgTypeEnum;
use think\Model;

class TmpPic extends Model
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

    public function setPicTypeAttr($value)
    {
        if ($value == 'MainImg') {
            return UploadImgTypeEnum::MainImg;
        } else if ($value == 'DetailImg') {
            return UploadImgTypeEnum::DetailImg;
        }
    }

    public static function getInfoByName($uid, $name)
    {
        $picInfo = self::where(['user_id'=>$uid, 'img_url'=>$name])->select();
        return $picInfo;
    }

    public static function DelTmpPicByImgUrl($img)
    {
        self::where('img_url', 'in', $img)->delete();
    }

    public static function getDelPic()
    {
        $delPic = self::where('create_time', '<', time()-2*24*3600)->select();
        return $delPic;
    }

}