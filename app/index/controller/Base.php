<?php
/**
 * Created by PhpStorm.
 * User: dayue
 * Date: 2018/11/3
 * Time: 14:02
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        $tel = session('tel');
        if ($tel == null) {
            $this->redirect('Login/index', '请先登录后操作');
        }
    }
}
