<?php
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// ini_set('display_errors', 0);
// error_reporting( E_ERROR | E_PARSE );
include_once "_core.php";
function initial()
{
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

    $id_servicio = $_REQUEST["id_servicio"];
	$sql = _query("SELECT * FROM servicios WHERE id_servicio='$id_servicio'");
	$datos = _fetch_array($sql);

	$id_servicio=$datos["id_servicio"];
	$nombre_servicio=$datos["descripcion"];

?>
	<div class="modal-header">
    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    	<h4 class="modal-title">Editar servicio</h4>
  	</div>
    <div class="modal-body">
      <div class="wrapper wrapper-content  animated fadeInRight">
        <div class="row" id="row1">
          <div class="col-lg-12">
            <?php if($links != 'NOT' || $admin == '1'){ ?>
            <form name="formulario" id="formulario" autocomplete="off">
				<div class="form-group has-info single-line">
					<label>Nombre  del servicio<span style="color:red;">*</span></label>
					<input type="text" placeholder="Nombre de servicio" class="form-control" id="nombre_servicio" name="nombre_servicio" value="<?php echo $nombre_servicio; ?>">
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
						<?php
							$sql = _query("SELECT * FROM precios_servicio WHERE id_servicio='$id_servicio'");
							$cr = 1;
							while($datos = _fetch_array($sql)){
								$id_precio_serv = $datos["id_precio_serv"];
								$precio_venta=$datos["precio_venta_serv"];
								echo "<tr data-mode='' data-id_precio='".$id_precio_serv."'>
									<td>PRECIO ".$cr."</td>
									<td><div><input type='number' class='form-control precio_venta' 
									data-precio_original='".$precio_venta."'
									value='".$precio_venta."' style='width:100%;' min='0'></div></td>
									<td class='text-center'><a href='#'><i class='fa fa-ban'></i></a></td>
									</tr>";
								$cr++;
							}
						?>
					</tbody>
				</table>
              <div>
			  	<input type="hidden" name="id_servicio" id="id_servicio" value="<?php echo $id_servicio; ?>">
				<input type="submit" class="btn btn-primary" id="guardar_btn" value="Guardar">
				<input type="hidden" name="process" id="process" value="edited"><br>
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
                tr_add += "<td><div><input data-precio_original='0' type='number' class='form-control precio_venta' value='' style='width:100%;' min='0'></div></td>";
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

		$(document).on("keyup", ".precio_venta", function() {
			let precio_original = $(this).data("precio_original");
			if(
				precio_original != $(this).val() &&
				$(this).data("precio_original") != 0 &&
				$(this).parents("tr").attr("data-mode") != "insert"
			){
				$(this).parents("tr").attr("data-mode", "update");
				console.log("hizo update bro");
			}else if(
				precio_original == $(this).val() &&
				$(this).data("precio_original") != 0 &&
				$(this).parents("tr").attr("data-mode") != "insert"
			){
				$(this).parents("tr").attr("data-mode", "");
				console.log("no se ha tocado nada ... " + $(this).parents("tr").attr("data-mode"));
			}
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
	
	} //permiso del script
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
	}
}

function actualizar()
{
	$id_servicio= $_POST['id_servicio'];
	$nombre_servicio=$_POST["nombre_servicio"];
	$datos = json_decode($_POST["datos"], true);
	$estado=1;//Estado 1 = activo
	
	_begin();

	/**
	 * Primero almacenamos el nombre del servicio
	 * que se ha editado
	 */
	$table = 'servicios';
	$form_data = array(
		'descripcion' => $nombre_servicio,
		'estado' => $estado
	);
	$where = "id_servicio =".$id_servicio;
	$update_serv = _update($table,$form_data,$where);
	$update_serv_affected = _affected_rows();

	$countr = 0;
	$error_finder = 0;

	/**
	 * Ahora procederemos a actualizar los precios ya 
	 * existentes (en caso de que se hayan cambiado)
	 * y se han agregado nuevos precios, procedemos a guardarlos
	 */
	while($countr < count($datos)){
		$mode = $datos[$countr]['mode'];
		$id_precio_serv = $datos[$countr]['id_precio_serv'];
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
		}elseif($mode == "update"){
			$table = 'precios_servicio';
			$form_data = array(
				'precio_venta_serv' => $precio_venta,
			);
			$where = "id_precio_serv =".$id_precio_serv;
			$update_precio_serv = _update($table,$form_data,$where);
			
			if(_affected_rows() <= 0){
				$error_finder += 1;
			}
		}
		$countr++;
	}
	
	if ($update_serv > 0 && $error_finder == 0) { 

		_commit();
        $xdatos['typeinfo'] = 'Success';
        $xdatos['msg'] = 'Registro modificado con exito!';
        $xdatos['process'] = 'edit';
    } else {

		_rollback();
        $xdatos['typeinfo'] = 'Error';
        $xdatos['msg'] = 'Registro no pudo ser modificado!' . _error();
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
			case 'edited':
            actualizar();
			break;
		}
	}
}



// again where clause is left optional
function _update_decode($table_name, $form_data, $where_clause='')
{
  // check for optional where clause
  $whereSQL = '';
  $form_data2=array();
  $variable='';
  if(!empty($where_clause))
  {
    // check to see if the 'where' keyword exists
    if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
    {
      // not found, add key word
      $whereSQL = " WHERE ".$where_clause;
    } else
    {
      $whereSQL = " ".trim($where_clause);
    }
  }
  // start the actual SQL statement
  $sql = "UPDATE ".$table_name." SET ";

  // loop and build the column /
  $sets = array();
  //begin modified
  foreach($form_data as $index=>$variable){
    $var1=preg_match('/\x{27}/u', $variable);
    $var2=preg_match('/\x{22}/u', $variable);
    if($var1==true || $var2==true){
      $variable = addslashes($variable);
    }
    $form_data2[$index] = $variable;
  }
  foreach ( $form_data2 as $column => $value ) {
    $sets [] = $column . " = '" . $value . "'";
  }
  $sql .= implode(', ', $sets);

  // append the where statement
  $sql .= $whereSQL;
  // run and return the query result
  return $sql;
}
?>
