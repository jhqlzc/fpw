<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 15:19
 */

namespace app\api\controller\v1;

use app\api\model\Company as CompanyModel;
use app\api\validate\CompanyImg;
use app\api\validate\CompanyNew;
use app\lib\exception\ParameterException;
use app\lib\exception\SuccessMessage;
use think\Cache;
use think\Exception;
use think\Session;
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
        //如果用户上传了图片
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public_html' . DS . 'company_pic');
            return json(['error_code'=>'ok', 'pic'=>"/company_pic/".$info->getSaveName(), 'type'=>$picType]);
            /*if($info){
                $file_url = "/company_pic/".$info->getSaveName();
                if($picType == 'LogoImg'){
                    $logo = $file_url;
                    Session::set('logo',$logo);
                    return json(['error_code'=>'ok', 'pic'=>$info->getSaveName(), 'type'=>'LogoImg']);

                }else if($picType == 'LicenseImg'){
                    $license = $file_url;
                    Session::set('license',$license);
                    return json(['error_code'=>'ok', 'pic'=>$info->getSaveName(), 'type'=>'LicenseImg']);
                }
            }else{
                throw new Exception($info->getError());
            }*/
        }
    }
    public function delImg($name = ''){
        if (empty(trim($name))) {
            throw new ParameterException();
        }
        unlink(ROOT_PATH . 'public_html' . DS . 'company_pic/'.$name);
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
        //如果上传了logo和license
        /*if(Session::has('logo')&& Session::has('license')){
            $logo = Session::pull('logo');
            $license = Session::pull('license');
            $dataArray = array_merge($dataArray, ['user_id' => $uid,'logo' => $logo,'license'=>$license]);
        }else if(Session::has('logo')){
            $logo = Session::pull('logo');
            $dataArray = array_merge($dataArray, ['user_id' => $uid,'logo' => $logo]);
        }else if(Session::has('license')){
            $license = Session::pull('license');
            $dataArray = array_merge($dataArray, ['user_id' => $uid,'license'=>$license]);
        }*/
        $company = new CompanyModel();
        $company::create($dataArray);
        throw new SuccessMessage();
    }
}