<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/1
 * Time: 15:51
 */
namespace app\admin\service;

use think\Db;

class Login
{
    protected $key='recycleTest';

    //登录校验
    public function loginCheck($data)
    {
        $pwd=$this->getPwd($data['password']);
        $sql="update jh_user set last_login_time=now() where state=0 and tel='".$data['username']."'";
        $sql.=" and psw='".$pwd."'";
        return Db::execute($sql);
    }

    //默认密码为123456
    public function getPwd($pwd='123456')
    {
        return md5($pwd);
    }

    //修改密码
    public function updatePwd()
    {

    }

}