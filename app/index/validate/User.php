<?php
/**
 * Created by PhpStorm.
 * User: dayue
 * Date: 2018/11/2
 * Time: 16:55
 */

namespace app\index\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'name'  =>  'require|max:25',
        'email' =>  'email',
    ];
}