<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/project/recycle/public/../app/admin/view/index/index2.html";i:1556082789;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css">
    <title>欢迎</title>
</head>
<body>
    <!-- 欢迎您：<span><?php echo \think\Session::get('adminRole'); ?>(<?php echo \think\Session::get('adminUser'); ?>)</span> -->
    <div class="layui-col-lg12 layui-collapse" style="border: none;">
                <div class="layui-col-lg12 layui-col-md12">


                    <!--统计信息展示-->
                    <fieldset class="layui-elem-field" style="padding: 5px;">
                        <!--<legend>信息统计</legend>-->
                        <blockquote class="layui-elem-quote font16">信息统计</blockquote>
                        <div class="">
                            <table class="layui-table" lay-even="">
                                <thead>
                                    <tr>
                                        <th>统计</th>
                                        <th>垃圾桶数</th>
                                        <th>溢出垃圾桶数</th>
                                        <th>24小时垃圾数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>总数</td>
                                        <td><?php echo $trash; ?></td>
                                        <td><?php echo $dust; ?></td>
                                        <td><?php echo $total; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="layui-table">
                                <thead>
                                    <tr>
                                        <th colspan="2" scope="col">服务器信息</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>服务器IP地址</th>
                                        <td><?php echo \think\Request::instance()->server('server_addr'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>服务器域名</td>
                                        <td><?php echo \think\Request::instance()->server('http_host'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>服务器端口 </td>
                                        <td><?php echo \think\Request::instance()->server('server_port'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>登录时间 </td>
                                        <td id="firstTime"><?php echo $timer; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <a href="javascript:;">
            </a></div>
</body>
</html>
