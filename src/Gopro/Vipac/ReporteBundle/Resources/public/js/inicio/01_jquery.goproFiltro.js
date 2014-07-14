$.fn.filtro = function() {
    if(this.length==0){return false;}
    var formName='parametrosForm';

    if($(this).find('#'+formName+'_campos').val()==''
        ||$(this).find('#'+formName+'_tipos').val()==''
        ||$(this).find('#'+formName+'_operadores').val()==''){
        return false;
    }
    var filtroRow={
        id:1,
        formName:formName,
        campos: JSON.parse($(this).find('#'+formName+'_campos').val()),
        tipos: JSON.parse($(this).find('#'+formName+'_tipos').val()),
        operadores: JSON.parse($(this).find('#'+formName+'_operadores').val())
    };

    var ordenRow={
        id:1,
        formName:formName,
        campos: JSON.parse($(this).find('#'+formName+'_campos').val()),
        ordenes: JSON.parse($(this).find('#'+formName+'_ordenes').val())
    };

    var el = {
        filtroContent : $("<div>", {id: "filtroContent"}),
        filtroTable : $("<table>", {id: "filtroTable"}),
        filtroAdd : $("<a>", {
            id: "filtroAdd",
            text: "Agregar Filtro",
            click: function(){
                agregarFiltro();
            }
        }),
        ordenContent : $("<div>", {id: "ordenContent"}),
        ordenTable : $("<table>", {id: "ordenTable"}),
        ordenAdd : $("<a>", {
            id: "ordenAdd",
            text: "Agregar Orden",
            click: function(){
                agregarOrden();
            }
        })
    };

    var parametrosform=$(this);
    el.ordenContent.prependTo(parametrosform);
    el.ordenTable.appendTo(el.ordenContent);
    el.ordenAdd.appendTo(el.ordenContent);
    el.filtroContent.prependTo(parametrosform);
    el.filtroTable.appendTo(el.filtroContent);
    el.filtroAdd.appendTo(el.filtroContent);

    el.filtroAdd.button({
            icons: {
                primary: 'ui-icon ui-icon-plus'
            }
        }
    );

    el.ordenAdd.button({
            icons: {
                primary: 'ui-icon ui-icon-plus'
            }
        }
    );

    $(document).ready(function() {

        if(parametrosform.find('#'+formName+'_filtroaplicado').val()!='' && typeof parametrosform.find('#'+formName+'_filtroaplicado').val() !== 'undefined'){
            var filtroAplicado=JSON.parse(parametrosform.find('#'+formName+'_filtroaplicado').val());
            for (var key in filtroAplicado)
            {
                agregarFiltro(filtroAplicado[key].campo,filtroAplicado[key].operador,filtroAplicado[key].valor);
            }
        }
        if(parametrosform.find('#'+formName+'_ordenaplicado').val()!='' && typeof parametrosform.find('#'+formName+'_ordenaplicado').val() !== 'undefined'){
            var ordenAplicado=JSON.parse(parametrosform.find('#'+formName+'_ordenaplicado').val());
            for (var key in ordenAplicado)
            {
                agregarOrden(ordenAplicado[key].campo,ordenAplicado[key].orden);
            }
        }
    });

    var agregarOrden=function(currentCampo,currentOrden){
        el.ordenTable.append(tmpl('plantillaOrdenRow',ordenRow));
        var fila=el.ordenTable.find('tr[data-id='+ordenRow.id+']');
        fila.find('a.borrarOrden').button().click(function() {
            $(this).closest('tr').remove();
        });
        var selectCampos=fila.find('td.campo select');
        var opcionesCampos=selectCampos.prop('options')
        var selectOrdenes=fila.find('td.orden select');
        var opcionesOrdenes=selectOrdenes.prop('options');
        $.each(ordenRow.campos, function(id, contenido) {
            opcionesCampos[opcionesCampos.length] = new Option(contenido.valor, contenido.key);
        });

        selectCampos.change(function(){
            $('option', selectOrdenes).remove();
            var selected=$(this).val();
            if(typeof ordenRow.ordenes === 'undefined'){
                return false;
            }
            $.each(ordenRow.ordenes, function(id, texto) {
                opcionesOrdenes[opcionesOrdenes.length] = new Option(texto, id);
            });
            if(typeof currentOrden !== 'undefined'){
                selectOrdenes.val(currentOrden);
                currentOrden='';
            }
        });

        ordenRow.id=ordenRow.id+1;
        if(typeof currentCampo !== 'undefined'){
            selectCampos.val(currentCampo);
            selectCampos.trigger("change");
        }
    };

    var agregarFiltro=function(currentCampo,currentOperador,currentValor){

        el.filtroTable.append(tmpl('plantillaFiltroRow',filtroRow));
        var fila=el.filtroTable.find('tr[data-id='+filtroRow.id+']');
        fila.find('a.borrarFiltro').button().click(function() {
            $(this).closest('tr').remove();
        });
        var selectCampos=fila.find('td.campo select');
        var opcionesCampos=selectCampos.prop('options')
        var selectOperadores=fila.find('td.operador select');
        var opcionesOperadores=selectOperadores.prop('options');
        var inputValor=fila.find('td.valor input')

        $.each(filtroRow.campos, function(id, contenido) {
            console.log(contenido);
            opcionesCampos[opcionesCampos.length] = new Option(contenido.valor, contenido.key);
        });

        selectCampos.change(function(){
            $('option', selectOperadores).remove();
            var selected=$(this).val();
            if(typeof filtroRow.operadores[selected] === 'undefined'){
                return false;
            }
            $.each(filtroRow.operadores[selected], function(id, texto) {
                opcionesOperadores[opcionesOperadores.length] = new Option(texto, id);
            });
            if(typeof currentOperador !== 'undefined'){
                selectOperadores.val(currentOperador);
                currentOperador='';
            }
            inputValor.val('');
            if(typeof currentValor !== 'undefined'){
                inputValor.val(currentValor);
                currentValor='';
            }

            if(typeof filtroRow.tipos[selected] === 'undefined'){
                return false;
            }
            $.each(filtroRow.tipos[selected], function(id, texto) {
                switch(id) {
                    case '3':
                        inputValor.prop("type", "date");
                        if (!Modernizr.inputtypes.date) {
                            inputValor.datepicker({
                                changeMonth: true,
                                changeYear: true,
                                dateFormat: 'yy-mm-dd ',
                                yearRange: "-0:+1"
                            });
                        }
                        break;
                    case '2':
                        inputValor.prop("type", "number");
                        if (!Modernizr.inputtypes.date) {
                            inputValor.datepicker("destroy").removeClass("hasDatepicker").removeAttr('id');
                        }
                        break;
                    default:
                        inputValor.prop("type", "text");
                        if (!Modernizr.inputtypes.date) {
                            inputValor.datepicker("destroy").removeClass("hasDatepicker").removeAttr('id');
                        }
                }
            })

        });
        filtroRow.id=filtroRow.id+1;
        if(typeof currentCampo !== 'undefined'){
            selectCampos.val(currentCampo);
            selectCampos.trigger("change");
        }
    }
};

