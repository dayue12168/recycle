<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"/project/recycle/public/../app/index/view/index/trash_number.html";i:1556082789;s:49:"/project/recycle/app/index/view/layoutextend.html";i:1556503668;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no /">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>查询<?php echo $title; ?></title>
    <link rel="stylesheet" href="/static/common/css/ssbase.css">
    <link rel="stylesheet" href="/static/common/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/common/css/font-awesome.min.css">
    
<link rel="stylesheet" href="/static/common/css/styles.css">
<link rel="stylesheet" href="/static/index/css/style.css">
<style>
    html,body{width: 100%;height:100%;overflow: auto}
    .classify>div{width: 120px;border: 1px solid #999;margin: 20px;text-align: center;border-radius: 5px}
    .classify>div:hover{box-shadow:0 0 10px #a2a2a2}
    .classify>div>img{width: 30%}
    ::-webkit-scrollbar{display: none}
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

    <!-- 垃圾数量统计表 -->
    <div class="pa-10" role="form" style="margin-bottom: 120px;">
        <h2><?php echo $title; ?></h2>

        <div class="form-group">
            <label style="width: 100px;">时间范围：</label>
            <select name="" class="form-control">
                <option value="" selected>按日</option>
                <option value="">按周</option>
                <option value="">按月</option>
            </select>
        </div>
        <div class="form-group flex-box flex-b flex-col-c">
                <div class="iDate date">
                    <input type="text">
                    <button type="button" class="addOn"></button>
                </div>
                <span style="color: #666">开始--结束</span>
                <div class="iDate date">
                    <input type="text">
                    <button type="button" class="addOn"></button>
                </div>

        </div>
        <div class="form-group ">
            <label style="width: 100px;">区域选择：</label>
            <select name="" class="form-control" style="margin-bottom: 5px">
                <option value="" selected>上海市</option>
                <option value="">天津市</option>
            </select>
            <select name="" class="form-control" style="margin-bottom: 5px">
                <option value="" selected>长宁区</option>
                <option value="">徐汇区</option>
            </select>
            <select name="" class="form-control" style="margin-bottom: 5px">
                <option value="" selected>天山街道</option>
                <option value="">龙华街道</option>
            </select>
            <select name="" class="form-control">
                <option value="" selected>所有班组</option>
                <option value="">班组1</option>
                <option value="">班组1</option>
                <option value="">班组1</option>
                <option value="">班组1</option>
                <option value="">班组5</option>

            </select>
        </div>
        <a href="<?php echo $url; ?>">
            <button type="button" class="btn" style="float: right;width: 150px;background: #5BC0DE;color: #fff">查看结果</button>
        </a>


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

<script src="/static/common/js/moment.js"></script>
<script src="/static/common/js/bootstrap-datetimepicker.js"></script>
<script>
if($(".iDate.date").length>0){
    $(".iDate.date").datetimepicker({
        locale:"zh-cn",
        format:"YYYY-MM-DD",
        dayViewHeaderFormat:"YYYY年 MMMM"
    });
}
</script>

<script>
    $(function(){
        $(".foot>a").click(function(){
            $(this).css("color","#1296db").siblings().css("color","#333");
        })

    })
</script>
</body>
</html>
