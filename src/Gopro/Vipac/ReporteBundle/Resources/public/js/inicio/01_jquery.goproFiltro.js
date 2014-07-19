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

    var grupoRow={
        id:1,
        formName:formName,
        campos: JSON.parse($(this).find('#'+formName+'_campos').val()),
        grupos: JSON.parse($(this).find('#'+formName+'_grupos').val())
    };

    var el = {
        filtroContent : $("<div>", {id: "filtroContent"}),
        filtroTable : $("<table>", {id: "filtroTable"}),
        filtroTableBody : $("<tbody>"),
        filtroAdd : $("<a>", {
            id: "filtroAdd",
            text: "Agregar Filtro",
            click: function(){
                agregarFiltro();
            }
        }),
        ordenContent : $("<div>", {id: "ordenContent"}),
        ordenTable : $("<table>", {id: "ordenTable"}),
        ordenTableBody : $("<tbody>"),
        ordenAdd : $("<a>", {
            id: "ordenAdd",
            text: "Agregar Orden",
            click: function(){
                agregarOrden();
            }
        }),
        grupoContent : $("<div>", {id: "grupoContent"}),
        grupoTable : $("<table>", {id: "grupoTable"}),
        grupoTableBody : $("<tbody>"),
        grupoAdd : $("<a>", {
            id: "grupoAdd",
            text: "Agregar columna o grupo",
            click: function(){
                agregarGrupo();
            }
        }),
        saveContent : $("<div>", {id: "saveContent", title:'Guardar parámetros'}),
        saveForm : $("<form>", {id: "saveForm"}),
        saveBoton : $("<a>", {
            id: "filtroAdd",
            text: "Guardar Parametros",
            click: function(){
                mostrarSave();
            }
        })
    };
    var mainContainer=$('#GoproVipacReporteBundle');
    var parametrosform=$(this);
    el.grupoContent.prependTo(parametrosform);
    el.grupoTable.appendTo(el.grupoContent);
    el.grupoTableBody.appendTo(el.grupoTable);
    el.grupoAdd.appendTo(el.grupoContent);
    el.ordenContent.prependTo(parametrosform);
    el.ordenTable.appendTo(el.ordenContent);
    el.ordenTableBody.appendTo(el.ordenTable);
    el.ordenAdd.appendTo(el.ordenContent);
    el.filtroContent.prependTo(parametrosform);
    el.filtroTable.appendTo(el.filtroContent);
    el.filtroTableBody.appendTo(el.filtroTable);
    el.filtroAdd.appendTo(el.filtroContent);
    el.saveBoton.prependTo(parametrosform);
    el.saveContent.appendTo(mainContainer);
    el.saveContent.append(tmpl('saveForm',grupoRow));
    el.saveContent.dialog().dialog("close");
    el.saveContent.find(":submit").button();

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

    el.grupoAdd.button({
            icons: {
                primary: 'ui-icon ui-icon-plus'
            }
        }
    );

    el.saveBoton.button({
            icons: {
                primary: 'ui-icon ui-icon-disk'
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
        if(parametrosform.find('#'+formName+'_grupoaplicado').val()!='' && typeof parametrosform.find('#'+formName+'_grupoaplicado').val() !== 'undefined'){
            var grupoAplicado=JSON.parse(parametrosform.find('#'+formName+'_grupoaplicado').val());
            for (var key in grupoAplicado)
            {
                agregarGrupo(grupoAplicado[key].campo,grupoAplicado[key].grupo);
            }
        }

        var fixHelperModified = function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width())
            });
            return $helper;
        };

        $('#grupoContent tbody').sortable({
            helper: fixHelperModified

        });
        $('#ordenContent tbody').sortable({
            helper: fixHelperModified

        });
    });

    var savedParameter={};

    var mostrarSave=function(currentCampo,currentGrupo){

        $.each(parametrosform.serializeArray(), function( index, value ) {
            if(typeof value.name != 'undefined' && typeof value.value != 'undefined' ){
                if(/^[A-Za-z]/.test(value.name))
                {
                    var input = value.name.replace(/[\[\]]$/,"").replace(/[\]\[]+/g,"|");
                    var keys = input.split("|");
                    if(keys.length==4){
                        if(typeof savedParameter[keys[1]] == 'undefined'){
                            savedParameter[keys[1]]=[];
                        }
                        if(typeof savedParameter[keys[1]][(keys[2]-1)] == 'undefined'){
                            savedParameter[keys[1]][(keys[2]-1)]={};
                        }
                        if(typeof keys[1] != 'undefined' && typeof keys[2] != 'undefined' && typeof keys[3] != 'undefined' && typeof value.value != 'undefined'){
                            savedParameter[keys[1]][(keys[2]-1)][keys[3]]=value.value;
                        }
                    }
                }
            }
        });
        console.log(JSON.stringify(savedParameter));
        el.saveContent.dialog( "open" )
    }

    var agregarGrupo=function(currentCampo,currentGrupo){
        el.grupoTableBody.append(tmpl('plantillaGrupoRow',grupoRow));
        var fila=el.grupoTableBody.find('tr[data-id='+grupoRow.id+']');
        fila.find('a.borrarGrupo').button().click(function() {
            $(this).closest('tr').remove();
        });
        var selectCampos=fila.find('td.campo select');
        var opcionesCampos=selectCampos.prop('options')
        var selectGrupos=fila.find('td.grupo select');
        var opcionesGrupos=selectGrupos.prop('options');
        $.each(grupoRow.campos, function(id, contenido) {
            opcionesCampos[opcionesCampos.length] = new Option(contenido.valor, contenido.key);
        });

        selectCampos.change(function(){
            $('option', selectGrupos).remove();
            var selected=$(this).val();
            if(typeof grupoRow.grupos === 'undefined'){
                return false;
            }
            $.each(grupoRow.grupos, function(id, texto) {
                opcionesGrupos[opcionesGrupos.length] = new Option(texto, id);
            });
            if(typeof currentGrupo !== 'undefined'){
                selectGrupos.val(currentGrupo);
                currentGrupo='';
            }
        });

        grupoRow.id=grupoRow.id+1;
        if(typeof currentCampo !== 'undefined'){
            selectCampos.val(currentCampo);
            selectCampos.trigger("change");
        }
    };

    var agregarOrden=function(currentCampo,currentOrden){
        el.ordenTableBody.append(tmpl('plantillaOrdenRow',ordenRow));
        var fila=el.ordenTableBody.find('tr[data-id='+ordenRow.id+']');
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

        el.filtroTableBody.append(tmpl('plantillaFiltroRow',filtroRow));
        var fila=el.filtroTableBody.find('tr[data-id='+filtroRow.id+']');
        fila.find('a.borrarFiltro').button().click(function() {
            $(this).closest('tr').remove();
        });
        var selectCampos=fila.find('td.campo select');
        var opcionesCampos=selectCampos.prop('options')
        var selectOperadores=fila.find('td.operador select');
        var opcionesOperadores=selectOperadores.prop('options');
        var inputValor=fila.find('td.valor input')

        $.each(filtroRow.campos, function(id, contenido) {
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

