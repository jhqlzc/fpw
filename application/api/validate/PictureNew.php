<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/26 0026
 * Time: 10:54
 */

namespace app\api\validate;


class PictureNew extends BaseValidate
{
    protected $rule = [
//        'goods_pic' => 'require|image|fileExt:jpg,png,gif|fileMime:image/jpeg,image/png,image/gif',
        'pic_type' => 'require|in:MainImg,DetailImg',
    ];
    protected $message = [
//        'goods_pic.require' => '图像文件不能为空',
//        'goods_pic.image' => '请上传图像文件',
//        'goods_pic.fileExt' => '上传文件类型不合法',
//        'goods_pic.fileMime' => '上传文件的Mine类型不合法',
    ];
}