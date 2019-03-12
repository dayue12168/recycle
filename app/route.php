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

    Route::group('iot', function(){
        Route::get('/create', 'v1/Api/create');
        Route::get('/delete', 'v1/Api/delete');
        Route::get('/getSSOUrl', 'v1/Api/getSSOUrl');
    });

Route::get('/set_garbage_alert', 'v1/Api/setGarbageAlert');
Route::get('/get_app_thing', 'v1/Api/getAppThing');
