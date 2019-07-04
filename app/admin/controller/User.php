<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/7
 * Time: 20:27
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\JhLog;
use think\Request;
use think\response\Json;
use app\admin\model\JhUser;
use app\admin\model\JhUserRole;
use app\admin\model\JhArea;
use app\admin\model\JhWorkInfo;

class User extends Base
{
    //获取权限列表
    public function getAuthList(Request $request)
    {
        $name=$request->param('user');
        //获取其角色以及对应的权限列表
        $role=model('User','service')->getRole($name);
        $auth=$role['role_auth'];
        if($auth=="*"){//超级管理员，拥有全部权限
            $authList=model('User','service')->getAuth();
        }else{
            $authList=model('User','service')->getAuth($auth);
        }
        return json($authList);
    }

    //角色管理
    public function role_mana()
    {
        //查询出所有非超级管理员角色
        $roles=model('User','service')->allRoles();
        $this->assign('roles',$roles);
        //查询出所有权限列表
        $auths=model('user','service')->allAuths();
        $this->assign('auths',$auths);
        return $this->fetch();
    }


    //系统管理员管理
    public function manager_mana()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $road=$roads[0]['area_id'];
        $groups=model('Address','service')->getChildAddr($road);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);

        //查询出所有系统管理员信息
        $users=model('User','service')->getAdmin($road);
