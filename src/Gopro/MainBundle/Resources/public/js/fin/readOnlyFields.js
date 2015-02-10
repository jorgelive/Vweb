$(document).ready(function()
{
    $(':input[readonly]').each(function(){
        $(this)
            .hide()
            .parent().append('<span class="hiddenPlaceholder">' + $(this).find(":selected").text() + '</span>')
    });
});
