var tabla;

function init(){   
    mostrarform(false);
    listar();

	$("#formulario").on("submit",function(e){
		guardaryeditar(e)
	})

    $.post("../ajax/ventaajax.php?op=selectCliente", function(r){
        $("#idcliente").html(r);
        $('#idcliente').selectpicker('refresh');
    })

}

function limpiar()
{
	$("#idcliente").val("");
	$("#cliente").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#impuesto").val("0");

	$("#total_venta").val("");
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

function mostrarform(flag)
{
	limpiar();
	if (flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").show();
		detalles=0;
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

function anular(idVenta)
{
    bootbox.confirm("¿Está Seguro de anular la venta?", function(result){
        if (result)
        {
            $.post("../ajax/ventaajax.php?op=anular",{idventa: idVenta}, function(e){
                bootbox.alert(e);
	            tabla.ajax.reload();
            });
        }
    });
}


var impuesto = 18;
var cont = 0;
var detalles = 0

$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto()
{
	var tipo_comprobante=$("#tipo_comprobante option:selected").text();
	if (tipo_comprobante == "Factura")
	{
		$("#impuesto").val(impuesto);
	}
	else
	{
		$("#impuesto").val("0");
	}
}

function agregarDetalle(idarticulo, articulo, precio_venta)
{
	var cantidad = 1;
	var descuento = 0;

	if (idarticulo != "")
	{
		var subtotal = cantidad*precio_venta;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')">X</button></td>'+
		'<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>'+
		'<td><input type="number" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>'+
		'<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="'+precio_venta+'"></td>'+
		'<td><input type="number" name="descuento[]" value="'+descuento+'"></td>'+
		'<td><span name="subtotal" id="subtotal'+cont+'">'+subtotal+'</span></td>'+
		'<td><button type="button" onclick="modificarSubototales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>'+
		'</tr>';
		cont++;
		detalles++;
		$("#detalles").append(fila);
		modificarSubototales()
	}
	else
	{
		alert("Error al ingresar el detalle, revisar los datos del artículo");		
	}
}

function modificarSubototales()
{
	var cant = document.getElementsByName("cantidad[]");
	var prev = document.getElementsByName("precio_venta[]");
	var desc = document.getElementsByName("descuento[]");
	var subt = document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inputC  = cant[i];
		var inputPv = prev[i];
		var inputDe = desc[i];
		var inputSu = subt[i];

		inputSu.value = (inputC.value * inputPv.value) - inputDe.value;
		document.getElementsByName("subtotal")[i].innerHTML = inputSu.value;
	}
	calcularTotales();
}

function calcularTotales()
{
	var sub   = document.getElementsByName("subtotal");
	var total = 0.0;

	for (let i = 0; i < sub.length; i++) {
		total += document.getElementsByName("subtotal")[i].value;
	}
	$("#total").html("S./ "+total);
	$("#total_venta").val(total);
	evaluar();
}

function evaluar()
{
	if (detalles > 0) {
		$("#btnGuardar").show();
	} else{
		$("#btnGuardar").hide();
		cont=0
	}
}

function eliminarDetalle(indice)
{
	$("#fila"+indice).remove();
	calcularTotales();
	detalles--;
	evaluar();
}

function cancelarform()
{
    limpiar();
    mostrarform(false);
}

function listar()
{
    tabla = $("#tbllistado").dataTable(
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
            url:"../ajax/ventaajax.php?op=listar",
            type:"POST",
            datatype:"JSON",
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
	tabla=$("#tblarticulos").dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [],
		"ajax":
		{
			url:"../ajax/ventaajax.php?op=listarArticuloVenta",
			type:"GET",
			datatype:"JSON",
			error: function(e){
				console.log(e.responseText);	
			}			
		},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

function mostrar(idVenta)
{
    $.post("../ajax/ventaajax.php?op=mostrar",{idventa:idVenta}, function(data, status){
        data = JSON.parse(data);
        mostrarform(true);

        $("#idcliente").val(data.idcliente);
		$("#idcliente").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#serie_comprobante").val(data.serie_comprobante);
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idventa").val(data.idventa);

        $("#btnGuardar").hide();
        $("#btnAgregarArt").hide();
        $("#btnCancelar").show();
    });

    $.post("../ajax/ventaajax.php?op=listarDetalle&id="+idVenta, function(r){
        $("#detalles").html(r);
    });
    


}

function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url:"../ajax/ventaajax.php?op=guardaryeditar",
		type:"POST",
		data: formData,
		contentType: false,
	    processData: false,
		
		success:function(datos)
		{
			bootbox.alert(datos);
			mostrarform(false);
			listar();
		},
	});
	limpiar();
}

init();