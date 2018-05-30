<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/5/8 0008
 * Time: 14:45
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\lib\exception\TokenException;
use think\Cache;
use think\Request;
use app\api\model\User as UserModel;

class User extends BaseController
{
    public function getUserInfo()
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (!array_key_exists('uid', $vars)) {
                unset($vars['openid']);
                return json([
                    'error_code' => 'ok',
                    'user_info' => $vars,
                ]);
            } else {
                $userInfo = UserModel::get($vars['uid']);
                return json([
                    'error_code' => 'ok',
                    'user_info' => $userInfo,
                ]);
            }
        }
    }
}