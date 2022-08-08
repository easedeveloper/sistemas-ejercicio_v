<?php
require '../config/conexion.php';

Class Venta
{
    public function __construct(){}

    public function insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento)
    {
        $sql="INSERT INTO venta (idcliente,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_venta,estado)
		VALUES ('$idcliente','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_venta','Aceptado')";
		
        $ventaidnew = ejecutarConsulta_retornarID($sql);

        $num_element = 0;
        $sw=true;

        while ($num_element < count($idarticulo))
        {
            $sql_detalle="INSERT INTO detalle_venta(idventa,idarticulo,cantidad,precio_venta,descuento)
            VALUES('$ventaidnew','$idarticulo[$num_element]','$cantidad[$num_element]','$precio_venta[$num_element]','$descuento[$num_element]')";
            ejecutarConsulta($sql_detalle) or $sw=false;
            $num_element++;
        }
        return $sw;
    }

    public function anular($idventa)
    {
        $sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idventa)
    {
        $sql="SELECT v.idventa, DATE(v.fecha_hora) AS fecha, v.idcliente, p.nombre AS cliente, 
        v.idusuario, u.nombre as usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante,
        v.impuesto, v.total_venta, v.estado
        FROM venta v 
        INNER JOIN persona p ON v.idcliente=p.idpersona
        INNER JOIN usuario u ON v.idusuario=u.idusuario
        WHERE v.idventa='$idventa'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarDetalle($idventa)
    {
        $sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,
		(dv.cantidad*dv.precio_venta-dv.descuento) AS subtotal
		FROM detalle_venta dv 
		INNER JOIN articulo a ON dv.idarticulo=a.idarticulo 
		WHERE dv.idventa='$idventa'";
		return ejecutarConsulta($sql);                
    }

    public function listar()
    {
        $sql="SELECT v.idventa, DATE(v.fecha_hora) AS fecha, v.idcliente, p.nombre AS cliente, 
        v.idusuario, u.nombre as usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante,
        v.impuesto, v.total_venta, v.estado
        FROM venta v 
        INNER JOIN persona p ON v.idcliente=p.idpersona
        INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa DESC";
        return ejecutarConsulta($sql);
    }



}




?>