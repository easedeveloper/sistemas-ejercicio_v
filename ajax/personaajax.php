<?php
/* 1.requerimos el modelo
2.creamos e iniciamos el objeto
3.creamos la variable+isset+postobjeto+limpiarcadena+postobjeto
4.switch: guardaryeditar eliminar mostrar listarp listarc */

require_once '../modelos/Persona.php';

$personaM = new Persona();

$idpersona      = isset($_POST["idpersona"])     ? limpiarcadena($_POST["idpersona"])      : "";
$tipo_persona   = isset($_POST["tipo_persona"])  ? limpiarcadena($_POST["tipo_persona"])   : "";
$nombre         = isset($_POST["nombre"])        ? limpiarcadena($_POST["nombre"])         : "";
$tipo_documento = isset($_POST["tipo_documento"])? limpiarcadena($_POST["tipo_documento"]) : "";
$num_documento  = isset($_POST["num_documento"]) ? limpiarcadena($_POST["num_documento"])  : "";
$direccion      = isset($_POST["direccion"])     ? limpiarcadena($_POST["direccion"])      : "";
$telefono       = isset($_POST["telefono"])      ? limpiarcadena($_POST["telefono"])       : "";
$email          = isset($_POST["email"])         ? limpiarcadena($_POST["email"])          : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idpersona)){
            $rspta = $personaM->insetar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
            echo $rspta ? "Persona registrada" : "Persona no se pudo registrar";
        } else {
            $rspta = $personaM->editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
            echo $rspta ? "Persona Actualizada" : "Persona no se pudo Actualizar";
        }
    break;

    case 'eliminar':
        $rspta = $personaM->eliminar($idpersona);
        echo $rspta ? "Persona eliminada" : "Persona no se puede eliminar";
    break;

    case 'mostrar':
        $rspta = $personaM->mostrar($idpersona);
        echo json_encode($rspta);
    break;

    case 'listarp':
    /* 1.traer el metodo listap
    2.crear variable Array
    3.recorrer la varaible rspta
    4.usar el array data
    5.crear la tabla con sus campos */
    $rspta = $personaM->listarp();
    $data = Array();

    while ($reg = $rspta->fetch_object()) {
        $data[] = array(
            "0"=>'<button class="btn btn-warning" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
                 '<button class="btn btn-danger"  onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',
            "1"=>$reg->nombre,
            "2"=>$reg->tipo_documento,
            "3"=>$reg->num_documento,
            "4"=>$reg->telefono,
            "5"=>$reg->email
        );
    }
    $results = array(
        "sEcho"=>1, //Información para el datatables
        "iTotalRecords"=>count($data), //enviamos el total registros al datatable
        "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
        "aaData"=>$data);
    echo json_encode($results);
    break;


    case 'listarc':
    /* 1.traer el metodo listap
    2.crear variable Array
    3.recorrer la varaible rspta
    4.usar el array data
    5.crear la tabla con sus campos */
    $rspta = $personaM->listarc();
    $data = Array();

    while ($reg = $rspta->fetch_object()) {
        $data[] = array(
            "0"=>'<button class="btn btn-warning" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
                 '<button class="btn btn-danger"  onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',
            "1"=>$reg->nombre,
            "2"=>$reg->tipo_documento,
            "3"=>$reg->num_documento,
            "4"=>$reg->telefono,
            "5"=>$reg->email
        );
    }
    $results = array(
        "sEcho"=>1, //Información para el datatables
        "iTotalRecords"=>count($data), //enviamos el total registros al datatable
        "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
        "aaData"=>$data);
    echo json_encode($results);
    break;
}
?>