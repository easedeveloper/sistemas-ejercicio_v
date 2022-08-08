<?php
    /**
     * 1 requerimos el archivo global
     * 2 creamos la conexion con la constantes
     * 3 creamos en mysqlquery y enviamos el ENCONDE
     * 4 validamos la falla de conexion con la DB
     * 5 verificando si la funcion existe y creando(ejecutarConsulta, ejecutarConsultaSimpleFila, ejecutarConsulta_retornarID, limpiarcadena)
    */

    require_once 'global.php';

    $conex = new mysqli( DB_HOST, DB_USUARIO, DB_PASSWORD, DB_NAME, '3306' );
    mysqli_query( $conex, 'SET NAMES "'.DB_ENCODE.'"' );
    if ( mysqli_connect_error() ) {
        printf("Falló la conexion ala DB: %s\n", mysqli_connect_error());
    }

    if ( !function_exists('ejecutarConsulta') ) {
        function ejecutarConsulta( $sql ){
            global $conex;
            $query = $conex->query( $sql );
            return $query;
        }

        function ejecutarConsultaSimpleFila( $sql ){
            global   $conex;
            $query = $conex->query( $sql );
            $row   = $query->fetch_assoc();
            return $row;
        }

        function ejecutarConsulta_retornarID( $sql ){
            global $conex;
            $query = $conex -> query( $sql );
            return $conex ->insert_id;
        }

        function limpiarcadena( $str ){
            global $conex;
            $str = mysqli_real_escape_string( $conex, trim($str) );
            return htmlspecialchars( $str );
        }
    }


?>