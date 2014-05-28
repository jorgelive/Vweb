$(function () {
    'use strict';
    // Initialize the jQuery File Upload widget:
    $('#fileUpload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'server/php/'
    });
})