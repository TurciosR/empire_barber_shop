<?php
include ("_core.php");
function initial()
{
	$id_servicio = $_REQUEST['id_servicio'];
    $sql="SELECT * FROM servicios WHERE id_servicio='$id_servicio'";
    $result = _query($sql);

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];

	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);
	//permiso del script
	?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title">Borrar servicio</h4>
	</div>
	<div class="modal-body">
		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row" id="row1">
				<div class="col-lg-12">
					<?php
					//permiso del script
					if ($links!='NOT' || $admin=='1' ){
						?>
					<table	class="table table-bordered table-striped" id="tableview">
						<thead>
							<tr>
								<th class="col-lg-4">Campo</th>
								<th class="col-lg-4">Detalle</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while ($row = _fetch_array($result))
								{
									$id_servicio=$row["id_servicio"];
									$nombre_servicio=$row["descripcion"];
									$valor_servicio=$row["precio_venta"];
									$estado=$row["estado"];
									
									if($estado == 1)
									{
										$estado_servicio = "Activo";
									}
									else
									{
										$estado_servicio = "Inactivo";
									}
									
									echo"<tr><td>Id </td><td>".$id_servicio."</td></tr>";
									echo"<tr><td>Nombre del servicio</td><td>".$nombre_servicio."</td></tr>";
									echo"<tr><td>Precio de venta del servicio</td><td>$".$valor_servicio."</td></tr>";
									echo"<tr><td>Estado</td><td>".$estado_servicio."</td></tr>";
								}
							?>
						</tbody>
					</table>
					
				</div>
				<?php
				echo "<input type='hidden' nombre='id_servicio' id='id_servicio' value='$id_servicio'>";
				?>
			</div>
		</div>
		<!--/modal-footer -->
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="btnDelete">Borrar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
	</div>	
<?php
	} //permiso del script
	else {
		echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
	}
}
function deleted()
{
	$id_servicio = $_POST ['id_servicio'];
	$table = 'servicios';
	$where_clause = "id_servicio=" . $id_servicio;
	$delete = _delete ( $table, $where_clause );
	if ($delete)
	{
		$xdatos ['typeinfo'] = 'Success';
		$xdatos ['msg'] = 'Registro eliminado con exito!';
	}
	else
	{
		$xdatos ['typeinfo'] = 'Error';
		$xdatos ['msg'] = 'Registro no pudo ser eliminado!';
	}
	echo json_encode ( $xdatos );
}
if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'formDelete' :
				initial();
				break;
				case 'deleted' :
				deleted();
				break;
			}
		}
	}

	?>
