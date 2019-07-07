layui.use(['element','layer'], function(){
    var layer   = layui.layer,
        element = layui.element;

    // 绑定信息确认
    function bindinfo(trash,obj){
      trash.on('click',function(){
        var trashNum = $(this).html();
        obj.html(trashNum);
      })
    };
              
    var trash = $(".trash_num"),bind1 = $(".bind1"),hwer = $(".huanwei"),bind2 = $(".bind2");
    bindinfo(trash,bind1);
    bindinfo(hwer,bind2);

    // 绑定按钮
    $(".Bind").on('click',function(){
      var bind1 = $('.bind1').text().trim();
      var bind2 = $('.bind2').text().trim();
      if(bind1 && bind2 != ""){
        layer.msg("绑定成功")
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
    // 垃圾桶管理
    $(".trashManager").on('click',function(){
        var trash = $(this).parents("tr").find(".trash_num").text();
        var imei = $(this).parents("tr").find("td").eq(1).text();
        $.ajax({
            url:"/admin/index/getTrashs",
            data:{"imei":imei},
            type:"post",
            success:function(data){
                console.log(data);
            }
        })
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
      oTable += '<tr><td>123123</td><td>123123</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-mini unbind_hw">解绑</button></td></tr>';
      oTable += '</tbody></table>';
      managerInfo(title,oTable)
    })
    // 环卫工管理
    $(".hwManager").on('click',function(){
      var title = "环卫工姓名"+$(this).parents("tr").find(".huanwei").text();
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
      oTable += '<tr><td>123123</td><td>123123</td><td><button type="button" class="layui-btn layui-btn-normal layui-btn-mini unbind_trash">解绑</button></td></tr>';
      oTable += '</tbody></table>';
      managerInfo(title,oTable)
    })
})