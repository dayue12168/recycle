<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

    Route::group('api', function(){
        Route::get('/big_screen', 'v1/Api/bigScreen');
        Route::get('get_api_cron', 'v1/Api/getApiCron');
        // Route::get('/big_screen1', 'v1/Api/bigScreenTemp');
    });

    // Route::group('iot', function(){
    //     Route::get('/create', 'v1/Api/create');
    //     Route::get('/delete', 'v1/Api/delete');
    //     Route::get('/getSSOUrl', 'v1/Api/getSSOUrl');
    // });
//调用警报api
Route::get('/get_garbage_alert', 'v1/Api/getGarbageAlert');
Route::get('/get_app_thing', 'v1/Api/getAppThing');
Route::get('/get_app_thing_status', 'v1/Api/getAppThingStatus');

// 调用阿里云应用托管api
Route::get('/get_api_thing', 'v1/Api/getApiThing');




Route::get('authorize', 'v1/OAuth/authorize');
Route::post('authorize', 'v1/OAuth/authorize');
Route::post('token', 'v1/OAuth/token');
Route::get('cb', 'v1/OAuth/cb');


Route::get('get_authorize', 'v1/OAuth/getAuthorize');
Route::get('res1', 'v1/OAuth/res1');

Route::get('sso', 'v1/Api/sso');

Route::get('auto_get_info', 'v1/Api/autogetinfo');
Route::get('get_dust_info', 'admin/Index/getDustInfo');


