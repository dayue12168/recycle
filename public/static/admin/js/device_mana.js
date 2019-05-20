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

      // form.on("radio(sb)",function(data){
      //     $(".query_SB").show();
      // });
      // 查询设备
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
                      str+='<tr><td>'+res[i].cap_imei+'</td><td>'
                          +res[i].cap_imsi+'</td><td>'
                          +res[i].cap_serial+'</td><td>'
                            +res[i].cap_type+'</td><td>'+res[i].cap_sim+'</td><td>'
                            +res[i].cap_position+'</td><td>'+res[i].address+'</td><td>' +
                          '<button type="button" class="layui-btn layui-btn-normal layui-btn-small reSet">修改</button>';
                      if(res[i].cap_status==0){

                          var status='解除绑定';
                          str+= '<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jjiebang">'+status+'</button>';
                      }else{
                          if(res[i].cap_status==1){
                              var status='启用';
                          }else{
                              var status='禁用';
                          }
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jforbid">'+status+'</button>'
                      }
                      str+='</td><td style="display: none">'+res[i].cap_id+'</td></tr>';
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
                          var str='<tr><td>'+res.cap_imei+'</td><td>' + res.cap_imsi+'</td><td>';
                          str+=res.cap_serial+'</td><td>'+res.cap_type+'</td><td>'+res.cap_sim+'</td>';
                          str+='<td>'+res.cap_position+'</td><td>未绑定</td><td>';
                          str+='<button type="button" class="layui-btn layui-btn-normal layui-btn-small reSet">修改</button>';
                          str+='<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jforbid">禁用</button>';
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
      $(".layui-table").on("click",".reSet",function(){
          // 类型  编号 IMEI IMSI SIM 位置
          $("select.reset_type").find("option[value='0']").remove();
          var oNum = $(".reset_number"),
              oImei =$(".reset_IMEI"),
              oImsi =$(".reset_IMSI"),
              oSim = $(".reset_SIM"),
              oPos = $(".reset_position"),
              oid = $(".Jid");
          var _html = [];
          $(this).parent().siblings().each(function(){
              var SBinfo = $(this).html();
              _html.push(SBinfo);        
          });
          // console.log(_html);
          var option = "<option value='0' selected>"+_html[3]+"</option>";
          $(".reset_type").prepend(option);
          form.render('select'); 
          // oType.text(_html[3]);
          oNum.val(_html[2].trim());
          oImei.val(_html[0].trim());
          oImsi.val(_html[1].trim());
          oSim.val(_html[4].trim());
          oPos.val(_html[5].trim());
          oid.val(_html[7].trim());
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
      $(".layui-table").on('click','.Jforbid',(function(){
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
      }))


      //绑定设备解除绑定
      $(".layui-table").on('click','.Jjiebang',function(){
          var id=$(this).parent().next().text();
          var that=$(this);
          layer.open({
              type:1,
              title:"解绑设备",
              btn:["确定","取消"],
              yes:function(index){
                  $.ajax({
                      url:'/admin/index/freeDevice',
                      type:'post',
                      data:{'id':id},
                      cache:false,
                      success:function(){
                          // var _html='<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jforbid">禁用</button>';

                          that.removeClass('Jjiebang').addClass("Jforbid").text('禁用');
                          // that.html(_html);
                          layer.msg('解绑成功');
                      },
                      error:function(){
                          layer.msg('修改失败');
                      }
                  })
                  layer.close(index);
              }
          })

      })

  })
  var len = $(".tbody>tr").length;     
  $(".len").html(len);


