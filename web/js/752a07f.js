$(document).ready(function()
{
    var formulario=$('form[name="gopro_mainbundle_archivo"]');
    var url=formulario.attr('action');

    $("#cargadorArchivos").uploadFile({
        url:url,
        dynamicFormData: function()
        {
            return formulario.serializeObject();
        },
        fileName: 'gopro_mainbundle_archivo[archivo]',
        multiple:false,
        showStatusAfterSuccess:false,
        dragDropStr: "<span><b>Area para arrastrar y soltar archivos</b></span>",
        onSuccess:function(files,data,xhr)
        {
            $("#sessionFlash").empty().append(tmpl('plantillaHighlight',data.mensaje));
            $("table#listaArchivos tbody").prepend(tmpl('archivoRow',data.archivo));
            $(".borrarFila").borraFila();
        }
    });
});
$(document).ready(function()
{
    $(".borrarFila").borraFila();
});

$(function () {
    $('button').button();
    $('a.boton').button();
});
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
$(function () {
    $("#deleteForm").submit(function(event){
        if (!confirm("Esta seguro que desea eliminar?"))
        {
            event.preventDefault();
            return;
        }
    });
});
$(document).ready(function()
{
    $(':input[readonly]').each(function(){
        $(this)
            .hide()
            .parent().append('<span class="hiddenPlaceholder">' + $(this).find(":selected").text() + '</span>')
    });
});

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