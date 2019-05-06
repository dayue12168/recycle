<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/9
 * Time: 14:41
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\JhArea;
class Address
{
    //获取某地区下的子地区列表
    public function getOneChild(Request $request)
    {
        $id=$request->param('id');
        $jhArea=new JhArea();
//        $res=$jhArea::where('area_parent_id',$id)->column('area_id,area_name');
        $res=$jhArea::all(['area_parent_id'=>$id]);
        return json($res);
    }

    //获取某一区域下面的所有子地区列表
    public function getChildAddr(Request $request)
    {
        $addr=$request->param('addr');
        $res=model('Address','service')->getChildAddr($addr);
        $list=array();
        if(key_exists(0,$res)){
            for($i=$res[0]['area_level'];$i<=3;$i++){
//                if($cat[0]['area_level']<2){
//                    $list[$res[0]['area_level']]=$res;
//                }
                $list[$res[0]['area_level']]=$res;
//                $list[$res[0][$i]]=$res;
                $temp=model('Address','service')->getChildAddr($res[0]['area_id']);
                if(!$temp && $i<3){
                    array_push($list,[['area_id'=>-1,'area_name'=>'请选择']]);
                }else{
                    /*if($res[0]['area_level']==1){
                        array_unshift($list[$res[0]['area_level']],['area_id'=>-1,'area_parent_id'=>$res[0]['area_parent_id'],'area_name'=>'全部区域']);
                    }else*/if($res[0]['area_level']==2){
                        array_unshift($list[$res[0]['area_level']],['area_id'=>-1,'area_parent_id'=>$res[0]['area_parent_id'],'area_name'=>'全部街道']);
                    }elseif($res[0]['area_level']==3){
                        array_unshift($list[$res[0]['area_level']],['area_id'=>-1,'area_parent_id'=>$res[0]['area_parent_id'],'area_name'=>'全部班组']);
                    }
                    $res=$temp;
                }
            }
        }else{
            array_push($list,[['area_id'=>-1,'area_name'=>'请选择']]);
        }
        //将数组重新遍历一遍
        $res=array();
        foreach($list as $val){
            $res[]=$val;
        }
        return json($res);
    }

    //添加某一区域下的子区域
    public function addChild(Request $request)
    {
//        return json($request->param());
        $pid=$request->param('id');
        $name=$request->param('name');
        $res['state']=model('Address','service')->addChild($pid,$name);
        if($res['state']){
            $res['info']=$name.'区域创建成功';
        }else{
            $res['info']=$name.'区域创建失败';
        }
        return json($res);
    }

    //更改地址名
    public function updateName(Request $request)
    {
        $pid=$request->param('id');
        $name=$request->param('name');
        $res['state']=model('Address','service')->updateName($pid,$name);
        if($res['state']){
            $res['info']=$name.'改名成功';
        }else{
            $res['info']=$name.'改名失败';
        }
        return json($res);
    }

    //删除街道/班组
    public function delStreet(Request $request)
    {
        $id=$request->param('id');
        $res=model('Address','service')->delStreet($id);
        return json($res);
    }


}