var busca1 = function () { buscasocio(this.value,1); }
var busca2 = function () { buscasocio(document.getElementById("email").value,2); }

// Cargar los datos iniciales de la forma y la etiquetas
function cargaforma() {
	buscatitulo();
	var logo;

	var params = fparamurl(window.location.search.substr(1));
	var prov;

	if (params==undefined) {
		prov = sessionStorage.getItem("id_proveedor");
	} else {
		prov = params.id;
		sessionStorage.setItem("id_proveedor",params.id);
	}

	var titulo = sessionStorage.getItem("nombresistema");
	// cargar parámetros de la tabla
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				document.title = titulo;
				logo = respuesta.proveedor.logo;
				if (logo!="") {
					document.getElementById("logo").src = "../img/" + logo;
				} else {
					document.getElementById("logo").src = "../img/" + 'sin_imagen.jpg';
				}
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
				document.getElementsByClassName("campo")[campo].id = respuesta.campos[campo];
			}
		}
	};
	xmlhttp.open("GET", "cupon.json", false);
	xmlhttp.send();

	document.getElementById("email").addEventListener('change', busca1);
	document.getElementById("enviar").innerHTML = 'Buscar';
	document.getElementById("enviar").addEventListener('click', busca2);
}

// limpia el formulario
function limpiar() {
	for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
		document.getElementsByClassName("campo")[campo].value = "";
	}
	for (campo = 1; campo < document.getElementsByClassName("cmps").length; campo++) {
		document.getElementsByClassName("cmps")[campo].style.display = 'none';
	}

	document.getElementById("email").addEventListener('change', busca1);
	document.getElementById("enviar").innerHTML = 'Buscar';
	document.getElementById("enviar").removeEventListener('click', enviar);
	document.getElementById("enviar").addEventListener('click', busca2);

	document.getElementById("email").style.background = "";
	document.getElementById("nombres").style.background = "";
	document.getElementById("apellidos").style.background = "";
	document.getElementById("telefono").style.background = "";

	document.getElementById("email").readOnly = false;
	document.getElementById("nombres").readOnly = false;
	document.getElementById("apellidos").readOnly = false;
	document.getElementById("telefono").readOnly = false;

	document.getElementById("email").focus();
}

// Enviar los datos del formulario para procesar en el servidor
function enviar() {
	var id_proveedor = sessionStorage.getItem("id_proveedor");
	document.getElementById("id_proveedor").value = id_proveedor;

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
			console.log(this.responseText);
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				alert(fmensaje(respuesta.mensaje)+"\n# de cupón: "+respuesta.cuponlargo);
				limpiar();
			} else {
				alert(fmensaje(respuesta.mensaje));
			}
		}
	};
	xmlhttp.open("POST", "../php/registracupon.php", false);
	xmlhttp.send(datos);
}

// Busca dato inicial para rellenar el formulario
function buscasocio(valor,opc) {
	arroba = 0;
	punto = 0;
	posa = 0;
	posp = 0;
	for (index = 0; index < valor.length; index++) {
		if (valor[index] == "@") { arroba++; posa = index; }
		if (valor[index] == ".") { punto++; posp = index; }
	}
	if (arroba + punto > 1 && posp > posa) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				respuesta = JSON.parse(this.responseText);
				if (respuesta.exito == 'SI') {
					document.getElementById("nombres").value = respuesta.nombres;
					document.getElementById("apellidos").value = respuesta.apellidos;
					document.getElementById("telefono").value = respuesta.telefono;

					document.getElementById("email").style.background = "yellow";
					document.getElementById("nombres").style.background = "yellow";
					document.getElementById("apellidos").style.background = "yellow";
					document.getElementById("telefono").style.background = "yellow";

					document.getElementById("email").readOnly = true;
					document.getElementById("nombres").readOnly = true;
					document.getElementById("apellidos").readOnly = true;
					document.getElementById("telefono").readOnly = true;

					for (campo = 1; campo < document.getElementsByClassName("cmps").length - 1; campo++) {
						document.getElementsByClassName("cmps")[campo].style.display = 'flex';
					}

					document.getElementById("socio").style.display = 'none';
					sessionStorage.setItem("socio", true);

					document.getElementById("factura").focus();

				}
			} else {
				for (campo = 1; campo < document.getElementsByClassName("cmps").length - 1; campo++) {
					document.getElementsByClassName("cmps")[campo].style.display = 'flex';
				}
				document.getElementById("socio").style.display = 'flex';
				sessionStorage.setItem("socio", false);
				document.getElementById("nombres").focus();

			}
			document.getElementById("email").removeEventListener('change', busca1);
			document.getElementById("enviar").innerHTML = 'Enviar';
			document.getElementById("enviar").removeEventListener('click', busca2);
			document.getElementById("enviar").addEventListener('click', enviar);
		};
		xmlhttp.open("GET", "../php/buscasocios.php?email=" + valor, false);
		xmlhttp.send();

	} else {
		if (opc==1) { alert('Email invalido.'); }
		document.getElementById("email").focus();
	}
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