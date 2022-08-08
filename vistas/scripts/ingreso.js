var tabla;

function init(){
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e){
        guardarYeditar(e);
    })

    $.post('../ajax/ingresoajax.php?op=selectProveedor', function(rspt){
        $("#idproveedor").html(rspt);
        $("#idproveedor").selectpicker('refresh');
    });
}

//Función limpiar
function limpiar()
{
	$("#idproveedor").val("");
	$("#proveedor").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("0");

	$("#total_compra").val("");
	$(".filas").remove();
	$("#total").html("0");
	
	//Obtenemos la fecha actual
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#fecha_hora').val(today);

    //Marcamos el primer tipo_documento
    $("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").selectpicker('refresh');
}

function mostrarform(flag){
    limpiar();
    if (flag)
    {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        listarArticulos();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArt").show();

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
	    buttons:
        [		          
		    'copyHtml5',
		    'excelHtml5',
		    'csvHtml5',
		    'pdf'
		],
        "ajax":
        {
            url:"../ajax/ingresoajax.php?op=listar",
            type:"GET",
            dataType: "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
    }).DataTable();
}

function listarArticulos()
{
    tabla = $("#tblarticulos").dataTable(
    {
        "aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons:[],
        "ajax":
        {
            url:'../ajax/ingresoajax.php?op=listarArticulos',
            type:'GET',
            dataType:'JSON',
            error:function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
    }).DataTable();
}

function guardarYeditar(e)
{
    e.preventDefault();//No se activará la acción predeterminada del evento
	var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url:"../ajax/ingresoajax.php?op=guardaryeditar",
        type:"POST",
        data: formData,
        contentType: false,
	    processData: false,

        success: function(datos)
        {
            bootbox.alert(datos);
            mostrarform(false);
            listar();
        }
    });
    limpiar();
}

function mostrar(idIngreso)
{
    $.post("../ajax/ingresoajax.php?op=mostrar",{idingreso:idIngreso}, function(data, status)
    {
        data = JSON.parse(data);
        mostrarform(true);

        $("#idproveedor").val(data.idproveedor);
        $("#idproveedor").selectpicker("refresh");
        $("#tipo_comprobante").val(data.tipo_comprobante);
        $("#idproveedor").selectpicker("refresh");
        $("#serie_comprobante").val(data.serie_comprobante);
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idingreso").val(data.idingreso);

        //Ocultar y mostrar Botones
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArt").hide();
    });

    $.post("../ajax/ingresoajax.php?op=listarDetalle&id="+idIngreso,function(r){
        $("#detalles").html(r);
    });

}

function anular(idIngreso)
{
    bootbox.confirm("¿Está Seguro de anular el ingreso?", function(result){
        if (result)
        {
            $.post("../ajax/ingresoajax.php?op=anular",{idingreso:idIngreso},function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            })
        }
    })
}

var impuesto = 18;
var cont     = 0;
var detalles = 0;

$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto()
  {
  	var tipo_comprobante=$("#tipo_comprobante option:selected").text();
  	if (tipo_comprobante=='Factura')
    {
        $("#impuesto").val(impuesto); 
    }
    else
    {
        $("#impuesto").val("0"); 
    }
  }

function agregarDetalle(idarticulo, articulo)
{
    var cantidad      = 1;
    var precio_compra = 1;
    var precio_venta  = 1;

    if (idarticulo != "")
    {
        var subtotal = cantidad * precio_compra;
        var fila ='<tr class="filas" id="fila'+cont+'">'+
        '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')" >X</button></td>'+
        '<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>'+
        '<td><input type="number" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>'+
        '<td><input type="number" name="precio_compra[]" id="precio_compra[]" value="'+precio_compra+'"></td>'+
        '<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="'+precio_venta+'"></td>'+
        '<td><span name="subtotal" id="subtotal'+cont+'">'+subtotal+'</span></td>'+
        '<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>'+
        '</tr>';
        cont++;
        detalles++;
        $("#detalles").append(fila);
        modificarSubtotales();//colocarlo sirve para la funcion evaluar()
    }
    else
    {
    	alert("Error al ingresar el detalle, revisar los datos del artículo");
    }
}

function modificarSubtotales()
{
    var cant = document.getElementsByName("cantidad[]");//almacenando todas las cantidades
    var pre  = document.getElementsByName("precio_compra[]");//almacenando todas los precio_compra
    var sub  = document.getElementsByName("subtotal");//almacenando el subtotal

    for (var i = 0; i < cant.length; i++)
    {
        var inputC = cant[i];
        var inputP = pre[i];
        var inputS = sub[i];

        inputS.value = inputC.value * inputP.value;
        document.getElementsByName("subtotal")[i].innerHTML = inputS.value;
    }
    calcularTotales();
}

function calcularTotales()
{
    var sub = document.getElementsByName("subtotal");
    var total = 0.0;

    for (let i = 0; i < sub.length; i++) {
        total += document.getElementsByName("subtotal")[i].value;
    }

    $("#total").html("S/. "+ total);
    $("#total_compra").val(total);
    evaluar();
}

function evaluar()
{
    if (detalles>0)
    {
      $("#btnGuardar").show();
    }
    else
    {
      $("#btnGuardar").hide(); 
      cont=0;
    }
    
}

function eliminarDetalle(indice)
{
    $("#fila" + indice).remove();
  	calcularTotales();
  	detalles=detalles-1;
  	evaluar();        
}

init();


