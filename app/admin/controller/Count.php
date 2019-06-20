<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/7
 * Time: 20:47
 */

namespace app\admin\controller;

use app\admin\controller\Base;
use think\Request;
use think\Loader;
use think\Db;

class Count extends Base
{
    //垃圾数量统计表
    public function trash_number()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $road=$roads[0]['area_id'];
        $groups=model('Address','service')->getChildAddr($road);
        $ctime=date('Y-m-01',strtotime('-1 year'));
        $etime=date('Y-m-d');
        $res=Db::table('jh_rubbish_record')
            ->alias('jrr')
            ->join('jh_dustbin_info jdi','jrr.dustbin_id=jdi.dustbin_id')
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field('jrr.dust_num,jrr.dust_date,jrr.dust_num,sum(dust_num) total,ja.area_name pro,ja1.area_name city,ja2.area_name district')
            ->whereTime("jrr.dust_date","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jrr.dustbin_id")
            ->select();
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);
        $this->assign('res',$res);
        $this->assign('ctime',$ctime);
        $this->assign('etime',$etime);
        return $this->fetch();
    }


    //垃圾溢出情况统计表
    public function trash_overflow()
    {
        $citys=model('Address','service')->getCitys();
        $city=$citys[0]['area_id'];
        $regions=model('Address','service')->getChildAddr($city);
        $region=$regions[0]['area_id'];
        $roads=model('Address','service')->getChildAddr($region);
        $road=$roads[0]['area_id'];
        $groups=model('Address','service')->getChildAddr($road);
        $ctime=date('Y-m-01',strtotime('-1 year'));
        $etime=date('Y-m-d');
        $res=Db::table('jh_overflow')->alias("jo")
            ->join("jh_dustbin_info jdi","jo.dustbin_id=jdi.dustbin_id")
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field("ja.area_name pro,ja1.area_name city,ja2.area_name district,sum(jo.overflow_dustnum) total")
            ->whereTime("jo.overflow_time","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jdi.dustbin_id")
            ->select();
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);
        $this->assign('res',$res);
        $this->assign('ctime',$ctime);
        $this->assign('etime',$etime);
        return $this->fetch();
    }


    //垃圾数量统计查询
    public function queryNumber(Request $request)
    {
        $data=$request->param();
        $ctime=$data['ctime'];
        $etime=$data['etime'];
        $res=Db::table('jh_rubbish_record')
            ->alias('jrr')
            ->join('jh_dustbin_info jdi','jrr.dustbin_id=jdi.dustbin_id')
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field('jrr.dust_num,jrr.dust_date,jrr.dust_num,sum(dust_num) total,ja.area_name pro,ja1.area_name city,ja2.area_name district')
            ->whereTime("jrr.dust_date","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jrr.dustbin_id")
            ->select();
        return json($res);
    }



    //垃圾溢出情况查询
    public function queryOverflow(Request $request)
    {
        $data=$request->param();
        $ctime=$data['ctime'];
        $etime=$data['etime'];
        $res=Db::table('jh_overflow')->alias("jo")
            ->join("jh_dustbin_info jdi","jo.dustbin_id=jdi.dustbin_id")
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field("ja.area_name pro,ja1.area_name city,ja2.area_name district,sum(jo.overflow_dustnum) total")
            ->whereTime("jo.overflow_time","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jdi.dustbin_id")
            ->select();
        return json($res);
    }

    //回收效率统计表
    public function recovery()
    {
        return $this->fetch();
    }

    public function number_excel(Request $request)
    {
        $path = dirname(__FILE__); //找到当前脚本所在路径
        Loader::import('PHPExcel.Classes.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类

        $data=$request->param();
        $ctime=$data['ctime'];
        $etime=$data['etime'];
        $res=Db::table('jh_rubbish_record')
            ->alias('jrr')
            ->join('jh_dustbin_info jdi','jrr.dustbin_id=jdi.dustbin_id')
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field('jrr.dust_num,jrr.dust_date,jrr.dust_num,sum(dust_num) total,ja.area_name pro,ja1.area_name city,ja2.area_name district')
            ->whereTime("jrr.dust_date","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jrr.dustbin_id")
            ->select();

        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("垃圾详情"); //给当前活动sheet设置名称

        $PHPSheet->setCellValue("A1","区域")->setCellValue("B1","日期/周/月份");//表头数据
        $PHPSheet->setCellValue("C1","垃圾量")->setCellValue("D1","平均每小时垃圾量");//表头数据
        $PHPSheet->setCellValue("E1","平均每天垃圾量")->setCellValue("F1","平均7天垃圾量");//表头数据
        $PHPSheet->setCellValue("G1","平均30天垃圾量");//表头数据
        foreach($res as $key=>$val){
            $key+=2;
            $PHPSheet->setCellValue("A".$key,$val['pro'].'-'.$val['city'].'-'.$val['district']);
            $PHPSheet->setCellValue("B".$key,$val['dust_date']);
            $PHPSheet->setCellValue("C".$key,$val["dust_num"]);
            $PHPSheet->setCellValue("D".$key,$val['dust_num']/12);//表格数据
            $PHPSheet->setCellValue("E".$key,$val['total']);
            $PHPSheet->setCellValue("F".$key,$val['total']/7);//表格数据
            $PHPSheet->setCellValue("G".$key,$val['total']/30);//表格数据
        }


        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");//创建生成的格式
        header('Content-Disposition: attachment;filename="垃圾数量统计.xls"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xls文件
    }

    public function overflow_excel(Request $request)
    {
        $path = dirname(__FILE__); //找到当前脚本所在路径
        Loader::import('PHPExcel.Classes.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.Classes.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类

        $data=$request->param();
        $ctime=$data['ctime'];
        $etime=$data['etime'];
        $res=Db::table('jh_overflow')->alias("jo")
            ->join("jh_dustbin_info jdi","jo.dustbin_id=jdi.dustbin_id")
            ->join("jh_area ja","jdi.area_id0=ja.area_id")
            ->join("jh_area ja1","jdi.area_id1=ja1.area_id")
            ->join("jh_area ja2","jdi.area_id2=ja2.area_id")
            ->field("ja.area_name pro,ja1.area_name city,ja2.area_name district,sum(jo.overflow_dustnum) total")
            ->whereTime("jo.overflow_time","between",[$ctime,$etime])
            ->fetchSql(false)
            ->group("jdi.dustbin_id")
            ->select();

        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("垃圾溢出情况"); //给当前活动sheet设置名称

        $PHPSheet->setCellValue("A1","区域")->setCellValue("B1","溢出次数");//表头数据
        $PHPSheet->setCellValue("C1","总溢出量")->setCellValue("D1","平均回收时间");//表头数据
        $PHPSheet->setCellValue("E1","平均每天溢出量");

        foreach($res as $key=>$val){
            $key+=2;
            $PHPSheet->setCellValue("A".$key,$val['pro'].'-'.$val['city'].'-'.$val['district']);
            $PHPSheet->setCellValue("B".$key,$val['total']);
            $PHPSheet->setCellValue("C".$key,'');
            $PHPSheet->setCellValue("D".$key,'');
            $PHPSheet->setCellValue("E".$key,$val['total']);
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");//创建生成的格式
        header('Content-Disposition: attachment;filename="垃圾溢出统计.xls"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xls文件
    }
}
