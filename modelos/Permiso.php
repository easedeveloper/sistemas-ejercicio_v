<?php

require '../config/conexion.php';

Class Permisos
{
    public function __construct(){}

    public function listar()
    {
        $sql = "SELECT * FROM permiso";
        return ejecutarConsulta($sql);
    }
}
?>