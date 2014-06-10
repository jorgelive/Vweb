$(document).ready(function()
{
    var formulario=$('form[name="gopro_vipac_dbprocesobundle_archivocampos"]');
    var url=formulario.attr('action');

    $("#cargadorArchivos").uploadFile({
        url:url,
        dynamicFormData: function()
        {
            return formulario.serializeObject();
        },
        multiple:false,
        showStatusAfterSuccess:false
    });
});