//        return $users;
        $this->assign('users',$users);
        //查询出所有非超级管理员角色
        $roles=model('User','service')->allRoles();
        $this->assign('roles',$roles);
        return $this->fetch();
    }


    //环卫工管理
    public function sanitation()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $road=$roads[0]['area_id'];
        $groups=model('Address','service')->getChildAddr($road);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);

        $works=model('User','service')->getWorks($road);
        $this->assign('works',$works);
        return $this->fetch();
    }

    //添加角色
    public function addRole(Request $request)
    {
        $role_name=$request->param('name');
        //先查询后插入
        $jhrObj=new JhUserRole();
        $name= $jhrObj::get(['role_name' => $role_name]);
        if(is_null($name)){
            $jhrObj->role_name=$role_name;
            $jhrObj->save();
            $res['state']=$jhrObj->role_id;
            if($res['state']){
                $res['msg']='角色创建成功';
            };
        }else{
            $res=['state'=>0,'msg'=>'角色已存在'];
        }
        $res['name']=$role_name;
        return json($res);
    }

    //删除角色
    public function delRole(Request $request)
    {
        $role_id=$request->param('id');
        $jhUser=new JhUser();
        $exit=$jhUser::where('role_id',$role_id)->value('user_id');
        if($exit){
            return false;
        }
        $jhrObj=new JhUserRole();
        $jhrObj=$jhrObj::get($role_id);
        return $jhrObj->delete();
    }

    //根据角色id获取权限列表
    public function getAuthById(Request $request)
    {
        $role=$request->param('role');
        $jhUserRole=new JhUserRole();
        $jhUserRole=$jhUserRole->get($role);
        $res=explode(',',$jhUserRole->role_auth);
        return json($res);
    }

    //根据角色id设置权限
    public function updateRole(Request $request)
    {
        $id=$request->param('id');
        $auths=$request->param('auths');
        //$auths只获取到子权限，用子权限去找出父权限，加入权限列表
        $res=model('User','service')->updateRole($id,$auths);
        return $res;
    }

    //添加管理员
    public function addAdmin(Request $request)
    {
//        return json($request->param());
        $param['tel']=$request->param('tel');
        $param['user_name']=$request->param('name');
        $pwd=$request->param('pwd');
        $param['psw']=model('Login','service')->getPwd($pwd);
        $param['user_type']=intval($request->param('type'));
        $param['role_id']=intval($request->param('role'));
        $param['area_id0']=intval($request->param('city_g'));
        $param['area_id1']=intval($request->param('area_g'));
        $param['area_id2']=intval($request->param('street_g'));
        $param['area_id3']=intval($request->param('group_g'));
        $wx=$request->param('wx');
        $param['wx_band']=empty($wx)?0:$wx;
        $param['last_login_time']='0000-00-00 00:00:00';
        $jhUser=new JhUser();
        $jhUser->save($param);
        $param['user_id']=$jhUser->user_id;
        $jhArea=new JhArea();
        $param['area_id0']=$jhArea->where('area_id',$param['area_id0'])->value('area_name');
        $param['area_id1']=$jhArea->where('area_id',$param['area_id1'])->value('area_name');
        $param['area_id2']=$jhArea->where('area_id',$param['area_id2'])->value('area_name');
        $param['area_id3']=$jhArea->where('area_id',$param['area_id3'])->value('area_name');
        $param['user_type']=$param['user_type']==0?'系统管理员':'商户管理员';
        $jhUserRole=new JhUserRole();
        $param['role_id']=$jhUserRole->where('role_id',$param['role_id'])->value('role_name');
        unset($param['psw']);
        return json($param);
    }

    //修改管理员信息
    public function updateAdmin(Request $request)
    {
        $where['user_id']=$request->param('Jid');
        $post['user_name']=$request->param('Jname');
        $post['tel']=$request->param('Jtel');
        $post['user_type']=$request->param('JuserType');
        $post['area_id0']=$request->param('city_g');
        $post['area_id1']=$request->param('area_g');
        $post['area_id2']=$request->param('street_g');
        $post['area_id3']=$request->param('group_g');
        $post['role_id']=$request->param('role');
        $jhUser=new JhUser();
        $jhUser->save($post,$where);
        $jhArea=new JhArea();
        $post['area_id0']=$jhArea->where('area_id',$post['area_id0'])->value('area_name');
        $post['area_id1']=$jhArea->where('area_id',$post['area_id1'])->value('area_name');
        $post['area_id2']=$jhArea->where('area_id',$post['area_id2'])->value('area_name');
        $post['area_id3']=$jhArea->where('area_id',$post['area_id3'])->value('area_name');
        $post['user_type']=$post['user_type']==0?'系统管理员':'商户管理员';
        $jhUserRole=new JhUserRole();
        $post['role_id']=$jhUserRole->where('role_id',$post['role_id'])->value('role_name');
        $post['user_id']=$where['user_id'];
        return json($post);
    }

    //禁用/启用管理员
    public function setToggle(Request $request)
    {
        $where['user_id']=$request->param('id');
        $data['state']=$request->param('state');
        $jhUser=new JhUser();
        $jhUser->save($data,$where);
        return true;
//        $data['user_id']=$where['user_id'];
//        return json($data);
    }


    //查询管理员
    public function getAdmin(Request $request)
    {
        $type=$request->param('type');
        $addr=$request->param('addr');
        $res=model('User','service')->getAdmin($addr,$type);
        return json($res);
    }

    //查询环卫工
    public function getWorks(Request $request)
    {
        $addr=$request->param('addr');
        $res=model('User','service')->getWorks($addr);
        return json($res);
    }

    //新增环卫工
    public function addWorker(Request $request)
    {
        $data['worker_name']=$request->param('name');
        $data['tel']=$request->param('tel');
        $data['psw']=$this->signPwd($request->param('pwd'));
        $data['area_id0']=$request->param('city_g');
        $data['area_id1']=$request->param('area_g');
        $data['area_id2']=$request->param('street_g');
        $data['area_id3']=$request->param('group_g');
        $jhWorkInfo=new JhWorkInfo();
        $jhWorkInfo->save($data);
        $id=$jhWorkInfo->worker_id;
        $where='jwi.worker_id='.$id;
        $res=model('User','service')->getWorkSql($where);
        return json($res);
    }

    //修改环卫工信息
    public function updateWorker(Request $request)
    {
        $where['worker_id']=$request->param('id');
        $data['worker_name']=$request->param('name');
        $data['tel']=$request->param('tel');
        $pwd=$request->param('pwd');
        if($pwd){
            $data['psw']=$this->signPwd($pwd);
        }
        $data['area_id0']=$request->param('city_g');
        $data['area_id1']=$request->param('area_g');
        $data['area_id2']=$request->param('street_g');
        $data['area_id3']=$request->param('group_g');
        $jhWorkInfo=new JhWorkInfo();
        $jhWorkInfo->save($data,$where);
        $jhArea=new JhArea();
        $data['area_id0']=$jhArea->where('area_id',$data['area_id0'])->value('area_name');
        $data['area_id1']=$jhArea->where('area_id',$data['area_id1'])->value('area_name');
        $data['area_id2']=$jhArea->where('area_id',$data['area_id2'])->value('area_name');
        $data['area_id3']=$jhArea->where('area_id',$data['area_id3'])->value('area_name');
        return json($data);
    }



    //禁启用环卫工
    public function huanToggle(Request $request)
    {
        $where['worker_id']=$request->param('id');
        $data['state']=$request->param('state');
        $jhUser=new JhWorkInfo();
        $jhUser->save($data,$where);
        return true;
    }

    //环卫工密码加密
    protected function signPwd($pwd)
    {
        return md5($pwd);
    }


    public function waring()
    {
        return $this->fetch();
    }

}