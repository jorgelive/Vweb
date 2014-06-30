$(function () {
    $('div#sidebar ul li a').button({
            icons: {
                primary: 'ui-icon ui-icon-play'
            }
        }
    );
    if($('div#sidebar').css('display')=='none'){
        $('div#container').width('100%');
    }
});