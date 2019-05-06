// 角色管理 -----------------------------------------------------------------------------------
layui.use('element', function(){
var element = layui.element();  //依赖
});
// <!-- 分页 -->
layui.use(["layer",'laypage'],function(){
  var layer = layui.layer,
      laypage = layui.laypage;

});
layui.use("form",function(){
  var form = layui.form();

});
// <!-- 新建角色 -->
$(".new_role").click(function(){
  layer.prompt({
      formType: 0,
      value: '角色名',
      title: '新建角色',
      },function(value, index, elem){
          $.ajax({
              url:"/admin/user/addRole",
              type:"POST",
              data:{"name":value},
              cache:false,
              success:function(res){
                  // console.log(res);
                layer.msg(res.msg);
                if(res.state){
                    var _btn = "<button class='layui-btn layui-btn-primary layui-btn-small maTop_10 roles' data-to='"+res.state+"'>"+res.name+'</button>';
                    $(".rol_btn").append(_btn);
                }
                layer.close(index);
              }
          })
  });
});
$('.rol_btn').on('click','.roles',function(){
    var _html = $(this).html();
    var _prop = $(this).attr("data-to");
    $(".role_chooes").html(_html).attr("data-to",_prop);
    // $(".role_del").attr("data-to",_prop);

    //去查询出已经拥有的权限列表
    $.ajax({
        url:"/admin/user/getAuthById",
        type:"POST",
        data:{"role":_prop},
        cache:false,
        success:function(res){
            // console.log(res.length);
            var checkList=$('.layui-form-item>.Jauth');
            $.each(checkList,function(i,v){
                $(this).next().removeClass("layui-form-checked")
            })
            if(res[0]!=''){
                $.each(checkList,function(i,v){
                    // console.log($(this));
                    for(var j = 0;j<res.length;j++){
                        if(res[j] == $(this).attr("data-to")){
                            $(this).next().addClass("layui-form-checked");
                        }
                    }
                })
            }
        }
    });
});
$('.role_del').click(function(){
    var id=$('.role_chooes').attr('data-to');
    if(typeof id=='undefined'){
        layer.msg('请选择角色');
        return false;
    };
    var name=$('.role_chooes').text();
    layer.open({
        type:1,
        title:"删除角色:"+name,
        btn:["确定","取消"],
        yes:function(index){
            $.ajax({
                url:"/admin/user/delrole",
                type:"post",
                data:{'id':id},
                cache:false,
                success:function(res){
                    if(res){
                        var butDiv=$('.rol_btn').children('button');
                        $.each(butDiv,function(i,v){
                            if($(this).attr('data-to')==id){
                                $(this).remove();
                            }
                        })
                        $("button.role_chooes").removeAttr('data-to').text('请选择角色');
                        layer.msg('删除角色成功');
                    }else{
                        layer.msg('角色已有绑定，不能删除');
                    }
                }
            });
            layer.close(index);
        }
    });
})

$('.role_set').click(function(){
    var id=$('.role_chooes').attr('data-to');
    if(typeof id=='undefined'){
        layer.msg('请选择角色');
        return false;
    };
    //substring 方法用于提取字符串中介于两个指定下标之间的字符；substr 方法用于返回一个从指定位置开始的指定长度的子字符串

    var checked=$('div.layui-form-checked');
    if(!checked.length){
        layer.msg('请选择权限');
        return false;
    }
    var auths;
    $.each(checked,function(){
        auths+=','+$(this).prev('input').attr('data-to');
        // console.log($(this).prev('input').attr('data-to'));
    });
    var sub='undefined,';
    auths=auths.substring(sub.length,auths.length);
    $.ajax({
        url:"/admin/user/updateRole",
        type:"post",
        data:{"id":id,"auths":auths},
        cache:false,
        success:function(res){
            layer.msg('权限设置成功');
        }
    });
});