//    消息管理------------------------------------------------------------------------------
layui.use('element', function(){
    var element = layui.element();  //依赖
  });
  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;
          laypage({
              cont: 'demo3'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  })
  layui.use("form",function(){
      var form = layui.form();
      // 添加设备
      $(".add_manager").click(function(){
          layer.open({
              type:1,
              title:"添加管理员",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#addManager"), 
              yes:function(index){
                  // $.ajax({
                  //     url:"",
                  //     type:"POST",
                  //     data:"",
                  //     cache:false,
                  //     success:function(res){
  
                  //     }
                  // })
  
                  layer.close(index);
              }
          })
      })
      // 修改
      $(".reset_manager").click(function(){
          form.render('select'); 
  
          layer.open({
              type:1,
              title:"管理员信息修改",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#resetManager"),
              yes:function(index){
                  $.ajax({
                      url:"",
                      type:"POST",
                      data:"",
                      cache:false,
                      success:function(res){
  
                      }
                  })
                  layer.close(index);
              }
          })
      })
  })
  var len = $(".tbody>tr").length;     
  $(".len").html(len);
