
$(function () {
    $("#deleteForm").submit(function(e){
        if (!confirm("Esta seguro que desea eliminar?"))
        {
            e.preventDefault();
            return;
        }
    });
});