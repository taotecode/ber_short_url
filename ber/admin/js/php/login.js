function check_login() {
    var user = $("#user").val();
    var pass = $("#pass").val();
    if (user === '' || pass === '') {
        $("#login_form").removeClass('shake_effect');
        setTimeout(function () {
            $("#login_form").addClass('shake_effect')
        }, 1);
        return false
    }
    $.post("api.php", {mode: 'login', user: user, pass: pass},
        function (data) {
            if (data.code === 200) {
                alert("登录成功！");
                top.location=data.url;
            }else{
                alert(data.error);
            }
        });
}