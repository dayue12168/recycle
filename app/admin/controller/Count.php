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
//        dump($citys);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);
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
//        dump($citys);
        $this->assign('citys',$citys);
        $this->assign('regions',$regions);
        $this->assign('roads',$roads);
        $this->assign('groups',$groups);
        return $this->fetch();
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
        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称

        $PHPSheet->setCellValue("A1","区域")->setCellValue("B1","日期/周/月份");//表头数据
        $PHPSheet->setCellValue("C1","垃圾量")->setCellValue("D1","平均每小时垃圾量");//表头数据
        $PHPSheet->setCellValue("E1","平均每天垃圾量")->setCellValue("F1","平均7天垃圾量");//表头数据
        $PHPSheet->setCellValue("G1","平均30天垃圾量");//表头数据

        $PHPSheet->setCellValue("A2","上海市-浦东新区-海科园")->setCellValue("B2","10日/3/10");//表格数据
        $PHPSheet->setCellValue("C2","1000KG")->setCellValue("D2","10KG");//表格数据
        $PHPSheet->setCellValue("E2","100KG")->setCellValue("F2","500KG");//表格数据
        $PHPSheet->setCellValue("G2","1000KG");//表格数据
        $PHPSheet->setCellValue("A3","上海市-浦东新区-测试街道")->setCellValue("B3","10日/3/10");//表格数据
        $PHPSheet->setCellValue("C3","1000KG")->setCellValue("D3","10KG");//表格数据
        $PHPSheet->setCellValue("E3","100KG")->setCellValue("F3","500KG");//表格数据
        $PHPSheet->setCellValue("G3","1000KG");//表格数据
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
        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称

        $PHPSheet->setCellValue("A1","区域")->setCellValue("B1","溢出次数");//表头数据
        $PHPSheet->setCellValue("C1","总溢出量")->setCellValue("D1","平均回收时间");//表头数据
        $PHPSheet->setCellValue("E1","平均每天溢出量");

        $PHPSheet->setCellValue("A2","上海市-浦东新区-海科园")->setCellValue("B2","200");//表格数据
        $PHPSheet->setCellValue("C2","1000KG")->setCellValue("D2","10KG");//表格数据
        $PHPSheet->setCellValue("E2","100KG");//表格数据
        $PHPSheet->setCellValue("A3","上海市-浦东新区-测试街道")->setCellValue("B3","200");//表格数据
        $PHPSheet->setCellValue("C3","1000KG")->setCellValue("D3","10KG");//表格数据
        $PHPSheet->setCellValue("E3","100KG");//表格数据
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel5");//创建生成的格式
        header('Content-Disposition: attachment;filename="垃圾溢出统计.xls"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xls文件
    }
}
