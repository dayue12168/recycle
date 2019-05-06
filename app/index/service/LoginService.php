<?php
/**
 * Created by PhpStorm.
 * User: dayue
 * Date: 2018/11/2
 * Time: 14:54
 */

namespace app\index\service;

use lib\api_demo\SmsDemo;
use think\Controller;
use think\Db;

class LoginService extends Controller
{
    //发送手机验证码
    public function smsValidate($yzm, $tel)
    {
        if (isset($yzm)) {
            // return $tel;
            $smsDemo = new SmsDemo();
            $res = $smsDemo->sendSms($tel);
            if (isset($res)) {
                return $res;
            } else {
                return 0;
            }
        }
    }

//    校验手机号是否存在
    public function checkTel($tel)
    {
        $res = Db::table('jh_work_info')
            ->where('tel', $tel)
            ->find();
        if ($res == null) {
            $res = [
                'code' => -1,
                'url' => '/index/login/index.html',
                'msg' => '该账号不存在！'
            ];
            return $res;
        }
    }

//    校验密码
    public function doLogin($tel, $psw)
    {
        $res = Db::table('jh_work_info')
            ->where('tel', $tel)
            ->where('psw', $psw)
            ->find();
        if ($res == null) {
            $res = [
                'code' => 0,
                'url' => '/index/login/index.html',
                'msg' => '密码错误，请重新输入！'
            ];
            return $res;
        }
    }

}