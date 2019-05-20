
//   垃圾桶管理-------------------------------------------------------------------------------
layui.use('element', function(){
    var element = layui.element();  //依赖
  });
  // <!-- 分页 -->
  layui.use(["layer",'laypage'],function(){
      var layer = layui.layer,
          laypage = layui.laypage;


          laypage({
              cont: 'demo9'
              ,pages: 100 //总页数
              ,groups: 5 //连续显示分页数
          });
  })
  layui.use("form",function(){
      var form = layui.form();
      // 添加垃圾桶
      $(".add_trash").click(function(){
          layer.open({
              type:1,
              title:"添加垃圾桶",
              btn:["确定","取消"],
              area:["500px","480px"],
              content:$("#addTrash"), 
              yes:function(index){
                  var data=$('#JaddTrash').serialize();
                  // console.log(data);
                  $.ajax({
                      url:"/admin/index/addTrash",
                      type:"POST",
                      data:data,
                      cache:false,
                      success:function(res){
                            // console.log(res);
                            var tbody=$('tbody.tbody');
                            var list='<tr><td class="bind_id" style="display: none">'+res.dustbinId+'</td><td>'+res.dust_serial
                                +'</td><td><span class="city">'+res.city+'</span>-<span class="area">'+res.area+'</span>-'
                                +'<span class="street">'+res.street+'</span></td><td>'+res.dust_length+'*'+res.dust_width
                                +'*'+res.dust_height+'</td><td>'+res.longitude+','+res.latitude+'</td><td></td>' +
                                '<td><button type="button" class="layui-btn layui-btn-danger layui-btn-small Jbind">绑定</button>' +
                                '</td><td><span>0</span><button type="button" class="layui-btn layui-btn-normal layui-btn-small  trashMana">管理</button></td>' +
                                '<td><button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_trash">修改</button></td></tr>';
                            tbody.append(list);
                            layer.msg('垃圾桶'+res.dust_serial+'添加成功');
                            form.render();
                      }
                  })
                  layer.close(index);
              }
          })
      })
      // 修改
      $(".layui-table").on("click","button.reset_trash",function(){
          var id = $(this).parent("td").siblings(".bind_id").text();
          var that=$(this);
          $.ajax({
              url:"/admin/index/getTrash",
              type:"POST",
              data:{"id":id},
              cache:false,
              success:function(res){
                // console.log(res);
                //垃圾桶id
                $("input[name='Jserial']").val(res.dust_serial);
                // 垃圾桶编号
                $("input[name='id']").val(id);
                // 地址
                $("input[name='Jaddress']").val(res.dust_address);
                // 经度
                $("input[name='Jlongitude']").val(res.longitude);
                // 维度
                $("input[name='Jlatitude']").val(res.latitude);
                // 高德坐标
                $("input[name='Jgps_gd']").val(res.gps_gd);
                // 长
                $("input[name='Jlength']").val(res.dust_length);
                // 宽
                $("input[name='Jwidth']").val(res.dust_width);
                // 高
                $("input[name='Jheight']").val(res.dust_height);
                // 设备安装高度install_height
                $("input[name='install_height']").val(res.install_height);
                // 联通编号
                $("input[name='union_serial']").val(res.union_serial);
                form.render();
              },
              complete:function(){
                layer.open({
                    type:1,
                    title:"设备信息修改",
                    btn:["确定","取消"],
                    area:["400px","450px"],
                    content:$("#resetTrash"),
                    yes:function(index){
                   // 区域   市  区街道 垃圾桶编号 地址 经度 维度 高德坐标 长 宽 高 设备安装高度 联通编号
                      var serializeForm = $("#JresetTrash").serialize();
                        var streetVal = $('#JresetTrash select[name="street_g"] option:selected').val();
                        if(streetVal<0){
                            layer.msg('请确定地址完整');
                            return false;
                        }
                        $.ajax({
                            url:"/admin/index/updateTrash",
                            type:"POST",
                            data:serializeForm,
                            cache:false,
                            success:function(res){
                                // console.log(res);
                                that.parent().prevAll().eq(6).text(res.dust_serial);
                                that.parent().prevAll().eq(5).text(res.city+'-'+res.area+'-'+res.street);
                                that.parent().prevAll().eq(4).text(res.dust_length+'*'+res.dust_width+'*'+res.dust_height);
                                that.parent().prevAll().eq(3).text(res.longitude+','+res.latitude);
                                layer.msg('信息修改成功');
                            }
                        })
                        layer.close(index);
                    }
                });
              }
          })
      });


      //查询垃圾桶
      $("button.query_trash").click(function(){
          var checked=$("input[type='checkbox']:checked");
          var str='';
          $.each(checked,function(ele,index){
              str+=','+index.value;
          });
          var type=str.substring(1);
          var cityVal = $('select[name="city_g"] option:selected').val();
          var areaVal = $('select[name="area_g"] option:selected').val();
          var streetVal = $('select[name="street_g"] option:selected').val();
          var groupVal = $('select[name="group_g"] option:selected').val();
          var addr=cityVal+','+areaVal+','+streetVal+','+groupVal;
          // console.log(addr+'---'+type);
          $.ajax({
              url:"/admin/index/queryTrash",
              type:"post",
              data:{'type':type,'addr':addr},
              cache:false,
              success:function(res){
                  // console.log(res);
                  // return false;
                  var tb=$("tbody.tbody");
                  var str='';
                  for(var i in res){
                      if(res[i].cap_id){
                          var td='<button type="button" class="layui-btn layui-btn-danger layui-btn-small Junbind">解绑</button>';
                      }else{
                          var td='<button type="button" class="layui-btn layui-btn-danger layui-btn-small Jbind">绑定</button>';
                      }
                      str +='<tr><td class="bind_id" style="display: none">'+res[i].dustbin_id+'</td> <td>'+res[i].dust_serial
                          +'</td><td><span class="city">'+res[i].city+'</span>-<span class="area">'+res[i].area+'</span>-<span class="street">'
                          +res[i].street+'</span>' +'</td><td>'+res[i].dust_length+'*'+res[i].dust_width+'*'+res[i].dust_height
                          +'</td><td>'+res[i].longitude+','+res[i].latitude+'</td><td>'+res[i].cap_imei+'</td><td>'+td+'</td>' +
                          '<td><span>'+res[i].count+'</span>' +
                          '<button type="button" class="layui-btn layui-btn-normal layui-btn-small  trashMana">管理</button>' +
                          '</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-small reset_trash">修改</button>' +
                          '</td></tr>';
                  }
                  // console.log(str);
                  tb.html(str);
                  form.render();
              }
          })
      });

      //管理垃圾桶
      $(".layui-table").on('click','.trashMana',function(){
          var id=$(this).parent().prevAll().eq(6).text();
          window.open('trash_huanwei.html');

          // window.location.href = "trash_huanwei.html?id="+id;
      });


      //解绑垃圾桶
      $(".layui-table").on('click','.Junbind',function(){
          var id=$(this).parent().prevAll().eq(5).text();
          var that=$(this);
          layer.open({
              type:1,
              title:"解绑设备",
              btn:["确定","取消"],
              yes:function(index){
                  $.ajax({
                      url:'/admin/index/freeTrash',
                      type:'post',
                      data:{'id':id},
                      cache:false,
                      success:function(){
                          that.removeClass('Junbind').addClass("Jbind").text('绑定');
                          that.parent().prev().text('');
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

    // 设备操作 
    // 弹出层 table列表
    $(".layui-table").on('click','.Jbind',function(){
        var seri=$(this).parent().siblings().eq(1).text();
        $("input[name='hide_inp']").val(seri);
        var id=$(this).parent().siblings().eq(0).text();
        $("input[name='hide_trash']").val(id);
        imeithat = $(this).parent().siblings().eq(5);
        layer.open({
        title:"垃圾桶-设备绑定->设备查找",
        type: 1,
        area: ['800px', '450px'],
        fixed: false, //不固定
        maxmin: true,
        content: $("#G_bind_sb")
      });
    })
    // 绑定
   $(".layui-table").on('click','.bind_sb',function(){
       var imei=$(this).parent().prevAll().eq(5).text();
       var serial=$("input[name='hide_inp']").val();
       var id=$(this).parent().prevAll().eq(6).text();
       var trash=$("input[name='hide_trash']").val();


    var _html = "<div style='padding:10px'><p>设备IMEI号&nbsp;&nbsp;&nbsp;&nbsp;<span>"+imei+"</span></p>"
                +"<p>垃圾桶编号&nbsp;&nbsp;&nbsp;&nbsp;<span>"+serial+"</span></p>"
                +"设备安装高度&nbsp;&nbsp;<input type='text' name='i_height'></div>";
      layer.open({
        title:"垃圾桶-设备绑定->确认绑定",
        type: 1,
        area: ['300px', '200px'], //宽高
        content: _html,
        btn:["确定"],
        btnAlign:'c',
        yes:function(index){
          var iHeight = $("input[name='i_height']").val().trim();
          // 设备安装高度必填
            if(iHeight == 0){
              layer.msg("设备安装高度请填写完整",{time:1500})
            }else{
              $.ajax({
                  url:"/admin/index/trashDevice",
                  type:"post",
                  cache:false,
                  data:{'id':id,'trash':trash,'iHeight':iHeight},
                  success:function(res){
                      layer.closeAll(); //关闭所有层
                      layer.msg("绑定成功");
                      imeithat.text(imei);
                      imeithat.next().find('button').removeClass('Jbind').addClass('Junbind').text('解绑');
                  }
              })
            }
        }
      });
   });

      //查找设备
      $('#JqueryDevice').click(function(){
        var val=$('input[name="sb_IMEI"]').val();
        $.ajax({
           url:"/admin/index/getCapById",
            type:'post',
            cache:'false',
            data:{'id':val},
            success:function(res){
                var tbody=$('.Jcap');
                if(res.length>0){
                    var str='';
                    for(var i in res){
                        str +='<tr><td style="display:none;">'+res[i].cap_id+'</td>' +
                            '<td>'+res[i].cap_imei+'</td><td>'+res[i].cap_imsi+'</td>' +
                            '<td>'+res[i].cap_serial+'</td><td>'+res[i].cap_type+'</td>' +
                            '<td>'+res[i].cap_sim+'</td><td>'+res[i].cap_position+'</td>' +
                            '<td><button type="button" class=" layui-btn layui-btn-danger layui-btn-mini bind_sb">绑定</button>' +
                            '</td></tr>';
                    }
                }else{
                    str='';
                }

                tbody.html(str);
            }
        });
      });

  })
  var len = $(".tbody>tr").length;     
  $(".len").html(len);