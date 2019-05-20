<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/9
 * Time: 14:24
 */
namespace app\admin\service;

use think\Db;

class Address
{
    //获取城市列表
    public function getCitys()
    {
        $sql='select area_id,area_parent_id,area_name from jh_area where area_parent_id=0';
        $res=Db::query($sql);
        return $res;
    }

    //获取某一区域下面的子地区列表
    public function getChildAddr($addr)
    {
        $sql='select area_id,area_level,area_parent_id,area_name from jh_area where area_parent_id='.$addr;
        $res=Db::query($sql);
        return $res;
    }

    public function addChild($id,$name)
    {
        $level=Db::table('jh_area')->where('area_id',$id)->fetchSql(false)->value('area_level');
        $res=0;
        if($level) {
            Db::name('jh_area')->fetchSql(false)->insert([
                'area_level' => $level+1,
                'area_parent_id' => $id,
                'area_name' => $name
            ]);
            $res=Db::name('jh_area')->getLastInsID();
        }
        return $res;
    }

    public function updateName($id,$name)
    {
        $res=Db::table('jh_area')->where('area_id',$id)->update(
            ['area_name'=>$name]
        );
        return $res;
    }

    public function delStreet($id)
    {
//        $level=Db::table('jh_area')->where('area_id',$id)->value('area_level');
        $res=Db::table('jh_area')->where('area_id',$id)->field('area_name,area_level')->find();
        //判断级别
        $isDel=0;
        if($res['area_level']==2){
            //街判断下面是否有班组
            $isDel=Db::table('jh_area')->where('area_parent_id',$id)->find();
        }elseif($res['area_level']==3){
            //班组下面判断是否有环卫工
            $isDel=Db::table('jh_work_info')->where('area_id3',$id)->find();
        }
        unset($res['area_level']);
        $res['state']=0;
        if(is_null($isDel)){
            $res['state']=Db::table('jh_area')->delete($id);
        }
        $res['id']=$id;
        return $res;
    }


}