$(function () {
    $(".datePicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd ',
        yearRange: "-0:+1"
    });
});