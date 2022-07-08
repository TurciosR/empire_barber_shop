<?php
include_once "_core.php";
$query = $_REQUEST['query'];
$id_sucursal=$_SESSION['id_sucursal'];
$bandera_serv = "";

$sql0="SELECT producto.id_producto as id, descripcion, barcode
				FROM producto
				JOIN stock on stock.id_producto=producto.id_producto
				WHERE barcode='$query' 
				AND stock.id_sucursal='$id_sucursal' 
				AND stock.stock>0";
$result = _query($sql0);

if(_num_rows($result)==0)
{
	$sql = "SELECT producto.id_producto as id, descripcion, barcode
					FROM producto
					JOIN stock on stock.id_producto=producto.id_producto
					WHERE descripcion LIKE '%$query%' 
					AND stock.id_sucursal='$id_sucursal' 
					AND stock.stock>0 limit 100";
	$result = _query($sql);
}

/**
 * Evaluamos si es un servicio el que se ha buscado
 */
if(_num_rows($result)==0)
{
	$sql2 = "SELECT id_servicio, id_prod_serv, descripcion
					FROM servicios
					WHERE descripcion LIKE '%$query%' 
					AND estado = 1
					limit 100";
	$result = _query($sql2);
	$bandera_serv = "si";
}

if (_num_rows($result)==0) {
	# code...
	echo json_encode ("");
}
else {
	$array_prod[] = array();
	$i=0;

	/**
	 * Si se ha encontrado un servicio, lo mandamos al javascript
	 */
	if($bandera_serv == "si"){
		while ($row1 = _fetch_array($result))
		{
			$array_prod[$i] = array('producto'=>$row1['id_prod_serv']."|".$row1['id_servicio']."|".$row1['descripcion']);
			$i++;
		}
	}else{
		while ($row1 = _fetch_array($result))
		{
			if($row1['barcode']==""){
				$barcod=" ";
			}
			else{
				$barcod=" [".$row1['barcode']."] ";
			}
			$array_prod[$i] = array('producto'=>$row1['id']."|".$barcod.$row1['descripcion']);
			$i++;
		}
	}
	
	echo json_encode ($array_prod);
}

?>
