<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"/project/recycle/public/../app/admin/view/count/trash_number.html";i:1556518750;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>垃圾数量统计表</title>
    <link rel="stylesheet" href="/static/admin/layui/css/layui.css">
    <link rel="stylesheet" href="/static/admin/css/ssbase.css">
    <!-- v-5.0.9 laydate -->
    <link rel="stylesheet" href="/static/admin/layui/css/modules/laydate/laydate.css">
    <link rel="stylesheet" href="/static/admin/css/style.css">
    <style>
        .nav{width: 10%;}
        .navbar{width: 100%;text-align: center;line-height:30px;cursor: pointer;}
        /* .nav>.navbar:not(:last-child){border-bottom: 1px solid #999} */
        .navbar.active{background: #f00;color: #fff;border-radius: 0 15px 15px 0}
        .navChild{width: 90%;height: 120px;overflow-y: auto;padding-left: 20px;background: #f6f6f6}
        .hide{display: none}
        ::-webkit-scrollbar{display: none}
        /* tabnav */
        .tabnav{background: #C2C6C6;height: 40px;line-height: 40px;font-weight: 600}
        .tabnav>span{padding: 5px 20px}
        .tab_active{color: #fff}
    </style>
</head>
<body>
    <span class="layui-breadcrumb" lay-separator="/" >
        <a href="">统计报表</a>
        <a href="">垃圾数量统计表</a>
    </span>
    <div class="topLine">
        <div class="layui-form" action="">
            垃圾数量统计表
            <div class="layui-form-item flex-box flex-b">
                <div class="layui-inline">
                    <label class="layui-form-label">时间类型</label>
                    <div class=" flex-box flex-b">
                    <!-- <input type="text" name="startTime" lay-verify="required" readonly class="layui-input" placeholder="开始时间">
                    <input type="text" name="endTime" lay-verify="required" readonly class="layui-input" placeholder="结束时间"> -->
                        <input type="radio" name="timetype" value="1" title="按天" checked>
                        <input type="radio" name="timetype" value="2" title="按周">
                        <input type="radio" name="timetype" value="3" title="按月">
                    </div>
                </div>
                <div class="layui-inline" style="cursor: pointer;">
                    <button class="layui-btn layui-btn-small" >查询<i class="layui-icon">&#xe615;</i></button>
                    <button class="layui-btn layui-btn-small excel" >导出<i class="layui-icon">&#xe602;</i></button>
                </div>

            </div>
            <div class="layui-form-item flex-box flex-b">
                <div class="layui-inline">
                    <label class="layui-form-label">时间类型</label>
                    <div class=" flex-box flex-b">
                        <input type="text" name="startTime" lay-verify="required" readonly class="layui-input" placeholder="开始时间">
                        <input type="text" name="endTime" lay-verify="required" readonly class="layui-input" placeholder="结束时间">
                    </div>
                </div>
            </div>
            <div class="layui-form-item flex-box flex-2" style="background:#f6f6f6;border: 1px solid #b2b2b2">
                <div class="nav">
                    <!--<p class='navbar active'>城市</p>-->
                    <p class="navbar active">区县</p>
                    <p class="navbar">街道</p>
                    <p class="navbar">班组</p>
                </div>
                <div class="navChild">
                    <!--<p>-->
                        <!--<button onclick="oClick($(this))" class="layui-btn layui-btn-primary layui-btn-small">全省</button>-->
                        <!--<input type="checkbox" name="" value="" title="上海市" lay-skin="primary">-->
                        <!--&lt;!&ndash;<input type="checkbox" name="" value="" title="上海市" lay-skin="primary">&ndash;&gt;-->
                        <!--&lt;!&ndash;<input type="checkbox" name="" value="" title="上海市" lay-skin="primary">&ndash;&gt;-->
                        <!--&lt;!&ndash;<input type="checkbox" name="" value="" title="上海市" lay-skin="primary">&ndash;&gt;-->
                    <!--</p>-->
                    <!--<p class="hide">-->
                    <p>
                        <button onclick="oClick($(this))" class="layui-btn layui-btn-primary layui-btn-small">全市</button>
                        <?php if(is_array($regions) || $regions instanceof \think\Collection || $regions instanceof \think\Paginator): $i = 0; $__LIST__ = $regions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <input type="checkbox" name="<?php echo $vo['area_name']; ?>" value="<?php echo $vo['area_name']; ?>" title="<?php echo $vo['area_name']; ?>" lay-skin="primary">
                        <?php endforeach; endif; else: echo "" ;endif; ?>

                        <!--<input type="checkbox" name="" value="" title="浦东新区" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="徐汇区" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="徐汇区" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="徐汇区" lay-skin="primary">-->
                    </p>
                    <p class="hide">
                        <button onclick="oClick($(this))" class="layui-btn layui-btn-primary layui-btn-small">全区</button>
                        <?php if(is_array($roads) || $roads instanceof \think\Collection || $roads instanceof \think\Paginator): $i = 0; $__LIST__ = $roads;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <input type="checkbox" name="<?php echo $vo['area_name']; ?>" value="<?php echo $vo['area_name']; ?>" title="<?php echo $vo['area_name']; ?>" lay-skin="primary">
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        <!--<input type="checkbox" name="" value="" title="海科园" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="龙华街" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="龙华街" lay-skin="primary">-->
                        <!--<input type="checkbox" name="" value="" title="龙华街" lay-skin="primary"> -->
                    </p>
                    <p class="hide">
                        <button onclick="oClick($(this))" class="layui-btn layui-btn-primary layui-btn-small">整个街道</button>
                        <?php if(is_array($groups) || $groups instanceof \think\Collection || $groups instanceof \think\Paginator): $i = 0; $__LIST__ = $groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <input type="checkbox" name="<?php echo $vo['area_name']; ?>" value="<?php echo $vo['area_name']; ?>" title="<?php echo $vo['area_name']; ?>" lay-skin="primary">
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </p>
                </div>
            </div>
            <div class="tabnav" style="cursor: pointer">
                <span class="tab_active">报表明细</span>
                <span>点线图</span>
                <span>班组类比</span>
            </div>
            <div class="tab_child">
                <div>
                    <table class="layui-table">
                        <colgroup>
                            <!-- 此处 width 按比例分割 -->
                            <col width='250'>
                            <col width='200'>
                            <col width='150'>
                            <col width='150'>
                            <col width='150'>
                            <col width='150'>
                            <col width='150'>

                        </colgroup>
                        <thead>
                            <tr>
                            <th>区域</th>
                            <th>日期/周次/月份</th>
                            <th>垃圾量</th>
                            <th>平均每小时垃圾量</th>
                            <th>平均每天垃圾量</th>
                            <th>平均7天垃圾量</th>
                            <th>平均30天垃圾量</th>
                            </tr> 
                        </thead>
                        <tbody class="tbody">
                            <?php if(is_array($roads) || $roads instanceof \think\Collection || $roads instanceof \think\Paginator): $i = 0; $__LIST__ = $roads;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <tr>
                                <td><?php echo $citys[0]['area_name']; ?>-<?php echo $regions[0]['area_name']; ?>-<?php echo $vo['area_name']; ?></td>
                                <td>10日/3/10</td>
                                <td>1000KG</td>
                                <td>10KG</td>
                                <td>100KG</td>
                                <td>500KG</td>
                                <td>1000KG</td>
                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table> 
                    <!--<div class="layui-elem-quote" style="padding: 5px 0;">-->
                        <!--&nbsp;&nbsp;查询到设备总数：<span class="len"></span>-->
                        <!--&nbsp;&nbsp; &nbsp;&nbsp;<div id="demo10" style="display: inline-block;"></div>-->
                    <!--</div>-->
                </div>
                <!-- 点线图 -->
                <div id="point_line_num" class="hide"  style="width: 600px;height:400px;">

                </div>
                <!-- 班组类比 饼状图 -->
                <div  id="bing_num" class="hide" style="width: 600px;height:400px;">
                    
                </div>
            </div>
        </div>
    </div>   
</body>

<script src="/static/common/js/jquery-2.1.4.min.js"></script>

<!-- layui 1.0.9版本 -->
<script type="text/javascript" src="/static/admin/layui/layui.js"></script>
<!-- laydate v-5.0.9 -->
<script src="/static/admin/layui/lay/modules/laydate/laydate.js"></script>
<script src="/static/common/js/echarts.common.min.js"></script>
<script src="/static/admin/js/trash_number.js"></script>
</html>