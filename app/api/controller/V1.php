<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2019/2/28
 * Time: 16:57
 */
namespace app\api\controller;
use think\Db;
class V1
{
    public function bigScreen()
    {
        $res='{"success":"0","message":"失败原因","sign":"jiuhai","result":{"dustbintotal":"100","binlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],"captotal":"50","caponline":"48","capoffline":"2","offlinelist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}],"dust1":"10000","dust7":"70000","overflownum":"2","overflowlist":[{"longitude":"123.12","latitude":"456.45"},{"longitude":"321.32","latitude":"654.65"}]}}';
        $res=json_decode($res);
        echo '<pre/>';
        print_r($res);

        return 'bbbbb';
    }

    public function bigScreenTemp()
    {
        $sql='select * from jh_dustbin_info';
        $res=Db::query($sql);
        return json_encode($res);
    }
}