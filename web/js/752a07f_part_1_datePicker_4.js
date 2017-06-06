$(function () {
    if (!Modernizr.inputtypes.date) {
        $(".datePicker-0--1").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd ',
            yearRange: "-0:+1"
        });
        $(".datePicker-80-18").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd ',
            yearRange: "-80:-18"
        });

    }
});