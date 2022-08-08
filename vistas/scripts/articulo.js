var tabla;

function init()
{
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(data){
        guardaryeditar(data)
    })

    $.post("../ajax/articuloajax.php?op=selectCategoria", function(result){
        $("#idcategoria").html(result);
        $("#idcategoria").selectpicker('refresh');
    });
    $("#imagenmuestra").hide();
}


function limpiar()
{
	$("#codigo").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#stock").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#print").hide();
	$("#idarticulo").val("");//dejando en blanco el #idarticulo para que no quede pegado
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

function cancelarform()
{
	limpiar();
	mostrarform(false);
}

function listar()
{
	tabla=$('#tbllistado').dataTable(
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
					url: '../ajax/articuloajax.php?op=listar',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

function guardaryeditar(e)
{
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url:"../ajax/articuloajax.php?op=guardaryeditar",
        type:"POST",
        data: formData,
        contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          bootbox.alert(datos);	          
	          mostrarform(false);
	          tabla.ajax.reload();
	    }
    });
}


function mostrar( idArticulo )
{
    $.post("../ajax/articuloajax.php?op=mostrar", {idarticulo: idArticulo}, function(data, status){
        data = JSON.parse(data);
        mostrarform(true);

        $("#idcategoria").val(data.idcategoria);
		$('#idcategoria').selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","../files/articulos/"+data.imagen);
		$("#imagenactual").val(data.imagen);
 		$("#idarticulo").val(data.idarticulo);
    })
}

function desactivar( idArticulo )
{
    bootbox.confirm("¿Está Seguro de desactivar el artículo?", function(result){
        if (result)
        {
            $.post("../ajax/articuloajax.php?op=desactivar", {idarticulo: idArticulo}, function(e)
            {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

function activar( idArticulo )
{
    bootbox.confirm("¿Está Seguro de activar el Artículo?", function(result) {
        if (result) {
            $.post("../ajax/articuloajax.php?op=activar", {idarticulo: idArticulo}, function(e)
            {
                bootbox.alert(e);
                tabla.ajax.reload();
            })
        }
    })
}


init();

