<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:64:"/project/recycle/public/../app/index/view/index/report_form.html";i:1556082789;s:49:"/project/recycle/app/index/view/layoutextend.html";i:1556503668;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no /">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>报表查看</title>
    <link rel="stylesheet" href="/static/common/css/ssbase.css">
    <link rel="stylesheet" href="/static/common/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/common/css/font-awesome.min.css">
    
<link rel="stylesheet" href="/static/index/css/style.css">
<style>
    html,body{width: 100%;height:100%;overflow: hidden;}
    .classify>div{width: 120px;border: 1px solid #999;margin: 20px;text-align: center;border-radius: 5px}
    .classify>div:hover{box-shadow:0 0 10px #a2a2a2}
    .classify>div>img{width: 30%}
</style>

</head>
<body>
<header>
    <div class='head flex-box flex-b flex-col-c'>
        <a href="<?php echo url('Index/index'); ?>">公司名称</a>
        <span>消息<p>(1)</p> </span>
    </div>
</header>



    <div id="headbar"></div>
    <div class="flex-box flex-a classify">
        <div>
            <a href="<?php echo url('Index/trashNumber'); ?>?action=trashTable"><img src="/static/index/images/report1.png" alt=""><br/><span>垃圾数量统计表</span></a>
        </div>
        <div>
            <a href="<?php echo url('Index/trashNumber'); ?>?action=trashOverflow"><img src="/static/index/images/report2.png" alt=""><br/><span>垃圾溢出情况统计表</span></a>
        </div>
    </div>
    <div class="flex-box flex-a classify">
        <div>
            <a href="<?php echo url('Index/trashNumber'); ?>?action=recoveryArea"><img src="/static/index/images/report4.png" alt=""><br/><span>回收效率统计表（按时间明细）</span></a>
        </div>
        <div>
            <a href="<?php echo url('Index/trashNumber'); ?>?action=recoveryTime"><img src="/static/index/images/report3.png" alt=""><br/><span>回收效率统计表（按区域明细）</span></a>
        </div>
    </div>
    <div id="footbar"></div>

<footer>
    <div class='foot flex-box flex-a flex-col-c'>
        <a href="<?php echo url('Index/index'); ?>" class="flex-col flex-col-c flex-wrap flex-a"><img src="/static/index/images/lj_1.png" data-src="../images/lj_11.png" alt=""><span>首页</span></a>
        <a href="<?php echo url('Index/reportForm'); ?>" class="flex-col flex-col-c flex-wrap flex-a"><img src="/static/index/images/table_1.png" data-src="../images/table_11.png" alt=""><span>报表查看</span></a>
        <a href="#" class="flex-col flex-col-c flex-wrap flex-a"><img src="/static/index/images/user_1.png" data-src="../images/user_11.png" alt=""><span>个人中心</span></a>
    </div>
</footer>
<script src="/static/common/js/jquery-2.1.4.min.js"></script>
<script src="/static/common/js/bootstrap.min.js"></script>
<script src="/static/common/layer/layer.js"></script>


<script>
    $(function(){
        $(".foot>a").click(function(){
            $(this).css("color","#1296db").siblings().css("color","#333");
        })

    })
</script>
</body>
</html>
