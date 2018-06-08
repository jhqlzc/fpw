<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 15:19
 */

namespace app\api\controller\v1;


use app\api\validate\CompanyImg;
use app\api\validate\CompanyNew;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use think\Cache;
use think\Exception;
use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;

class Company
{
    public function uploadImg(){
        $validate = new CompanyImg();
        $request = $validate->goCheck();
        //根据token获取用户信息
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        //验证图片的格式
        $picType = $request->param('pic_type');
        $file = request()->file('file');
        //如果用户上传了图片，将图片存放到目录中。
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public_html' . DS . 'company_pic');
            //将数据插入到数据库中
            $pic = "/company_pic/".$info->getSaveName();
            $user->$picType = $pic;
            $user->is_company = 1;
            $user->save();
            return json(['error_code'=>'ok', 'pic'=>$info->getSaveName(), 'type'=>$picType]);
        }
    }
    public function delImg($name = '',$type=''){
        if (empty(trim($name))) {
            throw new ParameterException();
        }
        unlink(ROOT_PATH . 'public_html' . DS . 'company_pic/'.$name);
        //删除数据库中的单个数据,相当于更新改字段为空
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        $user->$type = '';
        $user->save();
        throw new SuccessMessage();
    }
    public function addCompany(){
        $validate = new CompanyNew();
        $request = $validate->goCheck();
        //根据token获取用户信息
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        $dataArray = $validate->getDataByRule($request->post());
        $user->isUpdate()->save($dataArray);
        throw new SuccessMessage();
    }
    public function showCompany(){
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        //如果用户填写了公司详情，就返给信息
        $logo = explode('/',$user->logo);
        $license = explode('/',$user->license);
        $arr = array(
            'logo'=>config('setting.domain').$user->logo,
            'license'=>config('setting.domain').$user->license,
            'contact'=>$user->contact,
            'phone'=>$user->phone,
            'company_name'=>$user->company_name,
            'company_desc'=>$user->company_desc,
            'logo_name'=>$logo[2],
            'license_name'=>$license[2]
        );
        //规定只要填写了联系人或者手机号就查找出来所有信息
        if($user->contact || $user->phone){
            return json(['error_code'=>'ok','data'=>$arr]);
        }else{
            return json(['error_code'=>'error','data'=>'你还没有填写公司信息呢']);
        }
    }
}