<?php
/* 1.requerir el modelo
2.inicializar el modelo
3.compilar POST
4.crear switch
  crear case */

require_once '../modelos/Articulo.php';

$articuloM = new Articulo();

$idarticulo  = isset($_POST["idarticulo"]) ? limpiarcadena($_POST["idarticulo"]) :"";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) :"";
$codigo      = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) :"";
$nombre      = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) :"";
$stock       = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) :"";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) :"";
$imagen      = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) :"";


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
                // el nombre de la imagen se va a renombrar usando microtime, se concatena y finaliza con la extension de la imagen. Para no tener imagenes repetidas en nuestras carpetas
				$imagen = round(microtime(true)) . '.' . end($ext);
                //Cargamoas la imagen del proyecto
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/" . $imagen);
			}
		}
        
        if (empty($idarticulo)) {
            $rspta = $articuloM->insertar($idcategoria,$codigo,$nombre,$stock,$descripcion,$imagen);
            echo $rspta ? "Artículo registrado" : "Artículo no se pudo registrar";
        }else
        {
            $rspta = $articuloM->editar($idarticulo,$idcategoria,$codigo,$nombre,$stock,$descripcion,$imagen);
            echo $rspta ? "Artículo actualizado" : "Artículo no se pudo actualizar";
        }
    break;

    case 'desactivar':
        $rspta = $articuloM->desactivar($idarticulo);
        echo $rspta ? "Artículo Desactivado" : "Artículo no se puede desactivar";
    break;
    
    case 'activar':
        $rspta = $articuloM->activar($idarticulo);
        echo $rspta ? "Artículo activado" : "Artículo no se puede activar";
    break;

    case 'mostrar':
        $rspta = $articuloM->mostrar($idarticulo);
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $articuloM->listar();
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0"=>($reg->condicion)
                     ? '<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
                       ' <button class="btn btn-danger" onclick="desactivar('.$reg->idarticulo.')"><i class="fa fa-close"></i></button>'
                     : '<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
 					   '<button class="btn btn-primary" onclick="activar('.$reg->idarticulo.')"><i class="fa fa-check"></i></button>',
                "1"=>$reg->nombre,
                "2"=>$reg->categoria,
                "3"=>$reg->codigo,
                "4"=>$reg->stock,
                "5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >",
                "6"=>($reg->condicion)
                     ? '<span class="label bg-green">Activado</span>'
                     : '<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);
        break;

    
    case "selectCategoria":
            require_once "../modelos/Categoria.php";
            $categoria = new Categoria();
    
            $rspta = $categoria->selectCondiCATE();
    
            while ($reg = $rspta->fetch_object())
                    {
                        echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
                    }
    break;
}


?>