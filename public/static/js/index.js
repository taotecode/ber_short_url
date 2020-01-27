
$("#s").click(function () {//生成普通短链
    $.post("Api.php",
        {
            mode: "api-s",
            url: $("#url").val()
        },
        function (data) {
            if (data.code === 400)
                layer.msg(data.error);
            else if (data.code === 500)
                layer.msg(data.error);
            else if (data.code === 200){
                $("#y-url").text($("#url").val());
                $("#d-url").text(data.url);
                $("#display").show();
                $("#f-h").text("防红短链接已生成");
                layer.msg("成功");
                var date = new Date();
                date.setTime(date.getTime()+(60 * 1000));
                $ .cookie('url',0,{expires:date});
            }
        });
});
$("#h").click(function () {//还原短链
    $.post("Api.php",
        {
            mode: "api-h",
            url: $("#url").val()
        },
        function (data) {
            if (data.code === 400)
                layer.msg(data.error);
            else if (data.code === 500)
                layer.msg(data.error);
            else if (data.code === 200){
                $("#d-url").text($("#url").val());
                $("#y-url").text(data.url);
                $("#display").show();
                $("#f-h").text("");
                layer.msg("成功");
            }else if (data.code === 201){
                $("#d-url").text($("#url").val());
                $("#y-url").text(data.url);
                $("#f-h").text("该链接为防红短链接");
                $("#display").show();
                layer.msg("成功");
            }
        });
});
$("#f").click(function () {//生成防红短链
    $.post("Api.php",
        {
            mode: "api-f",
            url: $("#url").val()
        },
        function (data) {
            if (data.code === 400)
                layer.msg(data.error);
            else if (data.code === 500)
                layer.msg(data.error);
            else if (data.code === 200){
                $("#y-url").text($("#url").val());
                $("#d-url").text(data.url);
                $("#f-h").text("防红短链接已生成");
                $("#display").show();
                layer.msg("成功");
                var date = new Date();
                date.setTime(date.getTime()+(60 * 1000));
                $ .cookie('url',0,{expires:date});
            }
        });
});