<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/8
 * Time: 22:15
 */

namespace app\admin\service;

use think\Db;


class Index
{
    public function device_mana($road)
    {
        $sql='select jh.*,concat(ja1.area_name,"-",ja2.area_name,"-",ja3.area_name) address from jh_cap jh join jh_dustbin_info jdi on jh.cap_id=jdi.cap_id join jh_area ja1 on jdi.area_id0=ja1.area_id join jh_area ja2 on jdi.area_id1=ja2.area_id join jh_area ja3 on jdi.area_id2=ja3.area_id where cap_status=0 and jdi.area_id2='.$road;
        $res=Db::query($sql);
//        $res = Db::table('jh_cap')
//            ->alias('jh')
//            ->join('jh_dustbin_info jdi','jh.cap_id=jdi.cap_id')
//            ->join('jh_area ja1','jdi.area_id0=ja1.area_id')
//            ->join('jh_area ja2','jdi.area_id1=ja2.area_id')
//            ->join('jh_area ja3','jdi.area_id2=ja3.area_id')
//            ->where('jh.cap_status',0)
//            ->where('jdi.area_id2',$road)
//            ->field('jh.*,concat(ja1.area_name,"-",ja2.area_name,"-",ja3.area_name) address')
//            ->paginate(1);
        return $res;
    }

    public function getTypes()
    {
        $sql='select distinct cap_type from jh_cap';
        $res=Db::query($sql);
        return $res;
    }

    public function getDustbinInfo($road)
    {
        $sql='select case when isnull(worker_id) then	0 else count(*) end count,jiang.* from ';
        $sql.='(select jdi.*, jc.cap_imei,ja1.area_name city,ja2.area_name area,ja3.area_name street,jb.worker_id ';
        $sql.='	from jh_dustbin_info jdi left join jh_area ja1 on jdi.area_id0 = ja1.area_id left join jh_area ja2 ';
        $sql.='on jdi.area_id1 = ja2.area_id left join jh_area ja3 on jdi.area_id2 = ja3.area_id ';
        $sql.='left join jh_cap jc on jdi.cap_id = jc.cap_id left join jh_bind jb on jdi.dustbin_id = jb.dustbin_id where area_id2='.$road.') jiang ';
        $sql.='group by	dustbin_id';
//        die($sql);
//        return $sql;
        $res=Db::query($sql);
        return $res;
    }

    public function queryDevice($type,$status,$addr)
    {
        //查询有绑定垃圾桶信息的
        if(is_array($addr)){
            $sql='select jh.*,concat(ja1.area_name,"-",ja2.area_name,"-",ja3.area_name) address from jh_cap jh join jh_dustbin_info jdi on jh.cap_id=jdi.cap_id join jh_area ja1 on jdi.area_id0=ja1.area_id join jh_area ja2 on jdi.area_id1=ja2.area_id join jh_area ja3 on jdi.area_id2=ja3.area_id where cap_type="'.$type.'" and cap_status='.$status;
            if($addr[2]<0){//表明查询全部区域
                $sql.=' and jdi.area_id1='.$addr[1];
            }else{
                $sql.=' and jdi.area_id2='.$addr[2];
            }
        }else{
            $sql='select *,case cap_status when 1 then "已禁用" when 2 then "未绑定" end address from jh_cap where cap_type="'.$type.'" and cap_status='.$status;
        }
        $sql.=' order by cap_imei limit 500';
        return Db::query($sql);
    }


    public function freeDevice($id)
    {
        $data['dustbin_id']=Db::table('jh_dustbin_info')->where('cap_id',$id)->value('dustbin_id');
        //解绑操作
        $sql='update jh_dustbin_info jdi join jh_cap jc on jdi.cap_id=jc.cap_id set jdi.cap_id=0,jc.cap_status=2 where jc.cap_id='.$id;
        Db::execute($sql);
        //写入解绑表
        $data['cap_id']=$id;
        $data['unrelate_time']=date('Y-m-d H:i:s',time());
        $data['unrelate_user']=session('adminUser');
        Db::name('jh_unrelate')->insert($data);
    }


    public function freeTrash($id)
    {
        $data['cap_id']=Db::table('jh_dustbin_info')->where('dustbin_id',$id)->value('cap_id');
        //解绑操作
        $sql='update jh_dustbin_info jdi join jh_cap jc on jdi.cap_id=jc.cap_id set jdi.cap_id=0,jc.cap_status=2 where jdi.dustbin_id='.$id;
        Db::execute($sql);
        //写入解绑表
        $data['dustbin_id']=$id;
        $data['unrelate_time']=date('Y-m-d H:i:s',time());
        $data['unrelate_user']=session('adminUser');
        Db::name('jh_unrelate')->insert($data);
    }


    public function queryTrash($type,$addr)
    {
        $type=explode(',',$type);
        $addr=array_reverse(explode(',',$addr));
        foreach ($addr as $key=>$val){
            switch($val){
                case -1:
//                    continue;
                break;
                default:
                    if($key==0){
                        $addrWhere[]=' jwi.area_id3='.$val;
                    }elseif($key==1){
                        $addrWhere[]=' jdi.area_id2='.$val;
                    }else{
                        $addrWhere[]=' jdi.area_id1='.$val;
                    }
                    break;
            }
        }
        $addrWhere=$addrWhere[0];
        $sql='select case when isnull(worker_id) then	0 else count(*) end count, jiang.* from (select jdi.*, jc.cap_imei,ja1.area_name city,	ja2.area_name area,ja3.area_name street,jb.worker_id from jh_dustbin_info jdi left join jh_area ja1 on jdi.area_id0 = ja1.area_id left join jh_area ja2 on jdi.area_id1 = ja2.area_id left join jh_area ja3 on jdi.area_id2 = ja3.area_id left join jh_cap jc on jdi.cap_id = jc.cap_id left join jh_bind jb on jdi.dustbin_id = jb.dustbin_id left join jh_work_info jwi on jwi.worker_id=jb.worker_id where '.$addrWhere.' and (';
        foreach($type as $val){
            switch ($val){
                case 1:
                    $typeWhere=' jdi.cap_id<>0 or';
                    $sql.=$typeWhere;
                    break;
                case 2:
                    $typeWhere=' jdi.cap_id=0 or';
                    $sql.=$typeWhere;
                    break;
                case 3:
                    $typeWhere=' jb.worker_id<>0 or';
                    $sql.=$typeWhere;
                    break;
                case 4:
                    $typeWhere=' jb.worker_id is null or';
                    $sql.=$typeWhere;
                    break;
                default:
                    $typeWhere=' 1 or';
                    $sql.=$typeWhere;
                    break;
            }

        }
        $sql=substr($sql,0,-2).')) jiang group by dustbin_id';
//        die($sql);
        $res=Db::query($sql);
        return $res;
    }



}