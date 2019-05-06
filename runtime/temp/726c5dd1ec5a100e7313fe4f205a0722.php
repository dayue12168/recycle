<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/project/recycle/public/../app/admin/view/index/index.html";i:1556082789;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="Generator" content="EditPlus®">
    <meta name="Author" content="guanzejian">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <title>后台模板</title>
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css">
    <style>
        .layui-nav-tree>.layui-nav-item>.layui-nav-child>.layui-nav-item{padding-left: 20px}
        .layui-nav-tree>.layui-nav-item>a{font-weight: 700}
        .layui-nav-item>a{color: #fff !important;font-size: 16px !important; }
    </style>
</head>

<body>
    <!-- 布局容器 -->
    <div class="layui-layout layui-layout-admin">
        <!-- 头部 -->
        <div class="layui-header">
            <div class="layui-main">
                <!-- logo -->
                <a href="/" style="line-height: 60px">
                    <img src="/static/admin/images/logo.png" height="45" alt="">
                </a>
                <!-- 水平导航 -->
                <ul class="layui-nav" style="position: absolute; top: 0; right: 0; background: none;">

                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            欢迎！<span><?php echo \think\Session::get('adminRole'); ?>(<span class="reo"><?php echo \think\Session::get('adminUser'); ?></span>)</span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd>
                                <a href="/admin/Login/loginOut">
                                    退出登录
                                </a>
                            </dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            上海市
                        </a>
                        <dl class="layui-nav-child">
                            <?php foreach($citys as $val): ?>
                            <dd>
                                <a href="javascript:;">
                                    <?php echo $val['area_name']; ?>
                                </a>
                            </dd>
                            <?php endforeach; ?>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 侧边栏 -->
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree" lay-filter="left-nav" style="border-radius: 0;">
                </ul>
            </div>
        </div>


        <!-- 主体 -->
        <div class="layui-body">
            <!-- 顶部切换卡 -->
            <div class="layui-tab layui-tab-brief" lay-filter="top-tab" lay-allowClose="true" style="margin: 0;">
                <ul class="layui-tab-title"></ul>
                <div class="layui-tab-content"></div>
            </div>
        </div>

        <!--底部 -->
        <div class="layui-footer" style="text-align: center; line-height: 44px;">
            Copyright © 2018
        </div>
    </div>

    <script type="text/javascript" src="/static/admin/layui/layui.js"></script>
    <script src="/static/common/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript">
        /**
         * 对layui进行全局配置
         */
        layui.config({
            base: '/static/admin/js/'
        });

        /**
         * 初始化整个cms骨架
         */

        layui.use(['cms'], function() {
            var cms = layui.cms('left-nav', 'top-tab');
            $(document).ready(function(){
                var user = $(".reo").html();
                $.ajax({
                    url:"<?php echo url('admin/user/getAuthList'); ?>",
                    type:"POST",
                    data:{"user":user},
                    cache:false,
                    success:function(data){
                        cms.addNav(data,0,'id', 'pid', 'node', 'url')
                        cms.bind(60 + 41 + 20 + 44); //头部高度 + 顶部切换卡标题高度 + 顶部切换卡内容padding + 底部高度
                        cms.clickLI(0);
                    }
                })
            })
        });
    </script>
</body>
</html>
