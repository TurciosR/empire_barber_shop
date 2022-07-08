  $(function() {
	//binding event click for button in modal form
	$(document).on("click", "#btnDelete", function(event) {
	  swal({
		title: "Está seguro de borrar este servicio?",
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Si, borrar este servicio!",
		cancelButtonText: "No, cancelar!",
		closeOnConfirm: true,
		closeOnCancel: false
	  },
	  function(isConfirm) {
		if (isConfirm) {
		  deleted();
		} else {
		  swal("Cancelado", "Operación cancelada", "error");
		}
	  });
	});
	// Clean the modal form
	$(document).on('hidden.bs.modal', function(e) {
	  var target = $(e.target);
	  target.removeData('bs.modal').find(".modal-content").html('');
	});
  
  });

  $(document).on("click","#estado", function()
  {
	var id_servicio = $(this).parents("tr").find("#id_servicio_active").val();
	var estado = $(this).parents("tr").find("#estado").val();
	if(estado == 1)
	{
	  var text = "Desactivar";
	}
	else
	{
		var text = "Activar";
	}
	swal({
	  title: text+" este servicio?",
	  text: "",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonClass: "btn-danger",
	  confirmButtonText: "Si, "+text+" este servicio!",
	  cancelButtonText: "No, cancelar!",
	  closeOnConfirm: true,
	  closeOnCancel: false
	},
	function(isConfirm) {
	  if (isConfirm) {
		estado_serv(id_servicio, estado);
	  } else {
		swal("Cancelado", "Operación cancelada", "error");
	  }
	});
  })
  function estado_serv(id_servicio, estado) {
	//var id_proveedor = $('#id_proveedor').val();
	var dataString = 'process=estado' + '&id_servicio=' + id_servicio+ '&estado=' + estado;
	$.ajax({
	  type: "POST",
	  url: "admin_servicios.php",
	  data: dataString,
	  dataType: 'json',
	  success: function(datax) {
		display_notify(datax.typeinfo, datax.msg);
		// console.log("OKde");
		if (datax.typeinfo == "Success")
		{
		  // console.log("OKK");
		  setInterval("reload1();", 1000);
		  //$('#deleteModal').hide();
		}
	  }
	});
  }
  function senddata() {
	let urlprocess;
	let cuantos = 0;

	if($("#inventable>tbody tr").length == 0){
		display_notify("Error", "Ingrese por lo menos un precio");
	}else{

		let nombre_servicio = $("#nombre_servicio").val();
		let id_servicio = $('#id_servicio').val();
		let process = $('#process').val();
		let datos_array = [];
		let text = ""
		let obj = new Object();
		
		let error_precio = false;

		console.log($("#inventable>tbody [data-mode]").length);
		
		$("#inventable>tbody tr").each(function() {
		
			let id_precio_serv = $(this).data("id_precio");
			let mode = $(this).data("mode");
			let precio_venta = $(this).find(".precio_venta").val();

			if (parseFloat(precio_venta) > 0) {
				obj.id_precio_serv = id_precio_serv;
				obj.mode           = mode;
				obj.precio_venta   = precio_venta;
				
				text = JSON.stringify(obj);
				datos_array.push(text);

				cuantos++;
			}else{
				error_precio = true;
			}
		});
	
		datos_array = JSON.parse("["+datos_array+"]");
		datos_array = JSON.stringify(datos_array);

		
		if (process == 'insert') {
			urlprocess = 'admin_agregar_servicio.php';
		}
		if (process == 'edited') {
			urlprocess = 'editar_servicio.php';
		}
		if(error_precio == true){
			display_notify("Error", "Verifique el/los campo(s) del precio de venta");
		}else{
			console.log(datos_array);
			console.log(urlprocess);
			$.ajax({
				type: 'POST',
				url: urlprocess,
				data: {
					id_servicio: id_servicio,
					nombre_servicio: nombre_servicio,
					datos: datos_array,
					process : process,
					cuantos: cuantos
				},
				dataType: 'json',
				success: function(datax) {
					display_notify(datax.typeinfo, datax.msg);
					if (datax.typeinfo == "Success") {
						setInterval("reload1();", 1000);
					}
				}
			});
		}
	}
  }
  function deleted() {
	var id_servicio = $('#id_servicio').val();
	var dataString = 'process=deleted' + '&id_servicio=' + id_servicio;
	$.ajax({
	  type: "POST",
	  url: "borrar_servicio.php",
	  data: dataString,
	  dataType: 'json',
	  success: function(datax) {
		display_notify(datax.typeinfo, datax.msg);
		if (datax.typeinfo == "Success") {
		  setInterval("reload1();", 1000);
		  $('#deleteModal').hide();
		}
	  }
	});
  }

  function reload1() {
	location.href = 'admin_servicios.php';
  }
  
  