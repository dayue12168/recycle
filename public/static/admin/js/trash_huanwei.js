layui.use(['element','layer'], function(){
    var layer   = layui.layer,
        element = layui.element;
		
	
		
    // 绑定信息确认
	function bindinfo(trash,obj,nid,i){

    // trashNum_tr
		if(i == 2){
			trash.on('click',function(){
			  var trashNum = $(".trash_num").html();
			  obj.html(trashNum);
				var _nid = $(this).find(".huanwei").attr("nid")
			  nid.html(_nid)
			})
		}else{
      // huanwei_tr
			trash.on('click',function(){
			  var trashNum = $(this).find(".Imei").html();
			  obj.html(trashNum);
			
			})
		}
      
    };
              
    var trash = $(".trashNum_tr"),bind1 = $(".bind1"),hwer = $(".huanwei_tr"),bind2 = $(".bind2"),nid = $(".nid");
    bindinfo(trash,bind1,null,1);
    bindinfo(hwer,bind2,nid,2);

    // 绑定按钮
    $(".Bind").on('click',function(){

      var bind1 = $('.bind1').text().trim();
      var bind2 = $('.bind2').text().trim();
	  var nid = $(".nid").text().trim();
      if(bind1 && bind2 != ""){
        $.ajax({
            url:"/admin/index/bind",
            type:"post",
            data:{"trash":bind1,"user":nid},
            success:function(data){
                // console.log(data);
                if(data !== false){
                  layer.msg("绑定成功！");
                  $(".bind1").html("");
                  $(".bind2").html("");
                }
            }
        })
      }
    })

    // 垃圾桶绑定信息------环卫工绑定信息
    function managerInfo(title,obj){
      var index = layer.open({
        title: title,
        content: obj,
        btn:'关闭窗口',
        btnAlign:'c',
        area:['500px','400px'],
        yes:function(){

          layer.close(index);
        }
      })
    }
		// 生成table 弹窗
		function oTable(){
			return 
		}
    // 垃圾桶管理
    $(".trashManager").on('click',function(){
        var trash = $(this).parents("tr").find(".trash_num").text();
        var imei = $(this).parents("tr").find("td").eq(1).text();
				var loading = layer.load(1, {
					shade: [0.1,'#fff'] //0.1透明度的白色背景
				});
        $.ajax({
            url:"/admin/index/getTrashs",
            data:{"imei":imei},
            type:"post",
            success:function(data){
							layer.close(loading);
							var title = "垃圾桶编号:"+trash;
							var oTable = "";
							oTable += '<table class="layui-table oTable1" style="margin-top: 0">';
							oTable += '<colgroup>';
							oTable += '<col width="130"><col width="130"><col width="130">';
							oTable += '</colgroup>';         
							oTable += '<thead >';
							oTable += '<tr >';
							oTable += '<th>环卫工姓名</th><th>所属班组</th><th>操作</th>';
							oTable += '</tr></thead>';
							oTable += '<tbody >';
							for(var i = 0; i< data.length; i++){
								oTable += '<tr><td>'+data[i].worker_name+'</td><td>'+data[i].belong_user_id+'</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-mini unbind_hw">解绑</button></td></tr>';
								}
							oTable += '</tbody></table>';
							managerInfo(title,oTable)
            }
        })
      

      
    })
    // 环卫工管理
    $(".hwManager").on('click',function(){
        var worker_id=$(this).parents("tr").find(".huanwei").attr("nid");
				var loading = layer.load(1, {
					shade: [0.1,'#fff'] //0.1透明度的白色背景
				});
		var that = $(this);
        $.ajax({
            url:"/admin/index/trashByWorker",
            type:"post",
            data:{"worker_id":worker_id},
            success:function(data){
							layer.close(loading);
							var title = "环卫工姓名"+that.parents("tr").find(".huanwei").text();
							var oTable = "";
							oTable += '<table class="layui-table oTable1" style="margin-top: 0">';
							oTable += '<colgroup>';
							oTable += '<col width="130"><col width="130"><col width="130">';
							oTable += '</colgroup>';         
							oTable += '<thead >';
							oTable += '<tr >';
							oTable += '<th>垃圾桶编号</th><th>设备IMEI号</th><th>操作</th>';
							oTable += '</tr></thead>';
							oTable += '<tbody >';
							for(var i = 0 ; i< data.length; i++){
								oTable += '<tr><td>'+data[i].dust_serial+'</td><td>'+data[i].cap_imei+'</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-mini unbind_trash">解绑</button></td></tr>';
							}
							oTable += '</tbody></table>';
              managerInfo(title,oTable)
            }
        });
      
      
    })



})