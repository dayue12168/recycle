
//   环卫工管理----------------------------------------------------------------------------
layui.use('element', function(){
    var element = layui.element();  //依赖
  });
  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;
          laypage({
              cont: 'demo8'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  })
  layui.use("form",function(){
      var form = layui.form();
      // 添加环卫工
      $(".add_trash").click(function(){
          layer.open({
              type:1,
              title:"添加环卫工",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#addManager_sa"), 
              yes:function(index){
                  var streetVal = $('#addManager_sa select[name="street_g"] option:selected').val();
                  var groupVal = $('#addManager_sa select[name="group_g"] option:selected').val();
                  if(streetVal<0||groupVal<0){
                      layer.msg('请确定地址完整');
                      return false;
                  }
                  var data=$("#addForm_sa").serialize();
                  $.ajax({
                      url:"/admin/user/addWorker",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res){
                          // console.log(res);
                          var str='';
                          var tb=$("tbody.tbody");

                          //有微信绑定
                          // for(var i in res){
                          //     if(res[i].wx_bind){
                          //         var wx='√';
                          //         var button='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_sani">修改</button>' +
                          //             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small wx_unbind">解绑微信</button>' +
                          //             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small trash_bind">垃圾桶绑定</button>' +
                          //             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small worker_for">禁用</button>';
                          //     }else{
                          //         var wx='?';
                          //         var button='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_sani">修改</button>' +
                          //             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small trash_bind">垃圾桶绑定</button>' +
                          //             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small worker_for">禁用</button>';
                          //     }
                          //     str+='<tr> <td style="display: none">'+res[i].worker_id+'</td><td>'+res[i].worker_name+'</td>' +
                          //         '<td>'+res[i].tel+'</td><td>'+res[i].area+'-'+res[i].street+'-'+res[i].group+'</td>' +
                          //         '<td>'+wx+'</td><td>'+res[i].user_name+'</td><td>'+res[i].count+'</td>' +
                          //         '<td>'+button +'</td></tr>';
                          // }

                          for(var i in res){
                              if(res[i].user_name===null){
                                  res[i].user_name='';
                              }
                              var button='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_sani">修改</button>' +
                                      '<button type="button" class="layui-btn layui-btn-danger layui-btn-small trash_bind">垃圾桶绑定</button>' +
                                      '<button type="button" class="layui-btn layui-btn-danger layui-btn-small worker_for">禁用</button>';
                              str+='<tr> <td style="display: none">'+res[i].worker_id+'</td><td>'+res[i].worker_name+'</td>' +
                                  '<td>'+res[i].tel+'</td><td>'+res[i].area+'-'+res[i].street+'-'+res[i].group+'</td>' +
                                  '<td>'+res[i].user_name+'</td><td>'+res[i].count+'</td>' +
                                  '<td>'+button +'</td></tr>';
                          }
                          tb.append(str);
                          layer.msg('用户添加成功');
                      }
                  })
                  layer.close(index);
              }
          })
      });
      // 修改
      $("tbody.tbody").on('click','.reset_sani',function(){
          var id=$(this).parent().siblings('td').eq(0).text();
          var name=$(this).parent().siblings('td').eq(1).text();
          var tel=$(this).parent().siblings('td').eq(2).text();
          $("input[name='id']").attr('value',id);
          $("input[name='name']").attr('value',name);
          $("input[name='tel']").attr('value',tel);
          // return false;
          var that=$(this);
          layer.open({
              type:1,
              title:"管理员信息修改",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#resetManager_sa"),
              yes:function(index){
                  form.render();
                  var streetVal = $('#addForm_reset select[name="street_g"] option:selected').val();
                  var groupVal = $('#addForm_reset select[name="group_g"] option:selected').val();
                  if(streetVal<0||groupVal<0){
                      layer.msg('请确定地址完整');
                      return false;
                  }
                  var data=$("#addForm_reset").serialize();
                  $.ajax({
                      url:"/admin/user/updateWorker",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res) {
                          // console.log(res);
                          that.parent().siblings().eq(1).text(res.worker_name);
                          that.parent().siblings().eq(2).text(res.tel);
                          that.parent().siblings().eq(3).text(res.area_id1 + '-' + res.area_id2 + '-' + res.area_id3);
                          layer.msg('信息修改成功');
                      }
                  })
                  layer.close(index);
              }
          })
      });

      //查询管理员
      $(".query_trash").click(function(){
          var cityVal = $('select[name="city_g"] option:selected').val();
          var areaVal = $('select[name="area_g"] option:selected').val();
          var streetVal = $('select[name="street_g"] option:selected').val();
          var addr=cityVal+','+areaVal+','+streetVal;
          $.ajax({
             url:"/admin/user/getWorks",
              type:"post",
              cache:false,
              data:{'addr':addr},
              success:function (res) {
                 // console.log(res);
                 var str='';
                 var tb=$("tbody.tbody");
                 for(var i in res){
                     if(res[i].user_name===null){
                         res[i].user_name='';
                     }
                     var button='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_sani">修改</button>' +
                             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small trash_bind">垃圾桶绑定</button>' +
                             '<button type="button" class="layui-btn layui-btn-danger layui-btn-small worker_for">禁用</button>';
                     str+='<tr> <td style="display: none">'+res[i].worker_id+'</td><td>'+res[i].worker_name+'</td>' +
                         '<td>'+res[i].tel+'</td><td>'+res[i].area+'-'+res[i].street+'-'+res[i].group+'</td>' +
                         '<td>'+res[i].user_name+'</td><td>'+res[i].count+'</td>' +
                         '<td>'+button +'</td></tr>';
                 }
                  tb.html(str);
              }
          });
      });
  })
  var len = $(".tbody>tr").length;     
  $(".len").html(len);