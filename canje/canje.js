var params;
// Cargar los datos iniciales de la forma y la etiquetas
function cargaforma() {
	buscatitulo();
	var logo;
	var limpiardatos;

	params = fparamurl(window.location.search.substr(1));
	if (params.id!=undefined) {
		prov = params.id;
	} else {
		params = JSON.parse(params.cJson);
		prov = params.id_proveedor;
	}

	sessionStorage.setItem("id_proveedor",prov);

	var titulo = sessionStorage.getItem("nombresistema");
	// cargar parámetros de la tabla
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				document.title = titulo;
				logo = respuesta.proveedor.logo;
				document.getElementById("logo").src = "../img/" + logo;
				document.getElementById("logo").title = respuesta.proveedor.nombre;
			}
		}
	};
	xmlhttp.open("GET", "../php/proveedores.php?prov=" + prov, false);
	xmlhttp.send();

	// cargar parámetros del json: etiquetas y elementos de forma
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			modulo = respuesta.titulo;
			document.title = document.title + ' - ' + modulo;
			tituloformulario = modulo.toUpperCase();
			document.getElementById("tituloformulario").innerHTML = tituloformulario;
			for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
				if (campo<document.getElementsByClassName("campo").length-1) {
					document.getElementsByClassName("etiq")[campo].innerHTML = respuesta.etiquetas[campo];
				}
				// document.getElementsByClassName("campo")[campo].id = respuesta.campos[campo];
			}
			document.getElementById("clavecanje").focus();
		}
	};
	xmlhttp.open("GET", "canje.json", false);
	xmlhttp.send();

}

// limpia el formulario
function limpiausuario() {
	document.getElementById("clavecanje").value = '';
	document.getElementById("clavecanje").focus();
}

// limpia el formulario
function limpiacupon() {
	document.getElementById("cuponlargo").value = '';
	document.getElementById("cuponlargo").focus();
}

// Enviar los datos del formulario para procesar en el servidor
function buscausuario() {
	var id_proveedor = sessionStorage.getItem("id_proveedor");
	// var id_proveedor = params.id_proveedor;
	document.getElementById("id_proveedor").value = id_proveedor;

	var datos = new FormData();
	datos.append("clavecanje", document.getElementById("clavecanje").value);
	datos.append("id_proveedor", document.getElementById("id_proveedor").value);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				// if (params.cupon!=undefined) {
				// 	document.getElementById("cuponlargo").value = params.cupon;
				// 	enviacupon();
				// } else {
				// 	canjear();
				// }
				if (params.cupon!=undefined) {
					limpiardatos = false;
					console.log(params);
					console.log(params.cupon);
					document.getElementById("cuponlargo").value = params.cupon;
					document.getElementById("cuponlargo").disabled = true;
				} else {
					limpiardatos = true;
				}
				canjear(limpiardatos);
			} else {
				alert(fmensaje(respuesta.mensaje));
				limpiausuario();
			}
		}
	};
	xmlhttp.open("POST", "../php/clavecanje.php", false);
	xmlhttp.send(datos);
}

function canjear(limpiardatos) {
	document.getElementById("login").style.display = 'none';
	document.getElementById("canjear").style.display = 'block';
	if (limpiardatos) {
		limpiacupon();
	} else {
		document.getElementById("factura").focus();
	}
}

// Enviar los datos del formulario para procesar en el servidor
function enviacupon() {
	var datos = new FormData();
	console.log(document.getElementById("cuponlargo").value);
	datos.append("cuponlargo", document.getElementById("cuponlargo").value);
	datos.append("factura", document.getElementById("factura").value);
	datos.append("monto", document.getElementById("monto").value);
	datos.append("id_proveedor", document.getElementById("id_proveedor").value);
	console.log(datos);


	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				alert(fmensaje(respuesta.mensaje));
				window.location.replace("canje.html?id="+document.getElementById("id_proveedor").value);
			} else {
				alert(fmensaje(respuesta.mensaje));
				limpiausuario();
			}
		}
	};
	xmlhttp.open("POST", "../php/canjeacupon.php", true);
	xmlhttp.send(datos);
}

function buscatitulo() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				document.title = respuesta.parametros.nombresistema;
				sessionStorage.setItem("nombresistema", respuesta.parametros.nombresistema);
			}
		}
	};
	xmlhttp.open("GET", "../php/parametros.php", false);
	xmlhttp.send();
}