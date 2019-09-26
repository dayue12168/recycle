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
        // $pwd=$this->getPwd($data['password']);

            $tel = $data['username'];
            $result = Db::table('jh_user')->where(['tel' => $tel])->find();
            $inPWD=$this->getPwd($data['password']);
            // $inPWD=$this->getPwd(':QWER!@#$%4321');
            // return $inPWD;
            if(empty($result)){
                return ['code'=>-1,'msg'=>'用户不存在'];
            }
           
            //判断账户状态 2为锁定  
            if($result['state']  == 2){
                if((time() - strtotime($result['last_login_time'])) > 300){
                    //过了锁定时间 恢复正常状态
                    $count['state'] = 0;
                    Db::table('jh_user')->where(['tel' => $tel])->update($count);
                }else{
                    return ['code'=>-1,'msg'=>'账号或密码错误超过3次,请5分钟之后登录！'];
                }
            }
 
            if($result['psw']==$inPWD) {
                Db::table('jh_user')->where(['tel' => $tel])->update(['last_login_time' => date('Y-m-d H:i:s',time())]);
                
                return ['code' => 0, 'msg' => '登录成功!'];
            }else{
                if($result['count'] < 3){
                // 错误小于三次 字段值增加
                    $count['count'] = $result['count']+1;
                    Db::table('jh_user')->where(['tel' => $tel])->update($count);
                }else{
                    // 错误次数大于3时 属性恢复 清空 锁定
                    $count['count'] = 0;
                    $count['last_login_time'] =  date('Y-m-d H:i:s',time());
                    $count['state']  = 2;
                    Db::table('jh_user')->where(['tel' => $tel])->update($count);
                }
                return ['code'=>-1,'msg'=>'密码错误'];
            }


        // $sql="update jh_user set last_login_time=now() where state=0 and tel='".$data['username']."'";
        // $sql.=" and psw='".$pwd."'";
        // return Db::execute($sql);
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