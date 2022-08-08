<?php
if (strlen(session_id()) < 1)//Verificamos is la session no esta iniciada 
    session_start();

require_once '../modelos/Ingreso.php';

$ingresoM = new Ingreso();

$idingreso         = isset($_POST['idingreso'])         ? limpiarcadena($_POST['idingreso'])        : "";
$idproveedor       = isset($_POST['idproveedor'])       ? limpiarcadena($_POST['idproveedor'])      : "";
$idusuario         = $_SESSION["idusuario"];
$tipo_comprobante  = isset($_POST['tipo_comprobante'])  ? limpiarcadena($_POST['tipo_comprobante']) : "";
$serie_comprobante = isset($_POST['serie_comprobante']) ? limpiarcadena($_POST['serie_comprobante']): "";
$num_comprobante   = isset($_POST['num_comprobante'])   ? limpiarcadena($_POST['num_comprobante'])  : "";
$fecha_hora        = isset($_POST['fecha_hora'])        ? limpiarcadena($_POST['fecha_hora'])       : "";
$impuesto          = isset($_POST['impuesto'])          ? limpiarcadena($_POST['impuesto'])         : "";
$total_compra      = isset($_POST['total_compra'])      ? limpiarcadena($_POST['total_compra'])     : "";

switch ($_GET["op"]) {

    case 'anular':
        $rspta = $ingresoM->anular($idingreso);
        echo $rspta ? "Ingreso anulado" : "Ingreso no se puede anular";
    break;

    case 'mostrar':
        $rspta = $ingresoM->mostrar($idingreso);
        echo json_encode($rspta);
    break;

    case 'listarDetalle':
		//Recibimos el idingreso
		$id=$_GET['id'];

		$rspta = $ingresoM->listarDetalle($id);
		$total=0;
		echo '<thead style="background-color:#A9D0F5">
                <th>Opciones</th>
                <th>Artículo</th>
                <th>Cantidad</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Subtotal</th>
              </thead>';

		while ($reg = $rspta->fetch_object())
		{
			echo '<tr class="filas"><td></td>'.
                    '<td>'.$reg->nombre.'</td>'.
                    '<td>'.$reg->cantidad.'</td>'.
                    '<td>'.$reg->precio_compra.'</td>'.
                    '<td>'.$reg->precio_venta.'</td>'.
                    '<td>'.$reg->precio_compra*$reg->cantidad.'</td>'.
                 '</tr>';
			$total=$total+($reg->precio_compra*$reg->cantidad);
		}
		echo '<tfoot>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><h4 id="total">S/.'.$total.'</h4>'.
                 '<input type="hidden" name="total_compra" id="total_compra">'.
                '</th> 
              </tfoot>';
	break;

    case 'listar':
        $rspta = $ingresoM->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object())
        {
            $data[]=array(
                "0"=> ($reg->estado == "Aceptado")
                      ?'<button class="btn btn-warning" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>'.
                       '<button class="btn btn-danger"  onclick="anular('.$reg->idingreso.')"><i class="fa fa-close"></i></button>'
                      :'<button class="btn btn-warning" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>',
                "1"=>$reg->fecha,
                "2"=>$reg->proveedor,
                "3"=>$reg->usuario,
                "4"=>$reg->tipo_comprobante,
                "5"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
                "6"=>$reg->total_compra,
                "7"=>($reg->estado =="Aceptado")
                     ?'<span class="label bg-green">Aceptado</span>'
                     :'<span class="label bg-red">Anulado</span>'
            );
        }
        $results = array(
                "sEcho"=>1, //Información para el datatables
                "iTotalRecords"=>count($data), //enviamos el total registros al datatable
                "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
                "aaData"=>$data);
            echo json_encode($results);
    break;

    case 'selectProveedor':
        require_once '../modelos/Persona.php';
        $personaM = new Persona();
        $rspta = $personaM->listarp();

        while ($reg = $rspta->fetch_object())
        {
            echo '<option value='.$reg->idpersona.'>'.$reg->nombre.'</option>';
        }
    break;

    case 'listarArticulos':
        require_once '../modelos/Articulo.php';
        $articuloM = new Articulo();
        $rspta = $articuloM->listarActivos();
        $data = Array();

        while ($reg = $rspta->fetch_object())
        {
            $data[]=array(
                "0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\')"><span class="fa fa-plus"></span></button>',
                "1"=>$reg->nombre,
                "2"=>$reg->categoria,
                "3"=>$reg->codigo,
                "4"=>$reg->stock,
                "5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >"
            );
        }
        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);
    break;

    case 'guardaryeditar':
        if (isset($idingreso)){
            $rspta = $ingresoM->insertar(
                $idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto,
                $total_compra, $_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_venta"]);
                
                echo $rspta ? "Ingreso registrado" : "No se pudieron registrar todos los datos del ingreso";
        } else {
            # code...
        }
                        
    break;
    
}
?>