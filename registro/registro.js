// Cargar los datos iniciales de la forma y la etiquetas
function cargaforma() {
	buscatitulo();
	var logo;

	var params = fparamurl(window.location.search.substr(1));

	sessionStorage.setItem("id_proveedor",params.idp);
	sessionStorage.setItem("id_socio",params.ids);
	var prov = sessionStorage.getItem("id_proveedor");
	var socio = sessionStorage.getItem("id_socio");

	var titulo = sessionStorage.getItem("nombresistema");
	// cargar parámetros de la tabla
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 3) {
				document.title = titulo;
				logo = respuesta.proveedor.logo;
				if (logo!="") {
					document.getElementById("logo").src = "../img/" + logo;
				} else {
					document.getElementById("logo").src = "../img/" + 'sin_imagen.jpg';
				}
				document.getElementById("logo").title = respuesta.proveedor.nombre;
				document.getElementById("tituloformulario").innerHTML = '¡'+respuesta.socio.nombres+', bienvenido a tu club!';
			}
		}
	};
	xmlhttp.open("GET", "../php/buscadatos.php?prov=" + prov+"&socio="+socio, false);
	xmlhttp.send();

	// cargar parámetros del json: etiquetas y elementos de forma
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			modulo = respuesta.titulo;
			document.title = document.title + ' - ' + modulo;
			console.log(respuesta);
			for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
				if (campo<document.getElementsByClassName("campo").length-2) {
					document.getElementsByClassName("etiq")[campo].innerHTML = respuesta.etiquetas[campo];
				}
				document.getElementsByClassName("campo")[campo].id = respuesta.campos[campo].nombre;
			}
		}
	};
	xmlhttp.open("GET", "registro.json", false);
	xmlhttp.send();
}

// limpia el formulario
function limpiar() {
	for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
		document.getElementsByClassName("campo")[campo].value = "";
	}
	document.getElementById("fechanacimiento").focus();
}

// Enviar los datos del formulario para procesar en el servidor
function enviar() {
	var id_proveedor = sessionStorage.getItem("id_proveedor");
	document.getElementById("id_proveedor").value = id_proveedor;
	var id_socio = sessionStorage.getItem("id_socio");
	document.getElementById("id_socio").value = id_socio;

	var datos = new FormData();
	for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
		if (document.getElementsByClassName("campo")[campo].type=='checkbox'){
			datos.append(document.getElementsByClassName("campo")[campo].id, document.getElementsByClassName("campo")[campo].checked);
		} else {
			datos.append(document.getElementsByClassName("campo")[campo].id, document.getElementsByClassName("campo")[campo].value);
		}
	}

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				alert(fmensaje(respuesta.mensaje));
				limpiar();
				// window.location.replace("www.clubdeconsumidores.com.ve/index.html");
				window.location.replace("../");
			} else {
				alert(fmensaje(respuesta.mensaje));
			}
		}
	};
	xmlhttp.open("POST", "../php/registro.php", false);
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