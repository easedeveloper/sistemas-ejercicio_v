<?php
/* 1.requerir la conexion
2.crear la clase
3.crear la funcion del constructor
4.crear el metodo insertar, editar, eliminar, mostrar, listarp, listarc */

require '../config/conexion.php';

Class Persona{
    public function __construct(){}


    public function insetar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
    {
        $sql = "INSERT INTO persona(tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email)
        VALUES('$tipo_persona', '$nombre', '$tipo_documento', '$num_documento', '$direccion', '$telefono', '$email')";
        return ejecutarConsulta($sql);
    }

    public function editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email)
    {
        $sql = "UPDATE persona SET tipo_persona='$tipo_persona', nombre='$nombre', tipo_documento='$tipo_documento', num_documento='$num_documento', direccion='$direccion', telefono='$telefono', email='$email'
        WHERE idpersona='$idpersona'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($idpersona)
    {
        $sql = "DELETE FROM persona WHERE idpersona='$idpersona'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idpersona)
    {
        $sql = "SELECT * FROM persona WHERE idpersona='$idpersona'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarp()
    {
        /* creando la consulta sql */
        $sql = "SELECT * FROM persona WHERE tipo_persona='Proveedor'";
        return ejecutarConsulta($sql);
    }

    public function listarc()
    {
        /* creando la consulta sql */
        $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente'";
        return ejecutarConsulta($sql);
    }

   

    



}


?>