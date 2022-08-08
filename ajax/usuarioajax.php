<?php
session_start();
require_once '../modelos/Usuario.php';

$usuarioM = new Usuario();

$idusuario      = isset($_POST["idusuario"])      ? limpiarcadena($_POST["idusuario"]) : "";
$nombre         = isset($_POST["nombre"])         ? limpiarcadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarcadena($_POST["tipo_documento"]) : "";
$num_documento  = isset($_POST["num_documento"])  ? limpiarcadena($_POST["num_documento"]) : "";
$direccion      = isset($_POST["direccion"])      ? limpiarcadena($_POST["direccion"]) : "";
$telefono       = isset($_POST["telefono"])       ? limpiarcadena($_POST["telefono"]) : "";
$email          = isset($_POST["email"])          ? limpiarcadena($_POST["email"]) : "";
$cargo          = isset($_POST["cargo"])          ? limpiarcadena($_POST["cargo"]) : "";
$login          = isset($_POST["login"])          ? limpiarcadena($_POST["login"]) : "";
$clave          = isset($_POST["clave"])          ? limpiarcadena($_POST["clave"]) : "";
$imagen         = isset($_POST["imagen"])         ? limpiarcadena($_POST["imagen"]) : "";


switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
		{
			$imagen=$_POST["imagenactual"];
		}
		else 
		{
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png")
			{
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
			}
		}
        $clavehash=hash("SHA256",$clave);
        if (empty($idusuario))
        {
            //$_POST['permiso']enviando los permisos por el metodo POST
            $rspta = $usuarioM->insetar($nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clavehash,$imagen,$_POST['permiso']);
            echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
        }
        else
        {
            //$_POST['permiso']enviando los permisos por el metodo POST
            $rspta = $usuarioM->editar($idusuario,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clavehash,$imagen,$_POST['permiso']);
            echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
        }

    break;

    case 'desactivar':
        $rspta = $usuarioM->desactivar($idusuario);
        echo $rspta ? 'Usuario Desactivado' : 'Usuario No se pudo Desactivar';
    break;

    case 'activar':
        $rspta = $usuarioM->activar($idusuario);
        echo $rspta ? 'Usuario Activado' : 'Usuario No se pudo Activar';
    break;

    case 'mostrar':
        $rspta = $usuarioM->mostrar($idusuario);
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $usuarioM->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0"=>($reg->condicion)
                     ?'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
                      '<button class="btn btn-danger"  onclick="desactivar('.$reg->idusuario.')"><i class="fa fa-close"></i></button>'
                     :'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
                      '<button class="btn btn-primary" onclick="activar('.$reg->idusuario.')"><i class="fa fa-check"></i></button>',
                "1"=>$reg->nombre,
                "2"=>$reg->tipo_documento,
                "3"=>$reg->num_documento,
                "4"=>$reg->telefono,
                "5"=>$reg->email,
                "6"=>$reg->login,
                "7"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px' >",
                "8"=>($reg->condicion)
                     ?'<span class="label bg-green">Activado</span>'
                     :'<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);
    break;

    case 'permisos':
        require_once '../modelos/Permiso.php';
        $permisoM = new Permisos();
        $rspta = $permisoM->listar();

        $idusuario = $_GET["id"];
        $marcados = $usuarioM->listarmarcados($idusuario);

        $valores = array();

        while ($per = $marcados->fetch_object())
        {
            array_push($valores, $per->idpermiso);
        }

        while ($reg = $rspta->fetch_object())
        {
            $sw = in_array($reg->idpermiso, $valores)
                  ?'checked'
                  :'';

            echo '<li> <input type="checkbox" '.$sw.' name="permiso[]" value="'.$reg->idpermiso.'">'.$reg->nombre.'</li>';
        }

    break;

    case 'verificar':
        $logina = $_POST['logina'];
        $clavea = $_POST['clavea'];

        $clavehash = hash("SHA256", $clavea);
        $rspta = $usuarioM->verificar($logina, $clavehash);
        $fetch = $rspta->fetch_object();

        if (isset($fetch))
        {
            $_SESSION['idusuario']=$fetch->idusuario;
            $_SESSION['nombre']=$fetch->nombre;
            $_SESSION['imagen']=$fetch->imagen;
            $_SESSION['login']=$fetch->login;

            $marcados = $usuarioM->listarmarcados($fetch->idusuario);

            $valores = array();

            while ($per = $marcados->fetch_object()) {
                array_push($valores, $per->idpermiso);
            }

            in_array(1,$valores)? $_SESSION['escritorio']= 1 : $_SESSION['escriotorio']= 0;
            in_array(2,$valores)? $_SESSION['almacen']=1	 :$_SESSION['almacen']=0;
			in_array(3,$valores)? $_SESSION['compras']=1	 :$_SESSION['compras']=0;
			in_array(4,$valores)? $_SESSION['ventas']=1	     :$_SESSION['ventas']=0;
			in_array(5,$valores)? $_SESSION['acceso']=1	     :$_SESSION['acceso']=0;
			in_array(6,$valores)? $_SESSION['consultac']=1   :$_SESSION['consultac']=0;
			in_array(7,$valores)? $_SESSION['consultav']=1   :$_SESSION['consultav']=0;

            //DESPUES NOS VAMOS A LAS VISTAS PARA VALIDAR TAMBIEN LOS PERMISOS

        }
        echo json_encode($fetch);
    break;

    case 'salir':
        session_unset();
        session_destroy();
        header("Location: ../index.php");
    break;

}


?>