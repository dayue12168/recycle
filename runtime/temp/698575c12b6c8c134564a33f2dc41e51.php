<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/project/recycle/public/../app/admin/view/index/index2.html";i:1565607105;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css">
    <title>欢迎</title>
</head>
    <style type="text/css">
       html,body,#container{
           height:100%;
       }
    </style>
<body>
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


            <div id='container'></div>
            <div class="amap-geolocation-con" style="position: absolute; z-index: 9999; right: 10px; bottom: 20px;"><button style="height: 47px;width: 90px;padding: 0 5px;background-color: #fff;" type="button" id="setFitView" class="btn  btn-xs reload">
                <i class="fa fa-refresh" aria-hidden="true"></i>垃圾桶查看
            </button></div>



</body>
<script src="/static/common/js/jquery-2.1.4.min.js"></script>
<script src="/static/common/js/bootstrap.min.js"></script>
<script src="/static/common/layer/layer.js"></script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.10&key=6c2cfa8103979289249bd9a725514ab2"></script>
<!-- add -->
<script src="https://a.amap.com/jsapi_demos/static/demo-center/js/demoutils.js"></script>
<script>
var map = new AMap.Map('container', {
    resizeEnable: true
});
AMap.plugin('AMap.Geolocation', function() {
    var geolocation = new AMap.Geolocation({
        enableHighAccuracy: true,//是否使用高精度定位，默认:true
        timeout: 10000,          //超过10秒后停止定位，默认：5s
        buttonPosition:'RB',    //定位按钮的停靠位置
        buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
        zoomToAccuracy: true,   //定位成功后是否自动调整地图视野到定位点

    });
    map.addControl(geolocation);
    geolocation.getCurrentPosition(function(status,result){
        if(status=='complete'){
            onComplete(result)
        }else{
            onError(result)
        }
    });
});
//解析定位结果
//  自身位置
var startPoslng = "",srartPoslat = "";

function onComplete(data) {
    // document.getElementById('status').innerHTML='定位成功'
    log.success("定位成功");

    startPoslng = data.position.lng;
    startPoslat = data.position.lat;
    var str = [];
    str.push('定位结果：' + data.position);
    str.push('定位类别：' + data.location_type);
    if(data.accuracy){
        str.push('精度：' + data.accuracy + ' 米');
    }//如为IP精确定位结果则没有精度信息
    str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
}
// 解析定位错误信息
function onError(data) {
    // alert(JSON.stringify(data));
    layer.open({
        content: '定位失败，请设置定位权限'
        ,btn: '我知道了'
    });
}


  var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open('get','/get_dust_info');
        //步骤三:发送请求
        ajax.send();
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
           if (ajax.readyState==4 &&ajax.status==200) {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        // 　　      console.log(ajax.responseText);//输入相应的内容
                  var data = JSON.parse(ajax.responseText);
                  // 添加事件监听, 使地图自适应显示到合适的范围
    AMap.event.addDomListener(document.getElementById('setFitView'), 'click', function() {


                    var markers = [];
                    console.log(data);
                    for (var i=0;i<data.length;i++)
                    {
                    	console.log(data[i][2]);
                    	if(data[i][2] == 0){
	                        markers[i] = {'icon':'/static/index/images/trash_unfull.png','position':data[i]};
                    	}else{
	                        markers[i] = {'icon':'/static/index/images/trash_full.png','position':data[i]};
                    	}
                    }
                   // var markers = [{
                   //      icon: '/static/index/images/trash_unfull.png',
                   //      position: [121.905314,30.867764]
                   //  }, {
                   //      icon: '/static/index/images/trash_unfull.png',
                   //      position: [121.905085,30.868008]
                   //  }, {
                   //      icon: '/static/index/images/trash_full.png',
                   //      position: [121.498971,31.240019]
                   //  }];

        // 添加一些分布不均的点到地图上,地图上添加三个点标记，作为参照
        markers.forEach(function(marker) {
            new AMap.Marker({
                map: map,
                icon: marker.icon,
                position: [marker.position[0], marker.position[1]],
                offset: new AMap.Pixel(-13, -30)
            });
        });
        var newCenter = map.setFitView();
    });

          　　}
        }


</script>
</html>
