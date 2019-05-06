<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/10/30
 * Time: 9:37
 */

namespace app\admin\controller;
use think\Controller;

class Base extends Controller
{
    //未登录状况下跳到登录界面
    public function _initialize()
    {
        $user=session('adminUser');
        if(!$user){
            $this->redirect(url("/admin/Login/index"));
        }
    }
}