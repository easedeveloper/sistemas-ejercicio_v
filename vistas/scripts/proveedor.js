/* 1.creamos la variable tabla
2.creamos la funcion init
3.creamos la funcion limpiar mostraform cancelarform listar 
    guardaryeditar mostrar eliminar
4. init() */

var tabla;

function init(){
    mostrarform(false);
    listar();

    $("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);	
	})
}


function limpiar(){
    $("#nombre").val("");
    $("#num_documento").val("");
    $("#direccion").val("");
    $("#telefono").val("");
    $("#email").val("");
    $("#idpersona").val("");
}


function mostrarform(flag)
{
	limpiar();
	if (flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}


function cancelarform(){
    limpiar();
    mostrarform(false);
}


function listar(){
    /* 1.usamos la varible tabla, el id tabla datatable, ajax cerramosdatatable */
    tabla = $("#tbllistado").dataTable(
        {
        "aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            'copyHtml5',
		            'excelHtml5',
		            'csvHtml5',
		            'pdf'
		        ],
        "ajax":
        {
            url:"../ajax/personaajax.php?op=listarp",
            type:"get",
            dataType:"json",
            error:function(e){
                console.log(e.resposeText);
            }
        },
        "bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
    }).DataTable();

}


function guardaryeditar(e)
{
    e.preventDefault();
    $("#btnGuardar").prop("disabled", false);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url:"../ajax/personaajax.php?op=guardaryeditar",
        type:"POST",
        data:formData,
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


function mostrar(idPersona){
    $.post("../ajax/personaajax.php?op=mostrar",{idpersona:idPersona}, function(data, status){
        data = JSON.parse(data);
        mostrarform(true);

        $("#nombre").val(data.nombre);
        $("#tipo_documento").val(data.tipo_documento);
        $("#tipo_documento").selectpicker('refresh');
        $("#num_documento").val(data.num_documento);
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#email").val(data.email);
        $("#idpersona").val(data.idpersona);
    });
}


function eliminar(idPersona){
    bootbox.confirm("¿Está Seguro de eliminar el proveedor?", function(result){
        if (result) {
            $.post("../ajax/personaajax.php?op=eliminar",{idpersona:idPersona}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            })            
        }
    })
}

init();
