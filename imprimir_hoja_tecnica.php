<?php
error_reporting(E_ERROR | E_PARSE);
require("_core.php");
require("num2letras.php");
require('fpdf/fpdf.php');

$fini = $_REQUEST["fini"];
$fin = $_REQUEST["ffin"];
$id_cliente = $_REQUEST['id_cliente'];

$sql_empresa = "SELECT * FROM sucursal WHERE id_sucursal=$_SESSION[id_sucursal]";
$resultado_emp=_query($sql_empresa);
$row_emp=_fetch_array($resultado_emp);
$nombre_a = utf8_decode(Mayu(utf8_decode(trim($row_emp["descripcion"]))));
$tel1 = $row_emp['telefono1'];
$n_sucursal = $row_emp['n_sucursal'];
$tel2 = $row_emp['telefono2'];
$direccion = $row_emp['direccion'];
if(strlen($tel1)>0){
  $telefonos="TEL. ".$tel1;
  if(strlen($tel2)>0){
    $telefonos="TEL. ".$tel1." y ".$tel2;
  }
}

class PDF extends FPDF
{
    var $a;
    var $b;
    var $c;
    var $d;
    var $e;
    var $f;
    var $w;
    
    //FUNCION IMPORTADA PARA REALIZAR LISTADO DE ITEMS
    function MultiCellBltArray($w, $h, $blt_array, $border=0, $align='J', $fill=false)
    {
        if (!is_array($blt_array))
        {
            die('MultiCellBltArray requires an array with the following keys: bullet,margin,text,indent,spacer');
            exit;
        }
                
        //Save x
        $bak_x = $this->x;
        
        for ($i=0; $i<sizeof($blt_array['text']); $i++)
        {
            //Get bullet width including margin
            $blt_width = $this->GetStringWidth($blt_array['bullet'] . $blt_array['margin'])+$this->cMargin*2;
            
            // SetX
            $this->SetX($bak_x);
            
            //Output indent
            if ($blt_array['indent'] > 0)
                $this->Cell($blt_array['indent']);
            
            //Output bullet
            $this->Cell($blt_width,$h,$blt_array['bullet'] . $blt_array['margin'],0,'',$fill);
            
            //Output text
            $this->MultiCell($w-$blt_width,$h,$blt_array['text'][$i],$border,$align,$fill);
            
            //Insert a spacer between items if not the last item
            if ($i != sizeof($blt_array['text'])-1)
                $this->Ln($blt_array['spacer']);
            
            //Increment bullet if it's a number
            if (is_numeric($blt_array['bullet']))
                $blt_array['bullet']++;
        }
    
        //Restore x
        $this->x = $bak_x;
    }
    // Cabecera de página\
    public function Header()
    {
        $sql_empresa=_query("SELECT * FROM sucursal where id_sucursal=$_SESSION[id_sucursal]");
        $array_empresa=_fetch_array($sql_empresa);
        $logo_empresa=$array_empresa['logo'];
    
        // Logo
        $this->Image($logo_empresa, 10, 10, 35);
        $this->AddFont('latin','','latin.php');
        $this->SetFont('latin', '', 12);

        $this->Cell(195,6,$this->a,0,1,'C');
        $this->SetFont('Latin','',12);
        $this->SetX(40);
        $this->MultiCell(140,5,utf8_decode((Mayu(utf8_decode("Sucursal ".": ".$this->c)))),0,'C',0);
        $this->Cell(195,6,$this->b,0,1,'C');
        $this->Cell(195,6,utf8_decode("REPORTE TÉCNICO DE TRABAJO REALIZADO"),0,1,'C');
        $this->Cell(195,6,$this->d,0,1,'C');
        // Salto de línea
        $this->Ln(5);
    }
    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página requiere $pdf->AliasNbPages();
        //utf8_decode() de php que convierte nuestros caracteres a ISO-8859-1
        $this-> Cell(40, 10, utf8_decode('Fecha de impresión: '.ED(date('Y-m-d'))), 0, 0, 'L');
        $this->Cell(156, 10, utf8_decode('Página ').$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    public function setear($a,$b,$c,$d,$e,$f,$g,$w)
    {
      # code...
      $this->a=$a;
      $this->b=$b;
      $this->c=$c;
      $this->d=$d;
      $this->e=$e;
      $this->f=$f;
      $this->g=$g;
      $this->w=$w;
    }
}

$pdf = new PDF('P', 'mm', 'letter');
$fech =  "DEL ".ED($fini)." AL ".ED($fin);
$pdf->setear($nombre_a,$telefonos,$direccion,"",$n_sucursal,"","","");
$pdf->SetMargins(10, 10);
$pdf->SetLeftMargin(10);
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(195,6,utf8_decode("DATOS DEL CLIENTE"),1,1,'C',true);

//OBTENCION DE LOS DATOS DE LOS CLIENTES
$id_cliente = $_REQUEST['id_cliente'];
$sql_cliente=_query("SELECT * FROM cliente where id_cliente=".$id_cliente);
$array_cliente=_fetch_array($sql_cliente);
$nombre_cliente = $array_cliente['nombre'];
$dui_cliente = $array_cliente['dui'];
if(isset($array_cliente['direccion'])){
    $direccion_cliente = $array_cliente['direccion'];
}else{
    $direccion_cliente = "";
}
if(isset($array_cliente['telefono1'])){
    $numero_cliente = $array_cliente['telefono1'];
}elseif (isset($array_cliente['telefono2'])) {
    $numero_cliente = $array_cliente['telefono2'];
}else{
    $numero_cliente = "";
}

//OBTENCION DE LOS DATOS REFERENTES AL TRABAJO REALIZADO
$sql_reparacion=_query("SELECT 
    empleado.nombre,
    empleado.telefono1,
    reparaciones.numero_placa,
    reparaciones.diagnostico_inicial, 
    reparaciones.fecha_ingreso,
    factura.fecha, #fecha de salida
    factura.total,
    factura.finalizada
    FROM factura
    INNER JOIN reparaciones ON factura.id_factura = reparaciones.id_factura
    INNER JOIN empleado ON reparaciones.id_empleado = empleado.id_empleado
    WHERE factura.finalizada = 1 AND factura.id_cliente =".$id_cliente." AND factura.id_factura = ".$_REQUEST['id_factura']);
$array_reparacion=_fetch_array($sql_reparacion);
$numero_placa = $array_reparacion['numero_placa'];
$nombre_empleado = $array_reparacion['nombre'];
$numero_empleado = $array_reparacion['telefono1'];
$fecha_ingreso = $array_reparacion['fecha_ingreso'];
$fecha_salida = $array_reparacion['fecha'];
$diagnostico_inicial = $array_reparacion['diagnostico_inicial'];


//DATOS DEL CLIENTE
$pdf->SetFont('latin', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(127.5,8,utf8_decode(" Nombre del Cliente:        ".$nombre_cliente),1,0,'L');
$pdf->Cell(67.5,8,utf8_decode(" DUI cliente:        ".$dui_cliente),1,1,'L');
$pdf->Cell(195,8,utf8_decode(" Dirección:       ".$direccion_cliente),1,1,'L');
$pdf->Cell(97.5,8,utf8_decode(" No. de Telefono:      ".$numero_cliente),1,0,'L');
$pdf->Cell(97.5,8,utf8_decode(" No. de Placa del Vehiculo:      ".$numero_placa),1,1,'L');
$pdf->Ln(4);

//DETALLES DEL TRABAJO REALIZADO
$pdf->SetFont('Times', 'B', 12);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(195,6,utf8_decode("DETALLES DEL TRABAJO REALIZADO"),1,1,'C',true);
$pdf->SetFont('latin', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(137.5,8,utf8_decode(" Empleado a cargo de este trabajo:     ".$nombre_empleado),1,0,'L');
$pdf->Cell(57.5,8,utf8_decode(" No. de Telefono:    ".$numero_empleado),1,1,'L');
$pdf->Cell(97.5,8,utf8_decode(" Fecha de ingreso:      ".ED($fecha_ingreso)),1,0,'L');
$pdf->Cell(97.5,8,utf8_decode(" Fecha de salida:      ".ED($fecha_salida)),1,1,'L');
$pdf->MultiCell(195,10,utf8_decode(" Diagnostico inicial:   ".$diagnostico_inicial),1,'L');
$pos_inicial_cuadro = $pdf->GetY();
//LISTAMOS EL DETALLE DE SERVICIOS Y PRODUCTOS FACTURADOS
$pdf->Cell(195,8,utf8_decode(" Productos y servicios facturados:     "),0,0,'L');
$pdf->Ln(10);
//INICIO DE LISTADO DE PRODUCTOS
$query_facturado = _query("SELECT factura.id_factura,producto.descripcion,
    presentacion_producto.descripcion AS preprodesc, presentacion_producto.unidad,
    presentacion.descripcion AS predesc,
    factura.id_cliente, factura.fecha,
    factura.numero_doc, factura.total,
    factura.id_usuario, factura.anulada,
    factura.id_usuario, factura.finalizada,
    factura.id_sucursal,
    factura_detalle.id_factura_detalle,
    factura_detalle.id_prod_serv,factura_detalle.cantidad,
    factura_detalle.precio_venta, factura_detalle.subtotal,
    factura_detalle.tipo_prod_serv, factura.tipo_documento,
    factura.sumas,factura.iva
    FROM factura
    JOIN factura_detalle  ON factura.id_factura=factura_detalle.id_factura
    JOIN producto ON factura_detalle.id_prod_serv=producto.id_producto
    JOIN presentacion_producto ON factura_detalle.id_presentacion=presentacion_producto.id_pp
    JOIN presentacion ON presentacion_producto.id_presentacion=presentacion.id_presentacion
    WHERE
    factura.id_factura=".$_REQUEST['id_factura']."
    AND factura.id_sucursal=".$_SESSION['id_sucursal']);

$dataFacturado = array();
$dataFacturado['bullet'] = '>';
$dataFacturado['margin'] = ' ';
$dataFacturado['indent'] = 5;
$dataFacturado['spacer'] = 5;
$dataFacturado['text'] = array();
$i=0;
if(_num_rows($query_facturado)> 0){
  while ($row = _fetch_array($query_facturado))
  {
    $dataFacturado['text'][$i] = utf8_decode($row["descripcion"]." ----- Precio: $".$row["subtotal"]);
    $i++;
  }
}
$pdf->SetX(40);
$pdf->MultiCellBltArray(200,6,$dataFacturado);
//FIN DE LISTADO DE PRODUCTOS

//INICIO DE LISTADO DE SERVICIOS
$sql_servicios = _query("SELECT * FROM  factura_detalle 
    where id_factura=".$_REQUEST['id_factura']." AND id_prod_serv=-9999 AND id_presentacion=-9999");

$dataFacturadoServicio = array();
$dataFacturadoServicio['bullet'] = '>';
$dataFacturadoServicio['margin'] = ' ';
$dataFacturadoServicio['indent'] = 5;
$dataFacturadoServicio['spacer'] = 5;
$dataFacturadoServicio['text'] = array();
$i=0;
if(_num_rows($sql_servicios)> 0){
  while ($row = _fetch_array($sql_servicios))
  {
    $dataFacturadoServicio['text'][$i] = utf8_decode($row["description"]." ----- Precio: $".$row["subtotal"]);
    $i++;
  }
}
$pdf->SetX(40);
$pdf->MultiCellBltArray(200,6,$dataFacturadoServicio);
//FIN DE LISTADO DE SERVICIOS


$pdf->Rect(10,$pos_inicial_cuadro, 195, 150 ,"");

ob_clean();
$pdf->Output("reporte_inscripciones_vigente.pdf","I");
