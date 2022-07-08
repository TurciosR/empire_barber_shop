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
        INTERFAZ PARA VISUALIZAR EL HISTORIAL DE TRABAJOS DE CADA CLIENTE
    */
	include ("_core.php");
	// Page setup
	$_PAGE = array ();
	$title='Historial de trabajos de cliente';
	$_PAGE ['title'] = $title;
	$_PAGE ['links'] = null;
	$_PAGE ['links'] .= '<link href="css/bootstrap.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="font-awesome/css/font-awesome.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/iCheck/custom.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/chosen/chosen.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/select2/select2.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/animate.css" rel="stylesheet">';
	$_PAGE ['links'] .= '<link href="css/style.css" rel="stylesheet">';
	include_once "header.php";
	include_once "main_menu.php";

	$id_sucursal=$_SESSION["id_sucursal"];
	$id_cliente = $_REQUEST['id_cliente'];

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

    //fechas predeterminadas para controles de fechas
	$fechahoy=ED(date("Y-m-d"));
	$fechaanterior=ED(restar_dias($fechahoy,30));

    //seleccion del nombre de acuerdo al id en la URL
	$sql_cliente=_query("SELECT cliente.nombre FROM cliente WHERE id_cliente=$id_cliente");
	$row_cliente=_fetch_array($sql_cliente);
	$nombre_cliente=$row_cliente['nombre'];
?>

	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row" id="row1">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<?php
					//permiso del script
					if ($links!='NOT' || $admin=='1' ){
					echo"
	                <div class='ibox-title'>
	                    <h3>$title: <label class='badge blue-bg text-uppercase' style='margin-left: 10px; font-size: 1.5rem;' >$nombre_cliente</label></h3>
	                </div>";
                ?>
						<div class="ibox-content">
							<!--load datables estructure html-->
							<input type="hidden" placeholder="Fecha Inicio" class="datepick form-control" id="id_cliente" name="id_cliente" value="<?php echo  $id_cliente;?>">
							<div class="row">
								<div class="input-group">
									<div class="col-md-4">
										<div class="form-group">
											<label>Fecha Inicio</label>
											<input type="text" placeholder="Fecha Inicio" class="datepick form-control date" id="fecha_inicio" name="fecha_inicio" value="<?php echo  $fechaanterior;?>">
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label>Fecha Fin</label>
											<input type="text" placeholder="Fecha Fin" class="datepick form-control date" id="fecha_fin" name="fecha_fin" value="<?php echo $fechahoy;?>">
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<div><label>Buscar Trabajos</label> </div>
											<button type="button" id="btnMostrar" name="btnMostrar" class="btn btn-primary"><i class="fa fa-search"></i> Mostrar</button>
										</div>
									</div>
								</div>

							</div>
							<section>
								<table class="table table-striped table-bordered table-hover" id="editable2">
									<thead>
										<tr>
											<th>Empleado a cargo</th>
											<th>No. de Placa</th>
											<th>Fecha de Ingreso</th>
											<th>Fecha de Salida</th>
                                            <th>Monto total</th>
											<th>Estado</th>
											<th>Acci&oacute;n</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								<input type="hidden" name="autosave" id="autosave" value="false-0">
							</section>
						</div>
						<!--div class='ibox-content'-->
				</div>
				<!--<div class='ibox float-e-margins' -->
			</div>
			<!--div class='col-lg-12'-->
		</div>
		<!--div class='row'-->
	</div>
	<!--div class='wrapper wrapper-content  animated fadeInRight'-->
	<?php
	include("footer.php");
	echo" <script type='text/javascript' src='js/funciones/funciones_historial_cliente.js'></script>";
		} //permiso del script
else {
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}

?>
