//date picker porcess
$(document).ready(function () {
    $("#start-date")
        .datepicker({
            startDate: "-0m",
        })
        .on("changeDate", function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $("#end-date").datepicker("setStartDate", minDate);
        });
    $("#end-date")
        .datepicker({
            startDate: "-0m",
        })
        .on("changeDate", function (selected) {
            var minDate = new Date(selected.date.valueOf());
            $("#start-date").datepicker("setEndDate", minDate);
        });
    $(".fee-type").on("change", function () {
        let type = $(this).val();
        if (type == "free") {
            $(".entry-fee").hide();
            $("#entry-fee").val(0);
        } else {
            $(".entry-fee").show();
        }
    });
});
