<?php
    require '../config/conexion.php';

    Class Ingreso{
        public function __construct(){}

        public function insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante,
            $fecha_hora, $impuesto, $total_compra, $idarticulo, $cantidad, $precio_compra, $precio_venta)
        {
            $sql = "INSERT INTO ingreso(idproveedor,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_compra,estado)
            VALUES('$idproveedor','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_compra','Aceptado')";
            $idingresoNew = ejecutarConsulta_retornarID($sql);

            $num_elemen = 0;
            $sw = true;

            while ($num_elemen < count($idarticulo))
            {
                $sql_detalle = "INSERT INTO detalle_ingreso(idingreso, idarticulo, cantidad, precio_compra, precio_venta)
                VALUES('$idingresoNew', '$idarticulo[$num_elemen]', '$cantidad[$num_elemen]', '$precio_compra[$num_elemen]', '$precio_venta[$num_elemen]')";
                ejecutarConsulta($sql_detalle) or $sw=false;
                $num_elemen++;
            }
            return $sw;
        }

        public function anular($idingreso)
        {
            $sql = "UPDATE ingreso SET estado='Anulado' WHERE idingreso='$idingreso'";
            return ejecutarConsulta($sql);
        }

        public function mostrar($idingreso)
        {
            $sql="SELECT i.idingreso, DATE(i.fecha_hora) as fecha, i.idproveedor, p.nombre as proveedor, u.idusuario,
            u.nombre as usuario,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,
            i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona 
            INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE i.idingreso='$idingreso'";
            return ejecutarConsultaSimpleFila($sql);            
        }
        
        public function listarDetalle($idingreso)
        {
            $sql="SELECT di.idingreso,di.idarticulo,a.nombre,di.cantidad,di.precio_compra,di.precio_venta
            FROM detalle_ingreso di 
            INNER JOIN articulo a ON di.idarticulo=a.idarticulo
            WHERE di.idingreso='$idingreso'";
            return ejecutarConsulta($sql);
        }

        public function listar()
        {
            $sql = "SELECT i.idingreso, DATE(i.fecha_hora) AS fecha, i.idproveedor, p.nombre AS proveedor,
            u.idusuario, u.nombre AS usuario, i.tipo_comprobante, i.serie_comprobante, i.num_comprobante,
            i.impuesto, i.total_compra, i.estado
            FROM ingreso i
            INNER JOIN persona p ON i.idproveedor=p.idpersona
            INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.idingreso DESC";
            return ejecutarConsulta($sql);
        }
    }
?>