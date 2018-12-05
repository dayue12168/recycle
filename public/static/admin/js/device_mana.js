// 设备管理 ------------------------------------------------------------------

  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;
          laypage({
              cont: 'demoOne'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  });
  layui.use("form",function(){

      var form = layui.form();
      
      // 
      // 查询设备
      // form.on("checkbox(sb)",function(data){
      //   var len = $(".set_sb>input:checked").length
      //     if(len>0){
      //       $(".query_SB").show();
      //     }else{
      //       $(".query_SB").hide();
      //     }
      // })
      // form.on("radio(sb)",function(data){
      //     $(".query_SB").show();
      // });

      $("button.query_SB").click(function(){
          var type=$(this).prev().find('.layui-this').attr('lay-value');
          if(!type){
              layer.msg('请选择设备类型！');
              return false;
          }
          var status=$('input[name="sb"]:checked').val();
          var addr='';
          if(status==0){
              var cityVal = $('select[name="city_g"] option:selected').val();
              var areaVal = $('select[name="area_g"] option:selected').val();
              var streetVal = $('select[name="street_g"] option:selected').val();
              addr=cityVal+','+areaVal+','+streetVal;
          }
          $.ajax({
              url:"/admin/index/queryDevice",
              type:"post",
              data:{'type':type,'status':status,'addr':addr},
              cache:false,
              success:function(res){
                  // console.log(res);
                  // return false;
                  var tb=$('tbody.tbody');
                  var str='';
                  for(var i in res){
                      if(res[i].status){
                          var status='启用'
                      }else{
                          var status='禁用'
                      }
                      str+='<tr><td>'+res[i].cap_imei+'</td><td>'
                          +res[i].cap_imsi+'</td><td>'
                          +res[i].cap_serial+'</td><td>'
                            +res[i].cap_type+'</td><td>'+res[i].cap_sim+'</td><td>'
                            +res[i].cap_position+'</td><td>'+res[i].address+'</td><td>' +
                          '<button type="button" class="layui-btn layui-btn-normal layui-btn-small reSet">修改</button>' +
                          '<button type="button" class="layui-btn layui-btn-danger layui-btn-small">解除绑定</button>' +
                          '<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jforbid">'+status+'</button>' +
                          '</td><td style="display: none">'+res[i].cap_id+'</td></tr>';
                      // console.log(str);

                  }
                  str.substring(1);
                  tb.html(str);

              },error:function(){
                  layer.msg('请确保查询条件完整');
              }


          });
      });



      // 添加设备
      $("button.add_SB").click(function(){
          var streetVal = $('select[name="street_g"] option:selected').val();
          if(streetVal<0){
              layer.msg('请选择具体街道');
              return false;
          }
          var cityVal = $('select[name="city_g"] option:selected').val();
          var areaVal = $('select[name="area_g"] option:selected').val();
          $("input[name='city']").attr('value',cityVal);
          $("input[name='area']").attr('value',areaVal);
          $("input[name='street']").attr('value',streetVal);
          layer.open({
              type:1,
              title:"添加设备",
              btn:["确定","取消"],
              area:["400px","450px"],
              content:$("#addSB"),
              yes:function(index){
                  var data=$('#addForm').serialize();
                  $.ajax({
                      url:"/admin/index/addDevice",
                      type:"POST",
                      // data:{"addr":addr,"data":data},
                      data:data,
                      cache:false,
                      success:function(res){
                          // console.log(res);
                          // return false;
                          var tbody=$('tbody.tbody');
                          var str='<td>'+res.cap_imei+'</td><td>' + res.cap_imsi+'</td><td>';
                          str+=res.cap_serial+'</td><td>'+res.cap_type+'</td><td>'+res.cap_sim+'</td>';
                          str+='<td>'+res.cap_position+'</td><td>'+res.city+'-'+res.area+'-'+res.street+'</td><td>';
                          str+='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reSet">修改</button>';
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small">解除绑定</button>';
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small">禁用</button>';
                          str+='</td><td style="display: none">'+res.cap_id+'</td></tr>';
                          tbody.append(str);
                          layer.msg('添加设备'+res.cap_imei+'成功');
                      },error:function(){
                          layer.msg('添加设备失败，请检查信息填写');
                      }
                  })
                  layer.close(index);
              }
          })
      });

      // 修改
      $("button.reSet").click(function(){
          // 类型  编号 IMEI IMSI SIM 位置
          $("select.reset_type").find("option[value='0']").remove();
          var oNum = $(".reset_number"),
              oImei =$(".reset_IMEI"),
              oImsi =$(".reset_IMSI"),
              oSim = $(".reset_SIM"),
              oPos = $(".reset_position");
              oid = $(".Jid");
          var _html = [];
          $(this).parent().siblings().each(function(){
              var SBinfo = $(this).html();
              _html.push(SBinfo);        
          });
          var option = "<option value='0' selected>"+_html[3]+"</option>";
          $(".reset_type").prepend(option);
          form.render('select'); 
          // oType.text(_html[3]);
          oNum.val(_html[2]);
          oImei.val(_html[0]);
          oImsi.val(_html[1]);
          oSim.val(_html[4]);
          oPos.val(_html[5]);
          oid.val(_html[7]);
          var that=$(this);
          layer.open({
              type:1,
              title:"设备信息修改",
              btn:["确定","取消"],
              area:["400px","450px"],
              content:$("#resetSB"),
              yes:function(index){
                  var data=$('#resetForm').serialize();
                  // console.log(data);
                  $.ajax({
                      url:"/admin/index/updateDevice",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res){
                          // console.log(that);
                          // console.log(res);
                          that.parent().prevAll().eq(6).text(res.cap_imei);
                          that.parent().prevAll().eq(5).text(res.cap_imsi);
                          that.parent().prevAll().eq(4).text(res.cap_serial);
                          that.parent().prevAll().eq(3).text(res.cap_type);
                          that.parent().prevAll().eq(2).text(res.cap_sim);
                          that.parent().prevAll().eq(1).text(res.cap_position);
                            layer.msg('信息修改成功');
                      },error:function(){
                          layer.msg('信息修改失败，请检查信息是否正确');
                      }
                  });
                  layer.close(index);
              }
          })
      });

      //禁用设备
      $('button.Jforbid').click(function(){
          var temp=$(this).text();
          var forbid='禁用';
          var use='启用';
          var id=$(this).parent().next().text();
          if(temp==forbid){
              var status=1;
              temp=use;
          }else{
              var status=0;
              temp=forbid;
          }
          var that=$(this);
          $.ajax({
              url:'/admin/index/setToggle',
              type:'post',
              data:{'id':id,'status':status},
              cache:false,
              success:function(res){
                  that.text(temp);
              },
              error:function(){
                  layer.msg('修改失败');
              }

          })
      })
  })
  var len = $(".tbody>tr").length;     
  $(".len").html(len);


