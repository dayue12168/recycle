<?php
/**
 * Created by PhpStorm.
 * User: 安远
 * Date: 2018/11/12
 * Time: 16:26
 */

namespace app\admin\model;
use think\Model;
class JhLog extends Model
{
    protected $table='jh_log';
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'log_time';
    protected $updateTime = 'log_time';
}