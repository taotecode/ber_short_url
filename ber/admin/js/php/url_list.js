layui.use(['table','form'], function() {
    var table = layui.table,form = layui.form;;
    table.render({
        elem: '#list'
        , url: 'api.php?mode=url_list'
        , toolbar: '#toolbar'
        , page : true
        , loading: true
        , title: '短链列表'
        , id: 'listReload'
        , cols: [[
            {field: 'id', title: 'ID', type: 'checkbox', fixed: 'left'}
            , {field: 'user', title: '用户', sort: true, templet: '#user',width : 80}
            , {field: 'ip', title: 'IP',width : 100}
            , {field: 'short', title: '短链'}
            , {field: 'url', title: '原链'}
            , {field: 'type', title: '类型', sort: true, templet: '#type',width: 90}
            , {field: 'visit', title: '流量',width : 80,sort:true}
            , {field: 'time', title: '时间', sort: true,width: 180}
            ,{fixed: 'right', title:'操作', toolbar: '#tool_bar', width:120}
        ]]
        ,initSort: {
            field: 'id'
            ,type: 'desc'
        }
        ,text: {
            none: '暂无相关数据'
        }
    });
    table.on('toolbar(list)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        switch (obj.event) {
            case 'new_url':
                layer.open({
                    type: 2,
                    title:'新建短链',
                    area: ['80%', '80%'],
                    moveOut: false,
                    content: 'new_url.php'
                });
                break;
            case 'del_all':
                var data = checkStatus.data;
                layer.confirm('确定要删除选中的短链吗？', function(index){
                    $.post("ajax.php", {mode: 'url_del_all',data:JSON.stringify(data)},
                        function (data) {
                            if (data.code === 200) {
                                layer.msg('已删除');
                                shuax();
                                layer.close(index);
                            } else {
                                layer.msg(data.error);
                                return false;
                            }
                        }
                    );
                });
                break;
            case 'Reload':
                layer.msg('已刷新');
                shuax();
                break;
        }
    });

    table.on('tool(list)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定要删除这个短链吗？', function(index){
                $.post("ajax.php", {mode: 'url_del', id: data.id},
                    function (data) {
                        if (data.code === 200) {
                            layer.msg('已删除');
                            obj.del();
                            shuax();
                            layer.close(index);
                        } else {
                            layer.msg(data.error);
                            return false;
                        }
                    }
                );
            });
        } else if(obj.event === 'edit'){
            $('#jb_short').text(data.short);
            $("#jb_ip").text(data.ip);
            $("#jb_visit").text(data.visit);
            $("#jb_url").text(data.url);
            var short_coded = data.short.split('/');
            form.val("edit_form",{"short":short_coded[3],"url":data.url,"type":data.type});
            layer.open({
                type: 1,title:'编辑短链',area: ['70%', '570px'],closeBtn: 0,
                content: $('#edit')
                ,btn: ['保存','取消']
                ,yes: function(index, layero) {
                    var form_data = form.val("edit_form");
                    if (form_data.short === '') {
                        layer.msg('请输入短链后缀');
                        return false;
                    }
                    if (form_data.url === '') {
                        layer.msg('请输入原长链');
                        return false;
                    }
                    if (form_data.type === '') {
                        layer.msg('请选择短链属性');
                        return false;
                    }
                    $.post("ajax.php", {
                            mode: 'url_edit',
                            short: form_data.short,
                            url: form_data.url,
                            type: form_data.type,
                            id: data.id,
                            ip: data.ip
                        },
                        function (data) {
                            if (data.code === 200) {
                                layer.msg('保存成功');
                                shuax();
                                layer.close(index);
                            } else {
                                layer.msg(data.error);
                            }
                        }
                    );
                }
                ,btn2: function(index, layero){
                    layer.close(index);
                }
            });
        }
    });

    function shuax() {
        table.reload('listReload', {
            page: {curr: 1}
            , url: 'api.php?mode=url_list'
            , method: 'post'
            , loading: true
        });
    }
});