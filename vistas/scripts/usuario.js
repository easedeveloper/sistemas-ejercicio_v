var tabla;

function init(){
    mostrarform(false);
    listar();
    
    $("#formulario").on("submit", function(e)
    {
        guardaryeditar(e);
    });

    $("#imagenmuestra").hide();

    $.post("../ajax/usuarioajax.php?op=permisos", function(data){
        $("#permisos").html(data);
    });


}

function limpiar()
{
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#cargo").val("");
	$("#login").val("");
	$("#clave").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#idusuario").val("");
}

function mostrarform(flag)
{
    limpiar();
    if (flag)
    {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } 
    else
    {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

function cancelarform()
{
    listar();
    mostrarform(false);
}

function listar()
{
    tabla=$("#tbllistado").dataTable(
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
            url:'../ajax/usuarioajax.php?op=listar',
            type:"get",
            dataType:"json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
    }).DataTable();
}

function guardaryeditar(e)
{
    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url:"../ajax/usuarioajax.php?op=guardaryeditar",
        type:"POST",
        data:formData,
        contentType: false,
	    processData: false,

        success: function(datos)
	    {                    
	        bootbox.alert(datos);	          
	        mostrarform(false);
	        tabla.ajax.reload();
	    }
    });
    limpiar();
}

function mostrar(idUsuario)
{
    $.post("../ajax/usuarioajax.php?op=mostrar",{idusuario:idUsuario},function(data, status){
        data = JSON.parse(data);
        mostrarform(true);

        $("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#cargo").val(data.cargo);
		$("#login").val(data.login);
		$("#clave").val(data.clave);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","../files/usuarios/"+data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idusuario").val(data.idusuario);
    });

    $.post("../ajax/usuarioajax.php?op=permisos&id="+idUsuario, function(resp){
        $("#permisos").html(resp);
    });
}

function desactivar(idUsuario)
{
    bootbox.confirm("¿Está Seguro de desactivar el usuario?", function(result){
        if (result) {
            $.post("../ajax/usuarioajax.php?op=desactivar",{idusuario:idUsuario}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

function activar(idUsuario)
{
    bootbox.confirm("¿Está Seguro de activar el usuario?", function(result){
        if (result) {
            $.post("../ajax/usuarioajax.php?op=activar",{idusuario:idUsuario}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

init();



