<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/7
 * Time: 20:42
 */

namespace app\admin\controller;
use app\admin\controller\Base;

class System extends Base
{
    //短信提醒设置
    public function info_manager()
    {
        return $this->fetch();
    }

    //日志管理
    public function log_manage()
    {
        return $this->fetch();
    }

    //环卫工接收消息列表
    public function info_huanwei()
    {
        return $this->fetch();
    }
}