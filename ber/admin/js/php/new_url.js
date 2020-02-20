layui.use('form', function () {
    var form = layui.form;
    form.on('submit(go)', function (data) {
        if (data.field.url===''){
            layer.msg('请输入长链');
            return false;
        }
        if (data.field.user===''){
            layer.msg('请选择用户');
            return false;
        }
        if (data.field.type===''){
            layer.msg('请选择短链属性');
            return false;
        }
        $.post("ajax.php", {mode: 'new_url', url: data.field.url, coded:data.field.coded, user:data.field.user,type:data.field.type},
            function (data) {
                if (data.code === 200) {
                    layer.msg('添加成功');
                } else {
                    layer.msg(data.error);
                    return false;
                }
            }
        );
    });
});