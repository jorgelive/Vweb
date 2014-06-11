$(document).ready(function()
{
    var formulario=$('form[name="gopro_vipac_dbprocesobundle_archivo"]');
    var url=formulario.attr('action');

    $("#cargadorArchivos").uploadFile({
        url:url,
        dynamicFormData: function()
        {
            return formulario.serializeObject();
        },
        fileName: 'gopro_vipac_dbprocesobundle_archivo[archivo]',
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