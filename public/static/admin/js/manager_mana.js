//  管理员管理 ----------------------------------------------------------------------------------------
layui.use('element', function(){
    var element = layui.element();  //依赖
  });
  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;
          laypage({
              cont: 'demo5'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  })
  layui.use("form",function(){
      var form = layui.form();
      // 添加管理员
      $("button.add_manager").click(function(){
          layer.open({
              type:1,
              title:"添加管理员",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#addManager"), 
              yes:function(index){
                  var streetVal = $('#addManager select[name="street_g"] option:selected').val();
                  var groupVal = $('#addManager select[name="group_g"] option:selected').val();
                  if(streetVal<0||groupVal<0){
                      layer.msg('请确定地址完整');
                      return false;
                  }
                  var data=$("#ManaForm").serialize();
                  $.ajax({
                      url:"/admin/user/addAdmin",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res){
                          var tbody=$('.tbody');
                          var str='<td style="display: none">'+res.user_id+'</td><td>'+res.user_name+'</td><td>' + res.tel+'</td><td>';
                          str+=res.user_type+'</td><td>'+res.area_id0+'-'+res.area_id1;
                          str+='-'+res.area_id2+'-'+res.area_id3+'</td><td>'+res.role_id+'</td><td>';
                          str+=res.last_login_time+'</td><td>'+res.wx_band+'<td>';
                          str+='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_manager">修改</button>';
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small">解绑微信</button>';
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small reset_forbid">禁用</button>';
                          str+='</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-small">环卫工列表</button>';
                          str+='</td></tr>';
                          tbody.append(str);
                          layer.msg('添加管理'+res.user_name+'成功');
                      },
                      error:function(){
                          layer.msg('请检查您的信息是否完整、电话号码是否已注册');
                      }
                  })
                  layer.close(index);
              }
          })
      });
      // 修改管理员信息
      $(".tbody").on('click','.reset_manager',function(){
          form.render('select');
          //直接赋值
          var id=$(this).parent().prevAll().eq(7).text();
          $("[name='Jid']").attr('value',id);
          var name=$(this).parent().prevAll().eq(6).text();
          $("[name='Jname']").attr('value',name);
          var tel=$(this).parent().prevAll().eq(5).text();
          $("[name='Jtel']").attr('value',tel);
          var that=$(this);
          layer.open({
              type:1,
              title:"管理员信息修改",
              btn:["确定","取消"],
              area:["600px","450px"],
              content:$("#resetManager"),
              yes:function(index){
                  var streetVal = $('#resetManager select[name="street_g"] option:selected').val();
                  var groupVal = $('#resetManager select[name="group_g"] option:selected').val();
                  if(streetVal<0||groupVal<0){
                      layer.msg('请确定地址完整');
                      return false;
                  }
                  var data=$("#re_manaForm").serialize();
                  $.ajax({
                      url:"/admin/user/updateAdmin",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res){
                          // console.log(res);
                          that.parent().prevAll().eq(6).text(res.user_name);
                          that.parent().prevAll().eq(5).text(res.tel);
                          that.parent().prevAll().eq(4).text(res.user_type);
                          that.parent().prevAll().eq(3).text(res.area_id0+'-'+res.area_id1+'-'+res.area_id2+'-'+res.area_id3);
                          that.parent().prevAll().eq(2).text(res.role_id);
                          layer.msg('修改管理'+res.user_name+'成功');
                      },
                      error:function(){
                          layer.msg('请检查您的信息是否完整、电话号码'+tel+'是否已注册');
                      }
                  });
                  layer.close(index);
              }
          })
      });

      //禁用/启用管理员
      $(".tbody").on('click','.reset_forbid',function(){
              var temp=$(this).text();
              var forbid="禁用";
              var use='启用';
              var id=$(this).parent().prevAll().last().text();
              var that=$(this);
              if(temp==forbid){
                  temp=use;
                  var state=1;
              }else{
                  temp=forbid;
                  var state=0;
              }
              $.ajax({
                  url:'/admin/user/setToggle',
                  type:'post',
                  data:{'id':id,'state':state},
                  cache:false,
                  success:function(res){
                      console.log(res);
                      that.text(temp);
                  },
                  error:function(){
                      layer.msg('修改失败');
                  }
              });
          });

      //查询管理员
      $("button.query_manager").click(function(){
          var types=$("input[type='checkbox']:checked");
          var str='';
          $.each(types,function(ele,index){
              str+=','+index.value;
          });
          if(types.length){
              str=str.substring(1);
          }
          var cityVal = $('select[name="city_g"] option:selected').val();
          var areaVal = $('select[name="area_g"] option:selected').val();
          var streetVal = $('select[name="street_g"] option:selected').val();
          var addr=cityVal+','+areaVal+','+streetVal;
          $.ajax({
             url:"/admin/user/getAdmin",
              type:"post",
              cache:false,
              data:{'type':str,"addr":addr},
              success:function(res){
                 var str='';
                 for(var i in res){
                     var type=res[i].user_type==0?'系统管理员':'商户管理员';
                     var forbid=res[i].state==0?'禁用':'启用';
                     str+='<tr><td style="display: none">'+res[i].user_id+'</td><td>'+res[i].user_name+'</td>' +
                         '<td>'+res[i].tel+'</td><td>'+type+'</td><td>'+res[i].city+'-'+res[i].area+
                         '-'+res[i].street+'-'+res[i].group+'</td><td>'+res[i].role_name+'</td>' +
                         '<td>'+res[i].last_login_time+'</td><td>'+res[i].wx_band+'</td>' +
                         '<td><button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_manager">修改</button>' +
                         '<button type="button" class="layui-btn layui-btn-danger layui-btn-small">解绑微信</button>' +
                         '<button type="button" class="layui-btn layui-btn-danger layui-btn-small reset_forbid">'+forbid+'</button>' +
                         '</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-small check_huan">环卫工列表</button>' +
                         '</td></tr>';
                 }
                 $("tbody.tbody").html(str);
              }
          });
      });

  });
  var len = $(".tbody>tr").length;     
  $(".len").html(len);