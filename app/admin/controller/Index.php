<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/10/30
 * Time: 9:48
 */

namespace app\admin\controller;
use app\admin\controller\Base;
use think\Session;
use think\Request;
use app\admin\model\JhCap;
use app\admin\model\JhDustbinInfo;
use app\admin\model\JhArea;

class Index extends Base
{
    //首页
    public function index()
    {
        //获取其角色以及对应的权限列表
        $role=model('User','service')->getRole(session('adminUser'));
        session('adminRole',$role['role_name']);
        $citys=model('Address','service')->getCitys();
        $this->assign('citys',$citys);
        return $this->fetch();
    }
    //欢迎页
    public function index2()
    {
        return $this->fetch();
    }

    //区-街道-班组管理
    public function qu_street()
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
        return $this->fetch();
    }

    //设备管理
    public function device_mana()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $caps=model('Index','service')->device_mana();
        $types=model('Index','service')->getTypes();
        $this->assign('types',$types);
        $this->assign('caps',$caps);
        return $this->fetch();
    }

    //垃圾桶管理
    public function trash_mana()
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
        //垃圾桶信息
        $info=model('Index','service')->getDustbinInfo();
        $this->assign('info',$info);
        return $this->fetch();
    }


    //修改设备信息
    public function updateDevice(Request $request)
    {
        $where['cap_id']=$request->param('id');
        $param['cap_imsi']=$request->param('imsi');
        $param['cap_imei']=$request->param('imei');
        $param['cap_type']=$request->param('type');
        $param['cap_serial']=$request->param('serial');
        $param['cap_sim']=$request->param('sim');
        $param['cap_position']=$request->param('position');
        $jhCap=new JhCap();
        $jhCap->save($param,$where);
        return json($param);
    }

    //添加设备
    public function addDevice(Request $request)
    {
        $param['cap_imei']=$request->param('imei');
        $param['cap_imsi']=$request->param('imsi');
        $param['cap_position']=$request->param('position');
        $param['cap_serial']=$request->param('serial');
        $param['cap_sim']=$request->param('sim');
        $param['cap_type']=intval($request->param('type'));
//        return json($param);
        $jhCap=new JhCap($param);
        $jhCap->save();
        $param['cap_id']=$jhCap->cap_id;
        return json($param);
    }

    //禁用/启用设备
    public function setToggle(Request $request)
    {
        $where['cap_id']=$request->param('id');
        $data['cap_status']=$request->param('status');
        $jhCap=new JhCap();
        $jhCap->save($data,$where);
        $data['id']=$where['cap_id'];
        return json($data);

    }

    //添加垃圾桶
    public function addTrash(Request $request)
    {
        $data['area_id0']=$request->param('city_g');
        $data['area_id1']=$request->param('area_g');
        $data['area_id2']=$request->param('street_g');
        $data['dust_serial']=$request->param('Jserial');
        $data['dust_address']=$request->param('Jaddress');
        $data['longitude']=$request->param('Jlongitude');
        $data['latitude']=$request->param('Jlatitude');
        $data['gps_gd']=$request->param('Jgps_gd');
        $data['dust_length']=$request->param('Jlength');
        $data['dust_width']=$request->param('Jwidth');
        $data['dust_height']=$request->param('Jheight');
        $data['install_height']=$request->param('install_height');
        $data['union_serial']=$request->param('union_serial');
        $jhDustbinInfo=new JhDustbinInfo();
        $jhDustbinInfo->save($data);
        $data['dustbinId']=$jhDustbinInfo->dustbin_id;
        //查询出对应的市，区，街道
        $jhArea=new JhArea();
        $data['city']=$jhArea->where('area_id',$data['area_id0'])->value('area_name');
        $data['area']=$jhArea->where('area_id',$data['area_id1'])->value('area_name');
        $data['street']=$jhArea->where('area_id',$data['area_id2'])->value('area_name');
        return json($data);
    }


    //查询符合条件的设备
    public function queryDevice(Request $request)
    {
        $type=$request->param('type');
        $str=$request->param('state');
        $state=explode(',',$str);
        $res=model('Index','service')->queryDevice($type,$state);
        return json($res);
//        return $res;
    }






}