<?php
include ("_core.php");
$id_servicio_request = $_REQUEST['id_servicio'];
$sql="SELECT * FROM servicios WHERE id_servicio='$id_servicio_request'";
$result = _query($sql);
$count = _num_rows($result);

//permiso del script
$id_user=$_SESSION["id_usuario"];
$admin=$_SESSION["admin"];
$uri = $_SERVER['SCRIPT_NAME'];
$filename=get_name_script($uri);
$links=permission_usr($id_user,$filename);
//permiso del script
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Datos del servicio</h4>
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
									echo"<tr><td>Estado</td><td>".$estado_servicio."</td></tr>";
								}
							?>
						</tbody>
					</table>
					<h2>Listado de Precios</h2>
					<table class="table table-bordered table-striped" id="tableview">
						<thead>
							<tr>
								<th class="col-lg-4"># de Precio</th>
								<th class="col-lg-4">Precio de Venta</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sql="SELECT * FROM precios_servicio AS pr WHERE pr.id_servicio='$id_servicio_request'";
							$result = _query($sql);
							$count = _num_rows($result);
							//Obtener los precios que corresponden al servicio
							$cr = 1;
							while ($row = _fetch_array($result))
							{
								echo "<tr><td>Precio ".$cr."</td><td>".$row ['precio_venta_serv']."</td></tr>";
								$cr++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<div class="modal-footer">
<?php

	echo "<button type='button' class='btn btn-primary' data-dismiss='modal'>Salir</button>
	</div><!--/modal-footer -->";
	} //permiso del script
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
?>
