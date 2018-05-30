<?php
/**
 * Created by Sweet Jiao
 * User: Sweet Jiao
 * Date: 2018/4/22 0022
 * Time: 11:20
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\SmsCode as SmsCodeModel;
use app\api\service\SendSms as SendSmsService;
use app\api\validate\SmsCode;
use app\lib\enum\SmsCodeTypeEnum;
use app\lib\exception\LogAndRegException;
use app\lib\exception\SuccessMessage;
use think\Exception;

class Sms extends BaseController
{
    public function registerSms()
    {
        $request = (new SmsCode())->goCheck();
        $mobile = $request->param('mobile');
        $ip = $request->ip();
        $mobile_count = SmsCodeModel::checkByMobile($mobile, SmsCodeTypeEnum::ToRegister);
        $ip_count = SmsCodeModel::checkByIP($mobile, SmsCodeTypeEnum::ToRegister);
        if ($mobile_count >= config('aliyun.sms_mobile_limit') || $ip_count >= config('aliyun.sms_ip_limit')) {
            throw new LogAndRegException([
                'msg' => '发送次数过多，请稍后再试',
                'errorCode' => 20004,
            ]);
        } else {
            $code = $this->randomKeys(config('aliyun.sms_KL'));
            $sendSms = new SendSmsService($mobile, $code, config('aliyun.sms_TC1'));
            //返回stdClass
            $acsResponse = $sendSms->sendSms();
            if ('OK' == $acsResponse->Code) {
                $dataArray = [
                    'mobile' => $mobile, 'code' => $code, 'ip' => $ip,
                    'is_use' => 0, 'code_type' => SmsCodeTypeEnum::ToRegister, 'create_time' => time(),
                    'expire_time' => '',
                ];
                SmsCodeModel::create($dataArray);
                throw new SuccessMessage();
            } else {
                throw new Exception($acsResponse->Message);
            }
        }
    }

    private function randomKeys($length)
    {
        $key='';
        $pattern='1234567890';
        for($i=0;$i<$length;++$i)
        {
            $key .= $pattern{mt_rand(0,9)}; // 生成php随机数
        }
        return $key;
    }
}