<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function reportForm(){
        return $this->fetch();
    }

    //查询视图
    public function trashNumber(Request $request){
        $action = $request->get('action');
        switch ($action)
        {
            case 'trashTable':
                $this->assign('title','垃圾数量统计表');
                $this->assign('url','trashTable.html');
                break;
            case 'trashOverflow':
                $this->assign('title','垃圾溢出情况统计表');
                $this->assign('url','trashOverflow.html');
                break;
            case 'recoveryTime':
                $this->assign('title','回收效率统计表(按时间明细)');
                $this->assign('url','recoveryTime.html');
                break;
            case 'recoveryArea':
                $this->assign('title','回收效率统计表(按区域明细)');
                $this->assign('url','recoveryArea.html');
                break;
        }
        return $this->fetch();
    }

    public function recoveryArea(){
        return $this->fetch();
    }
    public function recoveryTime(){
        return $this->fetch();
    }
    public function trashOverflow(){
        return $this->fetch();
    }
    public function trashTable(){
        return $this->fetch();
    }
}
