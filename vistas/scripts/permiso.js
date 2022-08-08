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
}

function mostrarform( flag ){

    if ( flag ) {
        $("#listadoregistros").hide();
        $("#formularioregistro").show();
        $("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
    }else{
        $("#listadoregistros").show();
        $("#formularioregistro").hide();
        $("#btnagregar").hide();
    }

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
                url:'../ajax/permisoajax.php?op=listar',
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

init();




