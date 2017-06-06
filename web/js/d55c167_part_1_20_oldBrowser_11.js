$(function () {
    if (!Modernizr.json) {
        alert('Navegador muy antiguo, por favor actualícelo');
        window.location = "http://whatbrowser.org/"
    }
});
