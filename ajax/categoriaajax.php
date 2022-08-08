<?php
    /**
     * 1. requerir modelo de categoria
     * 2. crear el objeto del modelo inicializar
     * 3. crear variable de idecategoria, nombre, descripcion, validar isset del formulario html 
     * 4. validando condicion-casos con swtich
     *  4.1 guardaryeditar, desactivar, activar, mostrar, listar
    */

    require_once '../modelos/Categoria.php';

    $categoriaModel = new Categoria();

    $idCategoria = isset( $_POST["idcategoria"] )
         ? limpiarcadena( $_POST["idcategoria"] )
         : "";

    $nombre = isset( $_POST["nombre"] )
         ? limpiarcadena( $_POST["nombre"] )
         : "";

    $descrip = isset( $_POST["descripcion"] )
         ? limpiarcadena( $_POST["descripcion"] )
         : "";

    
    switch ($_GET["op"]) {
        case 'guardaryeditar':
            if ( empty($idCategoria) ) {
                $rspta = $categoriaModel->insertarCATE( $nombre, $descrip );
                echo $rspta ? "Categoria Registrada" : "Categoria no se pudo Registrar";
            }else{
                $rspta = $categoriaModel->editarCATE( $idCategoria, $nombre, $descrip );
                echo $rspta ?  "Categoria Actualizada" : "Categoria no se pudo Actualizar";
            }
        break;
        
        case 'desactivar':
            $rspta = $categoriaModel->desactivarCATE( $idCategoria );
            echo $rspta ? "Categoria Desactivada" : "Categoria Activada";
        break;

        case 'activar':
            $rspta  = $categoriaModel->activarCATE( $idCategoria );
            echo $rspta ? "Categoria Activada" : "Categoria No se pudo Activar";
        break;

        case 'mostrar':
            $rspta = $categoriaModel->mostrarRegisCATE( $idCategoria );
            echo json_encode( $rspta );
        break;

        case 'listar':
            $rspta = $categoriaModel->listarAllCATE();
            $data = array();

            while ( $regis = $rspta->fetch_object() ) {
                $data[] = array(
                    "0"=>($regis->condicion)
                        ? '<button class="btn btn-warning" onclick="mostrar('.$regis->idcategoria.')"><i class="fa fa-pencil"></i></button>'.
                          '<button class="btn btn-danger"  onclick="desactivar('.$regis->idcategoria.')"><i class="fa fa-close"></i></button>'
                        : '<button class="btn btn-warning" onclick="mostrar('.$regis->idcategoria.')"><i class="fa fa-pencil"></i></button>'.
                          '<button class="btn btn-primary" onclick="activar('.$regis->idcategoria.')"><i class="fa fa-check"></i></button>',
                    "1"=>$regis->nombre,
                    "2"=>$regis->descripcion,
                    "3"=>($regis->condicion)
                        ? '<span class="label bg-green">Activado</span>'
                        : '<span class="label bg-red">Desactivado</span>',
                );
            }
            $result = array(
                "sEcho"=>1,
                "iTotalRecors"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data
            );
            echo json_encode($result);
        break;
    }



?>