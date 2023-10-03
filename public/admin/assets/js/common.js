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
        var actionurl = webUrl + "/get-cities";
        $.ajax({
            url: actionurl,
            type: "get",
            dataType: "application/json",
            data: { state_id: this.value },
            dataType: "JSON",
            success: function (res) {
                $("#city_town").html("<option value=''> --City-- </option>");
                $.each(res, function (index, value) {
                    $("#city_town").append(
                        '<option value="' +
                            value.city_id +
                            '">' +
                            value.city +
                            "</option>"
                    );
                });
            },
        });
    });
});
