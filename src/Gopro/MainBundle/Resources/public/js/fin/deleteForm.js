$(function () {
    $("#deleteForm").submit(function(event){
        if (!confirm("Esta seguro que desea eliminar?"))
        {
            event.preventDefault();
            return;
        }
    });
});