// 环卫工接收消息列表------------------------------------------------------------------------
layui.use('element', function(){
    var element = layui.element();  //依赖
  });
  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;
          laypage({
              cont: 'demoTwo'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  })
  layui.use("form",function(){
      var form = layui.form();
  
  })
  // <!-- 发送时间 -->
  layui.use('laydate', function(){
      var laydate = layui.laydate;
      laydate.render({
          elem:"input[name='startTime']",
      })
      laydate.render({
          elem:"input[name='endTime']",
      })
  });
  var len = $(".tbody>tr").length;     
  $(".len").html(len);
