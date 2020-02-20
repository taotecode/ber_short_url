layui.use(['table','form'], function() {
    var table = layui.table,form = layui.form;;
    table.render({
        elem: '#list'
        , url: 'api.php?mode=black_list'
        , toolbar: '#toolbar'
        , page : true
        , loading: true
        , title: '黑名单列表'
        , id: 'listReload'
        , cols: [[
            {field: 'id', title: 'ID', type: 'checkbox', fixed: 'left'}
            , {field: 'ip', title: 'IP',width : 100}
            , {field: 'url', title: '链接'}
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
                layer.confirm('确定要删除选中的数据吗？', function(index){
                    $.post("ajax.php", {mode: 'black_del_all',data:JSON.stringify(data)},
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
            layer.confirm('确定要删除这个数据吗？', function(index){
                $.post("ajax.php", {mode: 'black_del', id: data.id},
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
        }
    });

    function shuax() {
        table.reload('listReload', {
            page: {curr: 1}
            , url: 'api.php?mode=black_list'
            , method: 'post'
            , loading: true
        });
    }
});