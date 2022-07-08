<?php
include("_core.php");

$requestData= $_REQUEST;

require('ssp.customized.class.php');
// DB table to use
$table = 'servicios';
// Table's primary key
$primaryKey = 'id_servicio';

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

$joinQuery = "FROM servicios";
$extraWhere = "";
$columns = array(
    //EXTRAYENDO REGISTROS DE LA BASE DE DATOS Y COLOCANDOLOS EN DATATABLE
    array( 'db' => 'servicios.id_servicio', 'dt' => 0, 
        'formatter' => function ($id_servicio, $row) {
            return "<input type='hidden' name='id_servicio_active' id='id_servicio_active' value='".$id_servicio."'>".$id_servicio;
        },
    'field' => 'id_servicio' ),
    array( 'db' => 'servicios.descripcion', 'dt' => 1, 'field' => 'descripcion' ),
    array( 'db' => 'servicios.estado', 'dt' => 2, 'formatter' => function ($estado, $row) {
            if ($estado==0) {
                // code...
                return "<label style='color:red'>Inactivo</label><input type='hidden' name='estado' id='estado' value='".$estado."'>";
            }
            else {
                return "<label style='color:blue'>Activo</label><input type='hidden' name='estado' id='estado' value='".$estado."'>";
            }
    }, 'field' => 'estado'),
    array( 'db' => 'servicios.id_servicio', 'dt' => 3, 'formatter' => function ($id_servicio, $row) {
        $menudrop="<div class='btn-group'>
		<a href='#' data-toggle='dropdown' class='btn btn-primary dropdown-toggle'><i class='fa fa-user icon-white'></i> Menu<span class='caret'></span></a>
		<ul class='dropdown-menu dropdown-primary'>";
        include("_core.php");

        $sql_s = _query("SELECT * FROM servicios WHERE id_servicio = ".$id_servicio);
        $row_s = _fetch_array($sql_s);
        $estado = $row_s['estado'];
        if($estado == 1)
        {
            $text1 = "Desactivar";
            $fa = "fa fa-eye-slash";
        }
        else
        {
            $text1 = "Activar";
            $fa = "fa fa-eye";
        }

        $id_user=$_SESSION["id_usuario"];
        //$id_sucursal=$_SESSION['id_sucursal'];
        $admin=$_SESSION["admin"];
        $filename='editar_servicio.php';
        $link=permission_usr($id_user, $filename);
        if ($link!='NOT' || $admin=='1') {
            $menudrop.= "<li><a data-toggle='modal' href='editar_servicio.php?id_servicio=".$row['id_servicio']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-pencil\"></i>Editar</a></li>";
        }
        // $filename='borrar_servicio.php';
        // $link=permission_usr($id_user, $filename);
        // if ($link!='NOT' || $admin=='1') {
        //     $menudrop.= "<li><a data-toggle='modal' href='borrar_servicio.php?id_servicio=" .  $row ['id_servicio']."&process=formDelete"."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-eraser\"></i> Eliminar</a></li>";
        // }
        $filename='ver_servicio.php';
        $link=permission_usr($id_user, $filename);
        if ($link!='NOT' || $admin=='1') {
            $menudrop.= "<li><a data-toggle='modal' href='ver_servicio.php?id_servicio=".$row['id_servicio']."' data-target='#viewModal' data-refresh='true'><i class=\"fa fa-search\"></i> Ver Detalle</a></li>";
        }
        $filename='admin_servicios.php';
        $link=permission_usr($id_user, $filename);
        if ($link!='NOT' || $admin=='1') {
            $menudrop.= "<li><a id='estado' ><i class='".$fa."'></i> ".$text1."</a></li>";
        }

        $menudrop.="</ul>
					</div>";
        return $menudrop;
    },'field' => 'id_servicio' ),

);
echo json_encode(
	SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
