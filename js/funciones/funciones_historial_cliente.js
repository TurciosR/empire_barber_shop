var dataTable ="";

$(document).ready(function()
{
	$(".date").datepicker();

	// Clean the modal form
	generar();
});
$(".datepick").datepicker({
		format: 'dd-mm-yyyy',
		language:'es',
});
$('.decimal').numeric({
    negative: false,
    decimalPlaces: 2
});
function generar(){
	fechai=$("#fecha_inicio").val();
	fechaf=$("#fecha_fin").val();
	id_cliente=$("#id_cliente").val();
	dataTable = $('#editable2').DataTable().destroy()
	dataTable = $('#editable2').DataTable( {
			"pageLength": 50,
			"processing": true,
			"serverSide": true,
			"ajax":{
					url :"historial_cliente_dt.php?fechai="+fechai+"&fechaf="+fechaf+"&id_cliente="+id_cliente, // json datasource
					//url :"admin_factura_rangos_dt.php", // json datasource
					//type: "post",  // method  , by default get
					error: function(){  // error handling
						$(".editable2-error").html("");
						$("#editable2").append('<tbody class="editable2_grid-error"><tr><th colspan="3">No se encontró información segun busqueda </th></tr></tbody>');
						$("#editable2_processing").css("display","none");
						$( ".editable2-error" ).remove();
						}
					}
				} );

		dataTable.ajax.reload()
	//}
}

$(document).on("click", "#btnMostrar", function(event) {
	generar();
});