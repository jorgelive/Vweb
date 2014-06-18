$.fn.filtro = function() {
    //console.log(this);
    if(this.length==0){return false;}
    var formName='parametrosForm';

    if($(this).find('#'+formName+'_campos').val()==''
        ||$(this).find('#'+formName+'_tipos').val()==''
        ||$(this).find('#'+formName+'_operadores').val()==''){
        return false;
    }
    var row={
        id:1,
        formName:formName,
        campos: JSON.parse($(this).find('#'+formName+'_campos').val()),
        tipos: JSON.parse($(this).find('#'+formName+'_tipos').val()),
        operadores: JSON.parse($(this).find('#'+formName+'_operadores').val())
    };

    var el = {
        filtroContent : $("<div>", {id: "filtroContent"}),
        filtroTable : $("<table>", {id: "filtroTable"}),
        filtroAdd : $("<a>", {
            id: "filtroAdd",
            text: "Agregar Filtro",
            click: function(){
                agregarFila();
            }
        })
    };
    var filtroForm=$(this);
    el.filtroContent.prependTo(filtroForm);
    el.filtroTable.appendTo(el.filtroContent);
    el.filtroAdd.appendTo(el.filtroContent);

    el.filtroAdd.button({
            icons: {
                primary: 'ui-icon ui-icon-plus'
            }
        }
    );

    $(document).ready(function() {

        if(filtroForm.find('#'+formName+'_filtroaplicado').val()==''|| typeof filtroForm.find('#'+formName+'_filtroaplicado').val() === 'undefined'){
            return false;
        }

        var filtroAplicado=JSON.parse(filtroForm.find('#'+formName+'_filtroaplicado').val())
        for (var key in filtroAplicado)
        {
            agregarFila(filtroAplicado[key].campo,filtroAplicado[key].operador,filtroAplicado[key].valor);
        }

        //console.log(filtroAplicado);
    })

    var agregarFila=function(currentCampo,currentOperador,currentValor){

        el.filtroTable.append(tmpl('plantillaFiltroRow',row));
        var fila=el.filtroTable.find('tr[data-id='+row.id+']');
        fila.find('a.borrarFiltro').button().click(function() {
            $(this).closest('tr').remove();
        })
        var selectCampos=fila.find('td.campo select');
        var opcionesCampos=selectCampos.prop('options')
        var selectOperadores=fila.find('td.operador select');
        var opcionesOperadores=selectOperadores.prop('options');
        var valorInput=fila.find('td.valor input')
        if(typeof currentCampo !== 'undefined'){
            selectCampos.val(currentCampo);
            console.log(selectCampos.val());
        };
        $.each(row.campos, function(id, texto) {
            opcionesCampos[opcionesCampos.length] = new Option(texto, id);
        });
        selectCampos.change(function(){
            $('option', selectOperadores).remove();
            var selected=$(this).val();
            //console.log(row.operadores[selected]);
            if(typeof row.operadores[selected] === 'undefined'){
                return false;
            };
            $.each(row.operadores[selected], function(id, texto) {
                opcionesOperadores[opcionesOperadores.length] = new Option(texto, id);
            })
            valorInput.val('');
            if(typeof row.tipos[selected] === 'undefined'){
                return false;
            };
            $.each(row.tipos[selected], function(id, texto) {
                switch(id) {
                    case '3':
                        valorInput.prop("type", "date");
                        if (!Modernizr.inputtypes.date) {
                            valorInput.datepicker({
                                changeMonth: true,
                                changeYear: true,
                                dateFormat: 'yy-mm-dd ',
                                yearRange: "-0:+1"
                            });
                        }
                        break;
                    case '2':
                        valorInput.prop("type", "number");
                        if (!Modernizr.inputtypes.date) {
                            valorInput.datepicker("destroy").removeClass("hasDatepicker").removeAttr('id');
                        }
                        break;
                    default:
                        valorInput.prop("type", "text");
                        if (!Modernizr.inputtypes.date) {
                            valorInput.datepicker("destroy").removeClass("hasDatepicker").removeAttr('id');
                        }
                }

            })

        })
        row.id=row.id+1;
    }
};

