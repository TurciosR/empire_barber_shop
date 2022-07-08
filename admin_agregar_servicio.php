<?php
include_once "_core.php";
function initial()
{
  $id_user=$_SESSION["id_usuario"];
  $admin=$_SESSION["admin"];

  $uri = $_SERVER['SCRIPT_NAME'];
  $filename=get_name_script($uri);
  $links=permission_usr($id_user,$filename);
  //permiso del script
  ?>
  	<div class="modal-header">
    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    	<h4 class="modal-title">Agregar Servicios</h4>
  	</div>
    <div class="modal-body">
      <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row" id="row1">
          <div class="col-lg-12">
            <?php if($links != 'NOT' || $admin == '1'){ ?>
            <form name="formulario" id="formulario" autocomplete="off">
				<div class="form-group has-info single-line">
					<label>Nombre  del Servicio<span style="color:red;">*</span></label>
					<input type="text" placeholder="Nombre de servicio" class="form-control" id="nombre_servicio" name="nombre_servicio" value="">
				</div>
				<div class="form-group has-info">
					<input type="button" class="btn btn-primary" id="add_precio" value="Añadir precio">
				</div>
				<table class="table table-striped table-bordered table-condensed" id="inventable">
					<thead>
						<tr>
							<th class="col-lg-1 text-center"># de Precio</th>
							<th class="col-lg-1 text-center">Precio de Venta</th>
							<th class="col-lg-1 text-center">Quitar</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
              <div>
                <input type="submit" class="btn btn-primary" id="guardar_btn" value="Guardar">
                <input type="hidden" name="process" id="process" value="insert">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
	  	$(document).ready(function(){
			$("#add_precio").click(function(){
				let tr_add = "";

				counter_rows = $("#inventable tr").length;
    
                tr_add += '<tr data-mode="insert">';
                tr_add += '<td>' + "PRECIO " + counter_rows + '</td>';
                tr_add += "<td><div><input type='number' class='form-control precio_venta' value='' style='width:100%;' min='0'></div></td>";
                tr_add += "<td class='Delete text-center'><a href='#'><i class='fa fa-trash'></i></a></td>";
                tr_add += '</tr>';

				$("#inventable").prepend(tr_add);
				$(".precio_venta").numeric({
					negative: false
				});
			});
		});

		$(document).on("click", ".Delete", function() {
			$(this).parents("tr").remove();
		});
		

		$(document).ready(function()
		{
			$('#formulario').validate({
				rules: {
					nombre_servicio: {
						required: true,
					}
				},
				messages: {
					nombre_servicio: "Por favor ingrese el nombre del servicio"
				},
				highlight: function(element) {
					$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
				},
				success: function(element) {
					$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
				},
				submitHandler: function(form) {
					senddata();
				}
			});
			$('#nombre_servicio').on('keyup', function(evt){
                $(this).val($(this).val().toUpperCase());
            });
		});
    </script>
    <?php

  }
  else
  {
    echo "<br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div></div></div></div></div>";
  }
}

function insertar()
{
    $current_date = date('Y-m-d h:i:s');
    $newDate = date("Y-m-d h:i:s", strtotime($current_date));

	$buscar_last_id_servicio=_query("SELECT MAX(id_servicio) AS last_id_servicio FROM servicios");
	$dato_last_id_servicio=_fetch_array($buscar_last_id_servicio);
	$last_id_servicio=$dato_last_id_servicio['last_id_servicio'];
	$new_id_servicio=$last_id_servicio+1;

	$id_servicio= $new_id_servicio;
	$nombre_servicio = $_POST["nombre_servicio"];
	$datos = json_decode($_POST["datos"], true);
	$estado=1;//Estado 1 = activo

	_begin();
	/**
	 * Insertamos el servicio, en este caso el nombre del servicio
	 */
	$table = 'servicios';
	$form_data = array(
		'id_servicio' => $id_servicio,
		'descripcion' => $nombre_servicio,
		'estado' => $estado
	);
	$insertar = _insert($table,$form_data);

	/**
	 * Insertamos los precios asociados al servicio
	 */
	$countr = 0;
	$error_finder = 0;

	while($countr < count($datos)){
		$mode = $datos[$countr]['mode'];
		$precio_venta = $datos[$countr]['precio_venta'];

		if($mode == "insert"){
			$table_su = "precios_servicio";
			$form_data_su = array(
				'id_servicio' => $id_servicio,
				'precio_venta_serv' => $precio_venta
			);
			$insert_precios_serv = _insert($table_su, $form_data_su);
			if(_insert_id() <= 0){
				$error_finder += 1;
			}
		}
		$countr++;
	}
	
	if ($insertar && $error_finder == 0) {

		_commit();
        $xdatos['typeinfo'] = 'Success';
        $xdatos['msg'] = 'Registro guardado con exito!';
        $xdatos['process'] = 'insert';
    } else {

		_rollback();
        $xdatos['typeinfo'] = 'Error';
        $xdatos['msg'] = 'Registro no pudo ser guardado!' . _error();
    }
	
	echo json_encode($xdatos);
}
if(!isset($_POST['process']))
{
	initial();
}
else
{
	if(isset($_POST['process']))
	{
		switch ($_POST['process'])
		{
			case 'insert':
            insertar();
			break;
		}
	}
}
?>
