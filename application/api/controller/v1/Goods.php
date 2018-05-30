<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/26 0026
 * Time: 10:18
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\Goods as GoodsModel;
use app\api\model\GoodsHits as GoodsHitsModel;
use app\api\model\TmpPic as TmpPicModel;
use app\api\service\Goods as GoodsService;
use app\api\service\Token as TokenService;
use app\api\validate\GoodsNew;
use app\api\validate\IDMustBePostiveInt;
use app\api\validate\PagingParameter;
use app\api\validate\PictureNew;
use app\lib\exception\GoodsException;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use app\api\model\GoodsDetailImages as GoodsDetialImagesModel;
use app\api\model\GoodsMainImages as GoodsMainImagesModel;

class Goods extends BaseController
{
    public function uploadImg()
    {
        $validate = new PictureNew();
        $request = $validate->goCheck();
        //根据Token来获取uid
        $uid = TokenService::getCurrentUid();
        $pic = $request->file();

        $picObject = $pic['file'];
        $picType = $request->param('pic_type');

        //图片原始信息
        $originInfo = $picObject->getInfo();
        //验证上传文件是否为图片格式
        $goods = new GoodsService($uid);
        $result = $goods->checkIsImg($originInfo);
        if ($result) {
            $info = $picObject->rule('uniqid')->move(ROOT_PATH.'public_html'.DS.'tmp_pic');
            if ($info) {
                $dataArray = [
                    'user_id' => $uid,
                    'img_url' => $info->getSaveName(),
                    'pic_type' => $picType,
                    'origin_name' => $originInfo['name'],
                ];
                TmpPicModel::create($dataArray);
                return json(['error_code'=>'ok', 'pic'=>$info->getSaveName(), 'type'=>$picType]);
            } else {
                throw new Exception($info->getError());
            }
        } else {
            throw new ParameterException([
                'msg' => '上传文件格式有误',
            ]);
        }
    }

    public function deleteTmpPic($name = '')
    {
        if (empty(trim($name))) {
            throw new ParameterException();
        }
        $uid = TokenService::getCurrentUid();
        $goods = new GoodsService($uid);
        $goods->delTmpPic($name);
        throw new SuccessMessage();
    }


    public function addGoods()
    {
        $validate = new GoodsNew('upload');
        $request = $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $dataArray = $validate->getDataByRule($request->post());
        $goods = new GoodsService($uid);
        $goods->add($dataArray);
        throw new SuccessMessage();
    }

