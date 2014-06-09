$(".borrarFila").click(function(event) {
    event.preventDefault();
    var id = $(this).data('id');
    var url = $(this).prop('href');
    var deleting = $.ajax({
        url: url,
        type: 'DELETE'
    });
    deleting.done(function(data) {
        if(!data.hasOwnProperty('exito')){
            alert ('El servidor no devolvio la respueta esperada')
            return false;
        }
        if(!data.hasOwnProperty('mensaje')){
            alert ('El servidor no devolvio mensaje')
            return false;
        }
        if(data.exito=='si'){
            $("#archivos #listado tr[data-id="+id+"]").remove();
            $("#sessionFlash").empty().append(tmpl('plantillaHighlight',data));
        }else{
            //alert(data.mensaje);
        };
    });
});