//   区-街道-班组管理 ---------------------------------------------------------------------------------
layui.use('form', function() {
    var form = layui.form();
    form.on("select(address | add1)",function(data){
        console.log(data.value);
        console.log(data.elem.attr('name'));
        return false;


            var id=$(this).find("option:selected").val();
            var name1=$(this).prev().find("option:selected").html();
            var name=$(this).find("option:selected").html();
            $('#Jregion').html(name1+"--"+name);
            $('#Jregion').attr('Jdata',id);


        $("select[name='street_g']").on("change",function(){
            var id=$(this).find("option:selected").val();
            var name1=$(this).prev().find("option:selected").html();
            var name=$(this).find("option:selected").html();
            $('#Jroad').html(name1+"--"+name);
            $('#Jroad').attr('Jdata',id);
            $('#JJroad').text(name);
            $('#JJroad').attr('Jdata',id);
        })

        $("select[name='s_group']").on("change",function(){
            var id=$(this).find("option:selected").val();
            var name=$(this).find("option:selected").html();
            $('#JJgroup').attr('Jdata',id);
            $('#JJgroup').text(name);
            $('.flex-box flex-b').attr('Jdata',id);
        })
    });


layui.use('element', function(){
    var element = layui.element;  //依赖
  });
  layui.use("layer",function(){
      var layer = layui.layer;
  })

  $(".layui-edit").click(function(){
      var name = $(this).siblings().html();
      var id = $(this).prev().attr('jdata');
      var that = $(this);
      layer.prompt({
          formType: 0,
          value: name,
          title: "改名",
          }, function(value, index, elem){
              $.ajax({
                  url:"/admin/Address/updateName",
                  type:"POST",
                  data:{'id':id,'name':value},
                  cache:false,
                  success:function(res){
                      layer.msg(res.info);
                      that.siblings().html(value);
                      layer.close(index);
                  }
              })
      });
  });
  $(".layui-add1").click(function(){
      var _html = $(".tbody1");
      var id=$(this).prev().attr('Jdata');
      layer.prompt({
          formType:0,
          value:"街道名",
          title:"新增街道",
          },function(value,index,elem){
          // console.log(value);
          $.ajax({
              url:"/admin/address/addChild",
              type:"POST",
              data:{'id':id,'name':value},
              success:function(res){
                layer.msg(res.info);
                layer.close(index);
              }
          })

          /*var oTr = '<tr><td><span>'+value+'</span> <i class="layui-icon layui-edit">&#xe642;</i></td><td> <i class="layui-icon layui-add1">&#xe608;</i></td></tr>';
          _html.append(oTr);
          layer.close(index);*/
      });
  });

  $(".layui-add2").click(function(){
      var _html = $(".tbody2");
      var id=$(this).prev().attr('Jdata');
      layer.prompt({
          formType:0,
          value:"班组",
          title:"新增班组",
          },function(value,index,elem){
          $.ajax({
              url:"/admin/address/addChild",
              type:"POST",
              data:{'id':id,'name':value},
              success:function(res){
                  console.log(res);
                  layer.msg(res.info);
                  layer.close(index);
              }
          })

          // var oTr = '<tr><td><span>'+value+'</span> <i class="layui-icon layui-edit">&#xe642;</i></td><td> <i class="layui-icon layui-add2">&#xe608;</i></td></tr>';
          // _html.append(oTr);
          // layer.close(index);
      });
  })
});




