$("body").on("click", ".button-remove", function () {
    if (confirm("Are you sure want to delete?")) {
        return true;
    } else {
        return false;
    }
});

$("#all-chk").click(function () {
    $("input:checkbox").not(this).prop("checked", this.checked);
});
$(document).ready(function () {
    $("#state").on("change", function () {
        var actionurl = webUrl + "/pashumitra/get-cities";
        var state_val = $(this).children("option:selected").attr("state_val");
        $("#state_id").val(state_val);
		
        $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { state_id: state_val },
            dataType: "JSON",
            success: function (res) {
                $("#city_town").html("<option value=''> --City-- </option>");
                $.each(res, function (index, value) {
                    $("#city_town").append(
                        '<option city_val="' +
                            value.city_id +
                            '" value="' +
                            value.city +
                            '">' +
                            value.city +
                            "</option>"
                    );
                });
            },
        });
    });

    $("#city_town").on("change", function () {
        var actionurl = webUrl + "/pashumitra/get-cities";
        var city_val = $(this).children("option:selected").attr("city_val");
        $("#city_id").val(city_val);
    });

    $("#rv_working_state").on("change", function () {
        var actionurl = webUrl + "pashumitra/get-cities";
        var state_val = $(this).children("option:selected").attr("state_val");
        $("#rv_working_state_id").val(state_val);
        $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { state_id: state_val },
            dataType: "JSON",
            success: function (res) {
                $("#rv_working_city_town").html(
                    "<option value=''> --City-- </option>"
                );
                $.each(res, function (index, value) {
                    $("#rv_working_city_town").append(
                        '<option city_val="' +
                            value.city_id +
                            '" value="' +
                            value.city +
                            '">' +
                            value.city +
                            "</option>"
                    );
                });
            },
        });
    });

    $("#rv_working_city_town").on("change", function () {
        var actionurl = webUrl + "pashumitra/get-cities";
        var city_val = $(this).children("option:selected").attr("city_val");
        $("#rv_working_city_id").val(city_val);
    });
});
