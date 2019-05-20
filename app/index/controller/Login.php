<?php
/**
 * Created by PhpStorm.
 * User: dayue
 * Date: 2018/11/2
 * Time: 13:30
 */

namespace app\index\controller;


use think\Controller;
use think\Loader;
use think\Session;

class Login extends Controller
{
    public function _initialize()
    {
        $tel = session('tel');
        if ($tel != null) {
            $this->redirect('Index/index', '已登录');
        }
    }

    public function index()
    {
        return $this->fetch();

    }

    public function doLogin()
    {
        if (request()->isAjax()) {
            $params = request()->post();
            $login_type = $params['login_type'];
            $tel = $params['tel'];
            //校驗手機號是否存在
            $checkTel = model('LoginService', 'service')->checkTel($tel);
            if ($checkTel['code'] == -1) {
                return json_encode($checkTel);
            }
            if ($login_type == 'pwd') {
                $res = model('LoginService', 'service')->doLogin($tel, $params['psw']);
                if (isset($res)) {
                    return json_encode($res);
                }
            }
            $res = json_encode(
                [
                    'code' => 1,
                    'url' => '/index/index/index.html',
                    'msg' => '登录成功！'
                ]
            );
            //登陆成功之后存session
            Session::set('tel', $tel);
            return $res;
        }
    }

//    驗證碼
    public function smsValidate()
    {
        if (request()->isAjax()) {
            $yzm = input('param.yzm');
            $tel = input('param.tel');
            return model('LoginService', 'service')->smsValidate($yzm, $tel);
        }
    }

    public function test()
    {
        $data = [
            'name' => 'thinkphp',
            'email' => 'thinkphp@qq.com'
        ];

        $validate = Loader::validate('User');

        if (!$validate->check($data)) {
            dump($validate->getError());
        } else {
            echo '123';
        }
    }
}