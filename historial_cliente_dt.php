<?php
/**
 * This file is part of the OpenPyme1.
 * 
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 * 
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

    /*
        DATATABLE PARA MOSTRAR EL HISTORIAL DE TRABAJOS DE CADA CLIENTE
    */
    include("_core.php");

    $requestData= $_REQUEST;
    $fechai= MD($_REQUEST['fechai']);
    $fechaf= MD($_REQUEST['fechaf']);
    $id_cliente= $_REQUEST['id_cliente'];

    require('ssp.customized.class.php');
    // DB table to use
    $table = 'factura';
    // Table's primary key
    $primaryKey = 'id_factura';

    // MySQL server connection information
    $sql_details = array(
    'user' => $username,
    'pass' => $password,
    'db'   => $dbname,
    'host' => $hostname
    );
    //permiso del script
    $id_user=$_SESSION["id_usuario"];
    $admin=$_SESSION["admin"];
    $uri = $_SERVER['SCRIPT_NAME'];
    $filename=get_name_script($uri);
    $links=permission_usr($id_user, $filename);

    $id_sucursal=$_SESSION['id_sucursal'];

    $joinQuery = "
    FROM factura
    INNER JOIN reparaciones ON factura.id_factura = reparaciones.id_factura
    INNER JOIN empleado ON reparaciones.id_empleado = empleado.id_empleado
	";
    $extraWhere = "factura.fecha BETWEEN '$fechai' AND '$fechaf' 
    AND factura.finalizada = 1 AND factura.id_cliente ='$id_cliente'";
    //$extraWhere = "";
    $columns = array(
        array( 'db' => 'empleado.nombre', 'dt' => 0, 'field' => 'nombre' ),
        array( 'db' => 'reparaciones.numero_placa', 'dt' =>1, 'field' => 'numero_placa' ),
        array( 'db' => 'reparaciones.fecha_ingreso', 'dt' => 2, 'field' => 'fecha_ingreso' ),
        array( 'db' => 'factura.fecha', 'dt' => 3, 'field' => 'fecha' ),
        array( 'db' => 'factura.total', 'dt' =>4, 'field' => 'total' ),
        array( 'db' => 'factura.finalizada', 'dt' => 5, 'formatter' => function ($idtransace, $row) {
                $txt_estado=estado($idtransace);
                return $txt_estado;
            },'field' => 'finalizada'),
        
        array( 'db' => 'factura.id_factura', 'dt' => 6, 'formatter' => function ($id_cliente, $row) {
            $menudrop="<div class='btn-group'>
            <a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
            <ul class='dropdown-menu dropdown-primary'>";    
            $menudrop.="<li><a target='_blank' href=\"imprimir_hoja_tecnica.php?id_cliente=".$_REQUEST['id_cliente']."&id_factura=".$row['id_factura']."\"><i class=\"fa fa-file\"></i> Impimir hoja técnica</a></li>";
            $menudrop.="</ul>
					</div>";    
            return $menudrop;
            },'field' => 'id_factura' ),
    );
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
function estado($idtransace)
{
    $txt_estado="";
    if ($idtransace==0) {
        $txt_estado="<h5 class='text-warning'>".'PENDIENTE'."</h5>";
    } else {
        $txt_estado="<h5 class='text-success'>".'FACTURADO'."</h5>";
    }

    return $txt_estado;
}
