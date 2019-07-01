// Cargar los datos iniciales de la forma y la etiquetas
function cargaforma() {
	buscatitulo();
	var logo;

	// var params = fparamurl(window.location.search.substr(1));

	var idp = 1;
	sessionStorage.setItem("id_proveedor",idp);
	// sessionStorage.setItem("id_proveedor",params.idp);
	// sessionStorage.setItem("id_socio",params.ids);
	var prov = sessionStorage.getItem("id_proveedor");
	// var socio = sessionStorage.getItem("id_socio");

	var titulo = sessionStorage.getItem("nombresistema");
	// cargar parámetros de la tabla
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			document.title = titulo;
			logo = respuesta.logo;
			if (logo!="") {
				document.getElementById("logo").src = "../../img/" + logo;
			} else {
				document.getElementById("logo").src = "../../img/" + 'sin_imagen.jpg';
			}
			document.getElementById("logo").title = respuesta.nombre;
		}
	};
	xmlhttp.open("GET", "../../php/buscaprov.php?prov=" + prov, false);
	xmlhttp.send();

	// cargar parámetros del json: etiquetas y elementos de forma
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		console.log(this.readyState);
		console.log(this.status);
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			preguntas = respuesta.preguntas;
			preg = 1;
			for (campo = 1; campo < document.getElementsByClassName("campo").length; campo++) {
				if (campo<document.getElementsByClassName("campo").length-3) {
					document.getElementsByClassName("etiq")[preg].innerHTML = preguntas[preg-1].pregunta;
					switch (preguntas[preg-1].tiporespuesta) {
						case 'si o no':
							opc = 1;
							document.getElementsByClassName("campo")[campo].name = preg;
							document.getElementsByClassName("campo")[campo].id = preg+'-'+opc;
							opc++;
							document.getElementsByClassName("campo")[campo+1].name = preg;
							document.getElementsByClassName("campo")[campo+1].id = preg+'-'+opc;
							campo++;
							preg++;
							break;
						case 'rango':
							opc = 1;
							document.getElementsByClassName("campo")[campo].name = preg;
							document.getElementsByClassName("campo")[campo].id = preg+'-'+opc;
							opc++;
							document.getElementsByClassName("campo")[campo+1].name = preg;
							document.getElementsByClassName("campo")[campo+1].id = preg+'-'+opc;
							opc++;
							document.getElementsByClassName("campo")[campo+2].name = preg;
							document.getElementsByClassName("campo")[campo+2].id = preg+'-'+opc;
							opc++;
							document.getElementsByClassName("campo")[campo+3].name = preg;
							document.getElementsByClassName("campo")[campo+3].id = preg+'-'+opc;
							opc++;
							document.getElementsByClassName("campo")[campo+4].name = preg;
							document.getElementsByClassName("campo")[campo+4].id = preg+'-'+opc;
							campo+=4;
							preg++;
							break;
						case 'desarrollo':
							document.getElementsByClassName("campo")[campo].name = preg;
							document.getElementsByClassName("campo")[campo].id = preg;
							preg++;
							break;
					}
				}
			}
		}
	};
	xmlhttp.open("GET", "../../php/buscaencuesta.php?prov=" + prov, false);
	xmlhttp.send();
}

// limpia el formulario
function limpiar() {
	for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
		document.getElementsByClassName("campo")[campo].value = "";
	}
	document.getElementById("email").focus();
}

// Enviar los datos del formulario para procesar en el servidor
function enviar() {
	var id_proveedor = sessionStorage.getItem("id_proveedor");
	document.getElementById("id_proveedor").value = id_proveedor;

	var datos = new FormData();
	datos.append("email", document.getElementById("email").value);
	preg = 1;
	for (campo = 1; campo < document.getElementsByClassName("campo").length; campo++) {
		if (document.getElementsByClassName("campo")[campo].type=='checkbox'){
			datos.append(preg, document.getElementsByClassName("campo")[campo].checked);
		} else if (document.getElementsByClassName("campo")[campo].type=='radio') {
			if (document.getElementsByClassName("campo")[campo].checked) {
				datos.append(preg, document.getElementsByClassName("campo")[campo].value);
				preg++;
			}
		} else {
			datos.append(preg, document.getElementById(preg).value);
		}
	}
	datos.append("nombre", document.getElementById("nombre").value);
	datos.append("telefono", document.getElementById("telefono").value);
	datos.append("id_proveedor", document.getElementById("id_proveedor").value);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				alert(fmensaje(respuesta.mensaje));
				limpiar();
				// window.location.replace("www.clubdeconsumidores.com.ve/index.html");
				// window.location.replace("../index.html");
				window.location.replace("gracias.html");
			} else {
				alert(fmensaje(respuesta.mensaje));
			}
		}
	};
	xmlhttp.open("POST", "../../php/registraencuesta.php", false);
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
	xmlhttp.open("GET", "../../php/parametros.php", false);
	xmlhttp.send();
}