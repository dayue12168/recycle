<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/10/30
 * Time: 9:48
 */

namespace app\admin\controller;
use app\admin\controller\Base;
use think\response\Json;
use think\Session;
use think\Request;
use app\admin\model\JhCap;
use app\admin\model\JhDustbinInfo;
use app\admin\model\JhArea;
use think\Db;

class Index extends Base
{
    //首页
    public function index()
    {
        // sso登录时候清除token
        $tel = session('adminUser');
        Db::table('jh_user')->where('tel',$tel)->update(['token' => '']);
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

        //垃圾桶数
        $trash=JhDustbinInfo::where('dustbin_state','=',0)->count();

        //溢出垃圾桶数
        $dust=JhDustbinInfo::where('dustbin_state','=',0)
            ->where('dustbin_overflow','=',1)->count();

        //24小时垃圾数
        $startTime=date('Y-m-d',strtotime('-1 day'));
        $total=Db::table('jh_rubbish_record')->whereTime('dust_date','between',[$startTime,$startTime])->value('dust_num');
        $total=$total?$total:0;

        $timer = date('Y-m-d H:i:s',time());

        $this->assign('trash',$trash);
        $this->assign('dust',$dust);
        $this->assign('total',$total);
        $this->assign('timer',$timer);
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
        $road=$roads[0]['area_id'];
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $types=model('Index','service')->getTypes();
        $this->assign('types',$types);
        $caps=model('Index','service')->device_mana($road);
//        dump($caps);

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
        if(empty($groups)){
            $groups[]=array('area_id'=>-1,'area_name'=>'请选择');
        }
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);
        //垃圾桶信息
        $info=model('Index','service')->getDustbinInfo($road);
        $this->assign('info',$info);

