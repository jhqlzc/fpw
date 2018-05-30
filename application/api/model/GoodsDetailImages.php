<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/28 0028
 * Time: 11:34
 */

namespace app\api\model;


class GoodsDetailImages extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $hidden = ['create_time', 'update_time', 'img_from', 'order', 'goods_id'];

    public function getImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id');
    }

    public static function getPicInfo($id)
    {
        $pic = self::with([
            'goods' => function ($query) {
                $query->field(['id', 'user_id']);
            }
        ])->field(['goods_id', 'img_url'=>'img'])->find($id);
        return $pic;
    }
}