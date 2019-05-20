<?php
/**
 * Created by PhpStorm.
 * User: luxiao
 * Date: 2017/5/8
 * Time: 16:49
 */
namespace app\test\controller;

use think\Loader;
use think\Controller;

class Test extends Controller
{
    public function excel()
    {
//        return ord('A2');
        return chr(65).'1';

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
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="垃圾溢出统计.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件


        $data=[0=>[
            '区域'=>'上海市-浦东新区-海科园',
            '日期/周/月份'=>'10日/3/10',
            '垃圾量'=>'1000KG',
            '平均每小时垃圾量'=>'10KG',
            '平均每天垃圾量'=>'100KG',
            '平均7天垃圾量'=>'500KG',
            '平均30天垃圾量'=>'1000KG'
        ],1=>[
            '区域'=>'上海市-浦东新区-测试街道',
            '日期/周/月份'=>'10日/3/10',
            '垃圾量'=>'1000KG',
            '平均每小时垃圾量'=>'10KG',
            '平均每天垃圾量'=>'100KG',
            '平均7天垃圾量'=>'500KG',
            '平均30天垃圾量'=>'1000KG']];
    }
}