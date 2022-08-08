<?php
require_once '../modelos/Permiso.php';

$permisoM = new Permisos();

switch ($_GET["op"]) {
    case 'listar':
        $rspta = $permisoM->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0"=>$reg->nombre
            );
        }
        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);
    break;
    
    default:
        # code...
        break;
}



?>