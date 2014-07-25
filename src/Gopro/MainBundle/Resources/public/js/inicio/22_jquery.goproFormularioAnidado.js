$.fn.formularioAnidado = function(datosForm,disparador) {
    if($(this).length==0){
        return;
    }
    if(typeof datosForm == 'undefined'){
        alert ('No se ha definido el contenedor de datos');
        return;
    }
    var parametrosJson= $('#'+datosForm).find('#'+datosForm+'_parametros').val();
    var camposJson= $('#'+datosForm).find('#'+datosForm+'_campos').val();

    if(typeof parametrosJson == 'undefined'){
        alert ('los parametros para la creacion de campos no fueron enviados');
        return;
    }
    if(typeof camposJson == 'undefined'){
        alert ('Los datos de los campos no estan disponibles');
        return;
    }

    var formulario=$(this);
    var formularioNombre=formulario.attr("name");
    var parametros=JSON.parse(parametrosJson);

    var el = {
        formularioDiv: formulario.find('#'+formularioNombre),
        anidadoContainer : $("<div>"),
        anidadoContent : $("<div>", {id: formularioNombre+'_'+parametros['entidadAnidada']})

    };

    el.anidadoContent.appendTo(el.anidadoContainer);
    el.formularioDiv.find('#gopro_vipac_proveedorbundle_informacion_submit').closest('div').before(el.anidadoContainer);

    var crearCampos= function(valorDisparador){
        //console.log(camposJson);
        $.each(JSON.parse(camposJson)[valorDisparador],function(index,value) {
            value['formularioNombre']=formularioNombre;
            value['entidadAnidada']=parametros['entidadAnidada'];
            value['campoCaracteristica']=parametros['campoCaracteristica'];
            value['id']=index;
            el.anidadoContent.append(tmpl('formularioanidadoRow',value));
            //console.log (value);
        });
    };

    crearCampos(1);


};
