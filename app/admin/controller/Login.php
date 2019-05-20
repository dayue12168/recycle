<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/10/30
 * Time: 9:29
 */

namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;
class Login extends Controller
{
    //登录首页
    public function index()
    {
        return $this->fetch();
    }

    //登录验证
    public function loginIn(Request $request)
    {
        $data=$request->param();
        $res=model('Login','service')->loginCheck($data);
        if($res){
            session('adminUser',$data['username']);
            return true;
        }
        return false;


    }

    //退出登录
    public function loginOut()
    {
        Session::delete('adminUser');
        $this->redirect(url("/admin/Login/index"));
    }

    //修改密码
    public function loginUpdate()
    {

    }
}