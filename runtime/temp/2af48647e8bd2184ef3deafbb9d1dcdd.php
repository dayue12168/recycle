<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:58:"/project/recycle/public/../app/index/view/index/index.html";i:1556524808;s:49:"/project/recycle/app/index/view/layoutextend.html";i:1556503668;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no /">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>首页</title>
    <link rel="stylesheet" href="/static/common/css/ssbase.css">
    <link rel="stylesheet" href="/static/common/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/common/css/font-awesome.min.css">
    
<link rel="stylesheet" href="/static/index/css/style.css">
<link rel="stylesheet" href="https://a.amap.com/jsapi_demos/static/demo-center/css/demo-center.css" />

</head>
<body>
<header>
    <div class='head flex-box flex-b flex-col-c'>
        <a href="<?php echo url('Index/index'); ?>">公司名称</a>
        <span>消息<p>(1)</p> </span>
    </div>
</header>


<div id="headbar"></div>
<div id="con">
    <div id='container'></div>
    <button type="button" id="setFitView" class="btn  btn-xs reload">
        <i class="fa fa-refresh" aria-hidden="true"></i>
    垃圾桶查看</button>
    <button type="button" class="btn  btn-xs roadline">规划路线</button>
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

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.10&key=6c2cfa8103979289249bd9a725514ab2"></script>
<!-- add -->
<script src="https://webapi.amap.com/maps?v=1.4.10&key=6c2cfa8103979289249bd9a725514ab2&plugin=AMap.Driving"></script>
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
    layer.open({
        content: '定位失败，请设置定位权限'
        ,btn: '我知道了'
    });
}


// 获取 点击地点经纬度
var endPoslng = "",endPoslat = "";
function bindClick(e){
    if(driving)
    {
        //调用clear()函数清除上一次结果，可以清除地图上绘制的路线以及路径文本结果
        map.clearMap();
    }
    endPoslng = e.lnglat.getLng();
    endPoslat = e.lnglat.getLat();
    // 根据起终点经纬度规划驾车导航路线
    driving.search(new AMap.LngLat(startPoslng,startPoslat), new AMap.LngLat(endPoslng,endPoslat), function(status, result) {
        // result即是对应的驾车导航信息，相关数据结构文档请参考 https://lbs.amap.com/api/javascript-api/reference/route-search#m_DrivingResult
        if (status === 'complete') {
            if (result.routes && result.routes.length) {

                drawRoute(result.routes[0])
                log.success('绘制驾车路线完成')
            }
        } else {
            log.error('获取驾车数据失败：' + result)
        }
    });
};

// 绑定规划路线
function roadline(){
    log.success("请点击您要去的终点位置");
    if(this.innerHTML == "规划路线"){
        map.on("click",bindClick);
        this.innerHTML = "取消规划"
    }else{
        // 解绑
        map.clearMap();
        map.off("click",bindClick);
        // document.getElementsByClassName("roadline")[0].onclick = off_roadline;
        this.innerHTML = "规划路线";
        log.error("已取消规划路线");
    }
}

document.getElementsByClassName("roadline")[0].onclick = roadline;

var drivingOption = {
    // AMap.DrivingPolicy.LEAST_TIME    最快捷模式
    // AMap.DrivingPolicy.LEAST_FEE     最经济模式
    // AMap.DrivingPolicy.LEAST_DISTANCE    最短距离模式
    // AMap.DrivingPolicy.REAL_TRAFFIC  考虑实时路况
    policy: AMap.DrivingPolicy.LEAST_TIME, // 其它policy参数请参考 https://lbs.amap.com/api/javascript-api/reference/route-search#m_DrivingPolicy
    ferry: 1, // 是否可以使用轮渡   1 为 否   0 为 是
    // province: '京', // 车牌省份的汉字缩写
}

// 构造路线导航类
var driving = new AMap.Driving(drivingOption)



function drawRoute (route) {

    var path = parseRouteToPath(route)

    var startMarker = new AMap.Marker({
        position: path[0],
        icon: 'https://webapi.amap.com/theme/v1.3/markers/n/start.png',
        map: map
    })

    var endMarker = new AMap.Marker({
        position: path[path.length - 1],
        icon: 'https://webapi.amap.com/theme/v1.3/markers/n/end.png',
        map: map
    })

    var routeLine = new AMap.Polyline({
        path: path,
        isOutline: true,
        outlineColor: '#ffeeee',
        borderWeight: 2,
        strokeWeight: 5,
        strokeColor: '#0091ff',
        lineJoin: 'round'
    })



    routeLine.setMap(map);


    // 调整视野达到最佳显示区域
    map.setFitView([ startMarker, endMarker, routeLine ])
}

// 解析DrivingRoute对象，构造成AMap.Polyline的path参数需要的格式
// DrivingResult对象结构参考文档 https://lbs.amap.com/api/javascript-api/reference/route-search#m_DriveRoute
function parseRouteToPath(route) {
    var path = []

    for (var i = 0, l = route.steps.length; i < l; i++) {
        var step = route.steps[i]
        for (var j = 0, n = step.path.length; j < n; j++) {
            path.push(step.path[j])
        }
    }

    return path
}

    // var marker, map = new AMap.Map("container", {
    //     resizeEnable: true,
    //     center: ['31.24450', '121.45866'],
    //     zoom: 13
    // });

    // // 实例化点标记
    // function addMarker() {
    //     marker = new AMap.Marker({
    //         icon: "//a.amap.com/jsapi_demos/static/demo-center/icons/poi-marker-default.png",
    //         position: ['31.24450', '121.45866'],
    //         offset: new AMap.Pixel(-13, -30)
    //     });
    //     marker.setMap(map);
    // }


    // var center = map.getCenter();

    // var centerText = '当前中心点坐标：' + center.getLng() + ',' + center.getLat();
    // document.getElementById('centerCoord').innerHTML = centerText;
    // document.getElementById('tips').innerHTML = '成功添加三个点标记，其中有两个在当前地图视野外！';

    // 添加事件监听, 使地图自适应显示到合适的范围
    AMap.event.addDomListener(document.getElementById('setFitView'), 'click', function() {

        var markers = [{
            icon: '/static/index/images/trash_unfull.png',
            position: [121.480699,31.236858]
        }, {
            icon: '/static/index/images/trash_unfull.png',
            position: [121.481699,31.239858]
        }, {
            icon: '/static/index/images/trash_full.png',
            position: [121.498971,31.240019]
        }];

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
