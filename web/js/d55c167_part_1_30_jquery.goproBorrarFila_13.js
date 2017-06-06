$.fn.borraFila = function() {
    this.click(function(event) {
        event.preventDefault();
        var id = $(this).data('id');
        var url = $(this).prop('href');
        var deleting = $.ajax({
            url: url,
            type: 'DELETE',
            statusCode: {
                500: function() {
                    alert("500 Error Interno: No se ha eliminado la fila.");
                }
            }
        });
        deleting.done(function(data) {
            if(!data.hasOwnProperty('mensaje')){
                alert ('La respuesta no fue válida.');
                return false;
            }
            if(data.mensaje.exito=='si'){

                $("table#listaArchivos tr[data-id="+id+"]").remove();
                $("#sessionFlash").empty().append(tmpl('plantillaHighlight',data.mensaje));
            }else{
                $("#sessionFlash").empty().append(tmpl('plantillaError',data.mensaje));
            };
        });
    });
};