        //获取未绑定的设备
        $jhCap=new JhCap();
        $device=$jhCap::all(['cap_status'=>2]);
        $this->assign('device',$device);
        return $this->fetch();
    }


    //垃圾桶-环卫工绑定
    public function trash_huanwei()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $road=$roads[0]['area_id'];
        $groups=model('Address','service')->getChildAddr($road);
        if(empty($groups)){
            $groups[]=array('area_id'=>-1,'area_name'=>'请选择');
        }

        //获取垃圾桶
        $trashs=Db::table("jh_dustbin_info")->alias("jdi")
            ->join("jh_cap jc","jdi.cap_id=jc.cap_id")
            ->field("jdi.dust_serial,jc.cap_imsi")
            ->select();
        //环卫工
        $works=Db::table("jh_work_info")->field("worker_id,worker_name")->select();
        $this->assign("works",$works);
        $this->assign('trashs',$trashs);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);


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
        $param['cap_type']=($request->param('type'));
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

    //设备解绑垃圾桶
    public function freeDevice(Request $request)
    {
        $id=$request->param('id');
        $res=model('Index','service')->freeDevice($id);
    }

    //垃圾桶解绑设备
    public function freeTrash(Request $request)
    {
        $id=$request->param('id');
        $res=model('Index','service')->freeTrash($id);
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
        $status=$request->param('status');
        $addr=$request->param('addr');
        $addr=explode(',',$addr);
        if(count($addr)==1){//表明不需要地址条件
            $addr=false;
        }
        $res=model('Index','service')->queryDevice($type,$status,$addr);
        return json($res);
    }

    //禁启用垃圾桶设备
    public function updateJQ(Request $request){
        $id=$request->param('id');
        $data['dustbin_state']=$request->param('state');
//        $jhDustbinInfo=new JhDustbinInfo();
        $jhDustbinInfo=JhDustbinInfo::get($id);
        return $jhDustbinInfo->save($data);

    }


    //查询垃圾设备
    public function queryTrash(Request $request)
    {
        $type=$request->param('type');
        $addr=$request->param('addr');
        $res=model('Index','service')->queryTrash($type,$addr);
        return json($res);
    }


    //修改垃圾桶信息
    public function updateTrash(Request $request)
    {
//        return $request->param();
        $where['dustbin_id']=$request->param('id');
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
        $jhDustbinInfo->save($data,$where);
        //查询出对应的市，区，街道
        $jhArea=new JhArea();
        $data['city']=$jhArea->where('area_id',$data['area_id0'])->value('area_name');
        $data['area']=$jhArea->where('area_id',$data['area_id1'])->value('area_name');
        $data['street']=$jhArea->where('area_id',$data['area_id2'])->value('area_name');
        return json($data);
    }

    //获取单个垃圾桶信息
    public function getTrash(Request $request)
    {
        $id=$request->param('id');
        $jhDustbinInfo=new JhDustbinInfo();
        $res=$jhDustbinInfo->get($id);
        return json($res);
    }

    //垃圾桶--设备绑定
    public function trashDevice(Request $request)
    {
        $where['dustbin_id']=$request->param('trash');
        $data['cap_id']=$request->param('id');
        $data['install_height']=$request->param('iHeight');
        $jhDustbinInfo=new JhDustbinInfo();
        $jhDustbinInfo->save($data,$where);
        $jhCap=new JhCap();
        $jhCap=$jhCap::get($data['cap_id']);
        $jhCap->cap_status=0;
        $jhCap->save();
//        return $jhCap::where('cap_id',$data['cap_id'])->value('cap_imei');
        return true;
    }


    //用imei号查询设备
    public function getCapById(Request $request)
    {
        $jhCap=new JhCap();
        $imei=$request->param('id');
        if(!empty($imei)){
            $res[]=$jhCap::getByCapImei($imei);
            if(empty($res[0])){
                $res=[];
            }
        }else{
            $res=$jhCap::all();
        }
        return $res;
    }

    // 获取垃圾桶坐标信息
    public function getDustInfo(Request $request)
    {
        $data = Db::table('jh_dustbin_info')->where('dustbin_id','>',3)->field('latitude,longitude,dustbin_overflow')->select();
        $res = [];
           foreach ($data as $key => $value) {
           $res[] = array_values($value);
               # code...
           }

        return json_encode($res);
    }

    //获取垃圾桶绑定信息
    public function getTrashs(Request $request)
    {
        $imei=$request->param('imei');//传递的是设备imei号
        $users=Db::table("jh_cap")
            ->alias("jc")
            ->join("jh_dustbin_info jdi","jc.cap_id=jdi.cap_id")
            ->join("jh_bind jb","jdi.dustbin_id=jb.dustbin_id")
            ->join("jh_work_info jwi","jb.worker_id=jwi.worker_id")
            ->field("jwi.worker_name,jwi.belong_user_id")
            ->where("jc.cap_imei='".$imei."'")
            ->select();
        return json($users);
    }

    public function bind(Request $request)
    {
        $trash=$request->param("trash");
        $user=$request->param("user");
        $data['bind_time']=date("Y-m-d H:i:s",time());
        $data['worker_id']=$user;
        $data['dustbin_id']=Db::table("jh_dustbin_info")
            ->alias("jdi")
            ->join("jh_cap jc","jdi.cap_id=jc.cap_id")
            ->where("jc.cap_imei='".$trash."'")
            ->value("jdi.dustbin_id");
        $exist=Db::table("jh_bind")->where("worker_id",$data['worker_id'])
            ->where("dustbin_id",$data['dustbin_id'])
            ->value("bind_id");
        if(!$exist){
           return  Db::table("jh_bind")->insert($data)?'true':'false';
        }
        return 'false';
    }

    public function unbind(Request $request)
    {

    }

    public function trashByWorker(Request $request)
    {
        $work=$request->param("worker_id");
        $trashs=Db::table("jh_bind")
            ->alias("jb")
            ->join("jh_dustbin_info jdi","jb.dustbin_id=jdi.dustbin_id")
            ->join("jh_cap jc","jc.cap_id=jdi.cap_id")
            ->field("jdi.dust_serial,jc.cap_imei")
            ->where("jb.worker_id=".$work)
            ->select();
        return json($trashs);
    }
}
