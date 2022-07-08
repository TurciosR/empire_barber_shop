<?php
include ("_core.php");
function initial()
{
	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$admin=$_SESSION["admin"];
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
            aria-hidden="true">&times;</button>
        <h4 class="modal-title">Agregar detalles de Trabajo </h4>
    </div>
    <style type="text/css">
        .datepicker table tr td, .datepicker table tr th{
            border:none;
            background:white;
            z-index: 3;
        }
        .select2-dropdown {
            z-index:99999;
        }
    </style>
    <form action="" method="post" id="formulario">
    <div class="modal-body">
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row" id="row1">
                <?php
                    if ($links!='NOT' || $admin=='1' ){
                ?>
                <div class="row">
                    <!-- Ingresar placa de la motocicleta -->
                    <div class="col-lg-6 form-group">
                        <label>Ingresar No. de Placa</label>
                        <input type="text" name="placa_moto"
                            id="placa_moto" class="form-control placa_moto" value="" placeholder="Ingrese el número de placa">
                    </div>
                    <!-- Empleado a cargo del trabajo -->
                    <div class="col-lg-6 form-group">
                        <label>Empleado a cargo</label>
                        <br>
                        <select name="id_empleado"
                            id="id_empleado" style="width:100%"
                            class="form-control select id_empleado">
                            <option value="">Seleccione</option>
                            <?php
                                $sql_empleado = "SELECT * FROM empleado";
                                $query_empleado = _query($sql_empleado);

                                while($row_empleado = _fetch_array($query_empleado)){
                                    $nombre_empleado = $row_empleado['nombre']." ".$row_empleado['apellido'];
                                    echo "<option value='".$row_empleado['id_empleado']."'";
                                    
                                    // if($row["id_doctor"] == $row_doctor['id_doctor']){
                                    //     echo " selected ";
                                    // }
                                    echo ">";
                                    echo $nombre_empleado;
                                    echo "</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <!-- Diagnostico inicial del trabajo a realizar -->
                    <div class="col-lg-12 form-group">
                        <label>Diagnostico inicial</label>
                        <textarea class="form-control diagnostico_inicial" id="diagnostico_inicial" 
                        name="diagnostico_inicial" rows="2" value="" placeholder="Ingrese el diagnostico"></textarea>
                    </div>
                </div>
                <div class="row">
                    <!-- Fecha de ingreso de la motocicleta -->
                    <div class='col-md-6 form-group'>
                        <label>Fecha de ingreso:</label>
                        <input type='text'
                            class='form-control datepicker fecha_ingreso'
                            id='fecha_ingreso'
                            name='fecha_ingreso'
                            value='<?php echo date("d-m-Y");?>' disabled>
                    </div>
                </div>
                <?php
                    //echo "<input type='hidden' name='id_constancia' id='id_constancia' value='$id_constancia'>";
                ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btnSave" id="btnSave">Guardar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
    </div>
    </form>
    <script type="text/javascript">
       
       $(document).ready(function(){
            $('#fecha_ingreso').datepicker();
            $(".select").select2();
            
            if(localStorage.getItem("detalle_trabajo")){

                let detalles_trabajo = JSON.parse(localStorage.getItem("detalle_trabajo"));
            
                $("#placa_moto").val(detalles_trabajo.placa_moto)
                $("#diagnostico_inicial").val(detalles_trabajo.diagnostico_inicial)
                $("#fecha_ingreso").val(detalles_trabajo.fecha_ingreso)
                $(".id_empleado").val( detalles_trabajo.id_empleado);
                $(".id_empleado").change();
            }

            $('#formulario').validate({
                rules: {
                    placa_moto: {
                        required: true,
                    },
                    id_empleado: {
                        required: true,
                    },
                    diagnostico_inicial: {
                        required: true,
                    },
                },
                messages: {
                    placa_moto: "Por favor ingrese el No. de placa",
                    id_empleado: "Por favor seleccione un Empleado",
                    diagnostico_inicial: "Por favor escriba un diagnostico",
                },
                highlight: function(element) {
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                success: function(element) {
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                },
                submitHandler: function(form) {
                    let placa_moto = $('.placa_moto').val();
                    let id_empleado = $('.id_empleado').val();
                    let diagnostico_inicial = $('.diagnostico_inicial').val();
                    let fecha_ingreso = $('.fecha_ingreso').val();

                    $.ajax({
                        type: "POST",
                        url: "agregar_detalles_trabajo.php",
                        dataType: 'JSON',
                        data: {
                            placa_moto, 
                            id_empleado,
                            diagnostico_inicial,
                            fecha_ingreso,
                            process: 'insertLocal'
                        },
                        success: function (response) {
                            
                            let datos_detalle = localStorage.setItem("detalle_trabajo", JSON.stringify(response.detalle_trabajo));
                            console.log('Se agregaron datos nuevos en el localstorage');
                            swal({
                                type: 'success',
                                title:'Detalles asignados correctamente',
                                showConfirmButton: false,
                                timer: 1400
                            })
                            $('#viewModal3').modal('hide');
                        }
                    });
                }
            });            
        });

    </script>
    <!--/modal-footer -->
    <?php
    } //permiso del script
    else {
        echo "<div></div><br><br><div class='alert alert-warning'>No tiene permiso para este modulo.</div>";
    }	
}
function guardarLocal() 
{
    if(isset($_POST["placa_moto"]) && isset($_POST["id_empleado"]) 
    && isset($_POST["diagnostico_inicial"]) && isset($_POST["fecha_ingreso"])){
        $placa_moto = $_POST["placa_moto"];
        $id_empleado = $_POST["id_empleado"];
        $diagnostico_inicial = $_POST["diagnostico_inicial"];
        $fecha_ingreso = MD($_POST["fecha_ingreso"]);
        
        $json = array(
            'placa_moto' => $placa_moto,
            'id_empleado' => $id_empleado,
            'diagnostico_inicial' => $diagnostico_inicial,
            'fecha_ingreso' => $fecha_ingreso
        );
        $xdatos['detalle_trabajo'] = $json;
    }
    else{
        $xdatos ['typeinfo'] = 'Error';
	    $xdatos ['msg'] = 'Datos no pudieron ser cargados';
    }

	echo json_encode ($xdatos);
}

function guardar()
{
    //primero verificamos si ya existe una reparacion existente
    $id_sucursal=$_SESSION["id_sucursal"];
    $last_id_factura = _query("SELECT MAX(id_factura) as id_factura FROM factura WHERE id_sucursal='$id_sucursal'");
    $rows=_fetch_array($last_id_factura);
    $id_factura = $rows['id_factura'];

    $coincidencias = _query("SELECT id_reparacion FROM reparaciones WHERE id_factura='$id_factura'");
    $count = _num_rows($coincidencias);
    if($count > 0){
        $xdatos ['typeinfo'] = 'Error';
	    $xdatos ['msg'] = 'Ya existe una reparacion asignada a esta preventa';
    }else{
        if(isset($_POST["placa_moto"]) && isset($_POST["id_empleado"]) 
        && isset($_POST["diagnostico_inicial"]) && isset($_POST["fecha_ingreso"])){
            $placa_moto = $_POST["placa_moto"];
            $id_empleado = $_POST["id_empleado"];
            $diagnostico_inicial = $_POST["diagnostico_inicial"];
            $fecha_ingreso = $_POST["fecha_ingreso"];

            $id_sucursal=$_SESSION["id_sucursal"];
            $last_id = _query("SELECT MAX(id_factura) as id_factura FROM factura WHERE id_sucursal='$id_sucursal' ");
            $rows=_fetch_array($last_id);
            $id_factura = $rows['id_factura'];
            $table = 'reparaciones';
            $form_data = array(
                'id_factura' => $id_factura,
                'numero_placa' => $placa_moto,
                'id_empleado' => $id_empleado,
                'diagnostico_inicial' => $diagnostico_inicial,
                'fecha_ingreso'=> $fecha_ingreso
            );
            $insertar = _insert($table, $form_data);
            if ($insertar) {
                $xdatos['typeinfo'] = 'Success';
                $xdatos['msg'] = 'Registro guardado con exito!';
                $xdatos['process'] = 'insert';
            } else {
                $xdatos['typeinfo'] = 'Error';
                $xdatos['msg'] = 'Registro no pudo ser guardado !'._error();
            }
        }
        else{
            $xdatos ['typeinfo'] = 'Error';
            $xdatos ['msg'] = 'Datos no pudieron ser cargados';
        }
    }
    
	echo json_encode ($xdatos);
}

function eliminar()
{
    $id_factura = $_POST ['id_factura'];
	$table = 'reparaciones';
	$where_clause = "id_factura='".$id_factura."'";
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

function cargarDatos()
{
    $id_factura = $_POST ['id_factura'];

    $result_detalle= _query("SELECT * FROM reparaciones WHERE id_factura = '$id_factura'");
    $count_fact=_num_rows($result_detalle);

    if ($count_fact > 0) {
        $row = _fetch_array($result_detalle);

        $xdatos ['typeinfo'] = "Success";
        $xdatos ['msg'] = "Detalle encontrado!";
        $xdatos ['placa_moto'] = $row["numero_placa"];
        $xdatos ['id_empleado'] = $row["id_empleado"];
        $xdatos ['diagnostico_inicial'] = $row["diagnostico_inicial"];
        $xdatos ['fecha_ingreso'] = $row["fecha_ingreso"];
    }else{
        $xdatos ['typeinfo'] = "Error";
        $xdatos ['msg'] = "Detalle no pudo ser encontrado!";
        $xdatos ['placa_moto'] = " ";
        $xdatos ['id_empleado'] = " ";
        $xdatos ['diagnostico_inicial'] = " ";
        $xdatos ['fecha_ingreso'] = " ";
    }
    echo json_encode($xdatos); 
}

if (! isset ( $_REQUEST ['process'] ))
{
	initial();
} else {
	if (isset ( $_REQUEST ['process'] ))
	{
		switch ($_REQUEST ['process'])
		{
			case 'insertLocal' :
				guardarLocal();
				break;
            case 'insert' :
                guardar();
                break;
            case 'delete' :
                eliminar();
                break;
            case 'loadData' :
                cargarDatos();
                break;
		}
	}
}

?>