    public function getAllGoodsByUser($page = 1, $size = 10)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingGoods = GoodsModel::getAllByUser($uid, $page, $size);
        if ($pagingGoods->isEmpty()) {
            throw new GoodsException([
                'msg' => '商品已见底线',
                'errorCode' => 30003
            ]);
        }
        $data = $pagingGoods->visible(['id', 'start_time','end_time','goods_name', 'current_price', 'main_img', 'check_status', 'goods_status'])->toArray();
        return json([
            'error_code' => 'ok',
            'data' => $data,
            'current_page' => $pagingGoods->getCurrentPage(),
        ]);
    }

    public function searchGoodsByName($name, $page = 1, $size = 10)
    {
        $pagingGoods = GoodsModel::getGoodsByName($name, $page, $size);
        if ($pagingGoods->isEmpty()) {
            throw new GoodsException([
                'msg' => '商品已见底线',
                'errorCode' => 30003
            ]);
        }
        $data = $pagingGoods->visible(['id', 'start_time','end_time','goods_name', 'current_price', 'main_img', 'goods_status'])->toArray();
        return json([
            'error_code' => 'ok',
            'data' => $data,
            'current_page' => $pagingGoods->getCurrentPage(),
        ]);
    }

    public function getAllGoods($page = 1, $size = 10)
    {
        $pagingGoods = GoodsModel::getAll($page, $size);
        if ($pagingGoods->isEmpty()) {
            throw new GoodsException([
                'msg' => '商品已见底线',
                'errorCode' => 30003
            ]);
        }
        $data = $pagingGoods->visible(['id', 'start_time','end_time','goods_name', 'current_price', 'main_img', 'goods_status'])->toArray();
        return json([
            'error_code' => 'ok',
            'data' => $data,
            'current_page' => $pagingGoods->getCurrentPage(),
        ]);
    }

    public function getOneDetail($id = '')
    {
        $request = (new IDMustBePostiveInt())->goCheck();
        $ip = $request->ip();
        $token = $request->header('token');

        //增加浏览次数
        $hits = GoodsHitsModel::checkIp($id, $ip);
        if (!$hits) {
            GoodsHitsModel::create(['goods_id' => $id, 'ip' => $ip]);
        }

        if (!empty(trim($token))) {
            $vars = Cache::get($token);
            if(!$vars) {
                $goods = GoodsModel::getGoodsDetail($id);
            } else {
                if (!is_array($vars)) {
                    $vars = json_decode($vars, true);
                }
                if (array_key_exists('uid', $vars)) {
                    $uid = $vars['uid'];
                    $goods = GoodsModel::getGoodsDetail($id, $uid);
                } else {
                    $goods = GoodsModel::getGoodsDetail($id);
                }
            }
        } else {
            $goods = GoodsModel::getGoodsDetail($id);
        }

        if (!$goods) {
            throw new GoodsException();
        }
        return json(['error_code'=>'ok', 'goods' =>$goods]);
    }

    public function editGoods($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $this->checkGoodsValid($id);
        $goodsInfo = GoodsModel::getGoodsInfoDetail($id);
        return json(['error_code'=>'ok', 'goods' =>$goodsInfo]);
    }

    public function delPic($id, $pic_type)
    {
        (new IDMustBePostiveInt())->goCheck();
        (new PictureNew())->goCheck();
        $goodsInfo = $this->checkPicValid($id, $pic_type);
        $goods = new GoodsService($goodsInfo['uid']);
        $goods->delPic($id, $pic_type, $goodsInfo['img']);
        throw new SuccessMessage();
    }

    public function updateImg($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $validate = new PictureNew();
        $request = $validate->goCheck();
        //根据Token来获取uid
        $uid = $this->checkGoodsValid($id);
        $pic = $request->file();

        $picObject = $pic['file'];
        $picType = $request->param('pic_type');

        //图片原始信息
        $originInfo = $picObject->getInfo();
        //验证上传文件是否为图片格式
        $goods = new GoodsService($uid);
        $result = $goods->checkIsImg($originInfo);
        if ($result) {
            $goods->updateImg($id, $picObject, $picType);
            throw new SuccessMessage();
        } else {
            throw new ParameterException([
                'msg' => '上传文件格式有误',
            ]);
        }
    }

    public function updateGoodsInfo($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $validate = new GoodsNew('update');
        $request = $validate->goCheck();
        $uid = $this->checkGoodsValid($id);
        $dataArray = $validate->getDataByRule($request->post());
        GoodsModel::where(['id'=>$id, 'user_id'=>$uid])->update($dataArray);
        throw new SuccessMessage();
    }

    public function delGoods($id)
    {
        (new IDMustBePostiveInt())->goCheck();
        $uid = $this->checkGoodsValid($id);
        $goods = new GoodsService($uid);
        $picArray = $goods->getAllPic($id);
        foreach ($picArray as $item) {
            unlink(ROOT_PATH.'public_html'.$item['img']);
        }
        $goods->delGoods($id);
        throw new SuccessMessage();
    }

    public function autoDelTmpPic()
    {
        $delPic = TmpPicModel::getDelPic();
        if (!$delPic->isEmpty()) {
            $picArray = $delPic->toArray();
            $picImgUrl = [];
            foreach ($picArray as $item) {
                unlink(ROOT_PATH.'public_html'.DS.'tmp_pic'.DS.$item['img_url']);
                array_push($picImgUrl, $item['img_url']);
            }
            TmpPicModel::DelTmpPicByImgUrl($picImgUrl);
        }
    }

    public function checkGoodsValid($goodsID)
    {
        $goods = GoodsModel::get($goodsID);
        if (!$goods) {
            throw new GoodsException();
        }
        $user_id = TokenService::isValidOperate($goods->user_id);
        if (!$user_id) {
            throw new TokenException([
                'msg' => '商品与用户不匹配',
                'errorCode' => 10003,
            ]);
        } else {
            return $user_id;
        }
    }

    public function checkPicValid($picID, $type)
    {
        if ('DetailImg' == $type) {
            $pic = GoodsDetialImagesModel::getPicInfo($picID);
        } else if ('MainImg' == $type) {
            $pic = GoodsMainImagesModel::getPicInfo($picID);
        }
        if (!$pic) {
            throw new GoodsException([
                'msg' => '商品图片不存在，或已被删除',
                'errorCode' => 30002,
            ]);
        }
        $user_id = TokenService::isValidOperate($pic->goods['user_id']);
        $goodsInfo = ['img' => $pic['img'], 'uid' => $user_id];
        if (!$user_id) {
            throw new TokenException([
                'msg' => '图片与用户不匹配',
                'errorCode' => 10003,
            ]);
        } else {
            return $goodsInfo;
        }
    }
    
}