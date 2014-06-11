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
        onSuccess:function(files,data,xhr)
        {
            $("#sessionFlash").empty().append(tmpl('plantillaHighlight',data.mensaje));
            //$("#eventsmessage").html($("#eventsmessage").html()+"<br/>Success for: "+JSON.stringify(data));

        }
    });
});