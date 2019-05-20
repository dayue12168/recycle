layui.use('form', function() {
    var form = layui.form();
    form.on("select(address)",function(data){
        var _val = data.value;
        var child=$(this).parent().parent().nextAll('select');
        $.ajax({
            url:"/admin/Address/getChildAddr",
            type:"post",
            data:{'addr':_val},
            cache:false,
            success:function(res){
                $.each(child,function(index,ele){
                    $(this).children().remove('option');
                    for(var i in res){
                        var data=res[i];
                        for(var j in data){
                            var options = "<option value='"+data[j].area_id+"'>"+data[j].area_name+"</option>";
                            if(index==i){
                                $(this).append(options);
                                form.render();
                            }
                        }
                    }
                });
            }
        });
    });
    form.render();
});


