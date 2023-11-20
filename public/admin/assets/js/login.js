$(document).ready(function () {
    $(".form-auth-small").submit(function (e) {
        e.preventDefault();
        $("#email_err").hide();
        $("#password_err").hide();

        var actionurl = e.currentTarget.action;
        var email = $("#email").val();
        var password = $("#password").val();

        if (email == "") {
            $("#email_err").show();
            return false;
        }
        if (password == "") {
            $("#password_err").show();
            return false;
        }

        $.ajax({
            url: actionurl,
            type: "post",
            dataType: "application/json",
            data: $(".form-auth-small").serialize(),
            dataType: "JSON",
            success: function (res) {
                if (res.statusCode == 404) {
                    $(".alert-warning").show();
                    $(".warning-msg").html(res.message);
                    $(".alert-warning").fadeOut(4000);
                }
                if (res.statusCode == 200) {
                    //var webUrl = '{{url("/pashumitra/")}}';
                    window.location.href = webUrl + "/dashboard";
                }
            },
        });
    });
});
