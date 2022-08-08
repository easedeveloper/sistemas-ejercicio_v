/**
 * 1. creamos variable tabla
 * 2. creamos funcion init
 * 3. creamos funcion limpiar
 * 4. creamos funcion mostrarform
 * 5. creamos funcion cancearform
 * 6. creamos funcion listar
 * 7. creamos funcion guardaryeditar
 * 8. creamos funcion mostrar
 * 9. creamos funcion desactivar
 * 10.creamos funcion activar
 * 11.init al final 
*/

var tabla;

function init(){
    mostrarform(false);

    listar();

    $("#formulario").on("submit",
        function(e){
            guardaryeditar(e);        
    })
}

function limpiar(){
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#idcategoria").val("");
}

function mostrarform( flag ){
    limpiar();

    if ( flag ) {
        $("#listadoregistros").hide();
        $("#formularioregistro").show();
        $("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
    }else{
        $("#listadoregistros").show();
        $("#formularioregistro").hide();
        $("#btnagregar").show();
    }

}

function cancelarform(){
    limpiar();
    mostrarform( false );
}

function listar(){
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
        buttons:[
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax":
            {
                url:'../ajax/categoriaajax.php?op=listar',
                type:"get",
                dataType:"json",
                error: function(e){
                    console.log(e.responseText);
                }
            },
        "bDestroy": true,
		"iDisplayLength": 10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
    }).DataTable();
}

function guardaryeditar( e ){
    e.preventDefault();
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url:"../ajax/categoriaajax.php?op=guardaryeditar",
        type:"POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos){
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
    limpiar();
}


function mostrar( idcategoria ){
    $.post("../ajax/categoriaajax.php?op=mostrar", { idcategoria: idcategoria }, function(data, status)
    {
        data = JSON.parse(data);
        mostrarform(true);

        $("#nombre").val(data.nombre);
        $("#descripcion").val(data.descripcion);
        $("#idcategoria").val(data.idcategoria);
    })
}

function desactivar( idcategoriaa )
{
    bootbox.confirm("¿Está seguro de desactivar la Categoria?", function(result){
        if (result) {
            $.post("../ajax/categoriaajax.php?op=desactivar",{ idcategoria: idcategoriaa }, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();                
            })
        }
    })
}

function activar( idcategoriaa )
{
    bootbox.confirm("¿Está seguro de activar la Categoria?", function(result){
        if ( result ) {
            $.post("../ajax/categoriaajax.php?op=activar", { idcategoria: idcategoriaa }, function(e){
               bootbox.alert(e);
               tabla.ajax.reload();
            })
        }
    })
}

init();

