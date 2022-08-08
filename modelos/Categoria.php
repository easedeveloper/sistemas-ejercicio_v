<?php
    /**
     * 1 Requerir la conexion
     * 2 Creando la Clase Categoria
     * 3 Creando el constructor
     * 4 Creando el metodo insertar, editar, desactivarCatego, activarCatego, mostrarrRegis, listarAll, select
    */

    require '../config/conexion.php';

    class Categoria{

        public function __construct(){}

        public function insertarCATE( $nombre, $descripcion )
        {
            $sql = "INSERT INTO categoria(nombre, descripcion, condicion)
                    VALUES('$nombre','$descripcion','1')";

            return ejecutarConsulta($sql);
        }

        public function editarCATE( $idCategoria, $nombre, $descripcion )
        {
            $sql= "UPDATE categoria SET nombre='$nombre', descripcion='$descripcion'
                   WHERE idcategoria='$idCategoria'";
            
            return ejecutarConsulta($sql);
        }

        public function desactivarCATE( $idCategoria )
        {
            $sql= "UPDATE categoria SET condicion='0' WHERE idcategoria='$idCategoria'";

            return ejecutarConsulta( $sql );
        }

        public function activarCATE( $idCategoria )
        {
            $sql= "UPDATE categoria SET condicion='1' WHERE idcategoria='$idCategoria'";
            return ejecutarConsulta( $sql );
        }

        public function mostrarRegisCATE( $idCategoria )
        {   
            $sql= "SELECT * FROM categoria WHERE idcategoria='$idCategoria'";

            return ejecutarConsultaSimpleFila( $sql );
        }

        public function listarAllCATE()
        {
            $sql= "SELECT * FROM categoria";

            return ejecutarConsulta( $sql );
        }

        public function selectCondiCATE()
        {
            $sql= "SELECT * FROM categoria WHERE condicion= 1";

            return ejecutarConsulta( $sql );
        }


    }




?>