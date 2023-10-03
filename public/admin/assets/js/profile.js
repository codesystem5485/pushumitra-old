$(document).ready(function () {
    $(".phone").keypress(function (e) {
        var arr = [];
        var kk = e.which;
        for (i = 48; i < 58; i++) arr.push(i);
        if (!(arr.indexOf(kk) >= 0)) e.preventDefault();
    });
    $(".form-mobile-otp").submit(function (e) {
        e.preventDefault();
        var actionurl = webUrl + "/mobile-verify";

        $.ajax({
            url: actionurl,
            type: "post",
            dataType: "application/json",
            data: $(".form-mobile-otp").serialize(),
            dataType: "JSON",
            success: function (res) {
                if (res.statusCode == 403 || res.statusCode == 500) {
                    $(".error-msg").show().html(res.message);
                    $(".error-msg").fadeOut(4000);
                } else if (res.statusCode == 200) {
                    $(".mobile-update").html(res.html);
                    $(".success-msg").html(res.message);
                    $(".success-msg").fadeOut(4000);
                }
            },
        });
    });
    //
    $(".form-change-password").submit(function (e) {
        e.preventDefault();
        $("#confirm_password_err").hide();
        $("#current_password_err").hide();
        $("#old_password_err").hide();

        var old_password = $("#old_password").val();
        var current_password = $("#current_password").val();
        var confirm_password = $("#confirm_password").val();

        if (old_password.trim() == "") {
            $("#old_password_err").show();
            return false;
        }
        if (current_password.trim() == "") {
            $("#current_password_err").show();
            return false;
        }
        if (confirm_password.trim() == "") {
            $("#confirm_password_err").show();
            return false;
        }

        var actionurl = webUrl + "/change-password";
        $.ajax({
            url: actionurl,
            type: "post",
            dataType: "application/json",
            data: $(".form-change-password").serialize(),
            dataType: "JSON",
            success: function (res) {
                if (res.statusCode == 400 || res.statusCode == 500) {
                    $("#error-msg").show().html(res.message);
                    $("#error-msg").fadeOut(4000);
                } else if (res.statusCode == 200) {
                    $("#old_password").val("");
                    $("#current_password").val("");
                    $("#confirm_password").val("");
                    $("#success-msg").show().html(res.message);
                    $("#success-msg").fadeOut(4000);
                }
            },
        });
    });
    //
    $("body").on("click", ".verify", function () {
        var actionurl = webUrl + "/update-mobile";
        var otp = $(".otp").val();
        var mobile = $(".phone").val();
        $.ajax({
            url: actionurl,
            type: "post",
            dataType: "application/json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                otp: otp,
                mobile_number: mobile,
            },
            dataType: "JSON",
            success: function (res) {
                if (
                    res.statusCode == 403 ||
                    res.statusCode == 400 ||
                    res.statusCode == 500
                ) {
                    $(".error-msg").show().html(res.message);
                    $(".error-msg").fadeOut(4000);
                } else if (res.statusCode == 200) {
                    window.location.href = webUrl + "/profile";
                }
            },
        });
        return false;
    });
});
