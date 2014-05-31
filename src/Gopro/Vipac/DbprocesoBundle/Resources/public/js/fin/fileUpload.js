$(function () {
    'use strict';
    $('#fileUpload').fileupload({
        xhrFields: {withCredentials: true},
        url: $('#fileUpload').attr('action')
    });

    if ($.support.cors) {
        $.ajax({
            url: 'http://vweb.local:10088/',
            type: 'HEAD'
        }).fail(function () {
            $('<div class="alert alert-danger"/>')
                .text('El servidor no se encuentra disponible - ' + new Date())
                .appendTo('#fileUpload');
        });
    }

    $('#fileUpload').addClass('fileupload-processing');
    $.ajax({
        url: $('#fileUpload').data("index-url"),
        dataType: 'json',
        type: 'POST',
        context: $('#fileUpload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
    });
});