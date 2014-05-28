$( ".ajaxLink" ).click(function(event) {
    event.preventDefault();
    var id = $(this).prop('rel');
    var url = $(this).prop( "href" );
    //alert(url);
    var posting = $.post( url, { id: id } );
    //$("#archivos #listado tr[rel="+id+"]").remove();

    posting.done(function( data ) {
        //  var content = $( data ).find( "#content" );
        if(!data.hasOwnProperty('exito')){
            alert ('El servidor no devolvio la respueta esperada')
            return false;
        }
        if(!data.hasOwnProperty('mensaje')){
            alert ('El servidor no devolvio mensaje')
            return false;
        }
        if(data.exito=='si'){
            $("#archivos #listado tr[rel="+id+"]").remove();
        }else{
            //alert(data.mensaje);

        };
        $("#sessionFlash").empty().append(data.mensaje);
        //
    });
});