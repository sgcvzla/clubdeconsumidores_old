function fparametros(forma) {
	var logo;
	// cargar parámetros de la tabla
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				document.title = respuesta.parametros.nombresistema;
				logo = respuesta.parametros.logosistema;
				document.getElementById("logo").src = "../img/"+logo;
				document.getElementById("logo").title = document.title;
			}
		}
	};
	xmlhttp.open("GET", "../php/parametros.php", false);
	xmlhttp.send();

	// cargar parámetros del json: etiquetas y elementos de forma
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			parametros = JSON.parse(this.responseText);
			document.title = document.title + ' - '+ parametros.etiquetasfijas.titulopagina;
			tituloformulario = parametros.etiquetasfijas.tituloformulario;
			document.getElementById("tituloformulario").innerHTML = tituloformulario;
		}
	};
	xmlhttp.open("GET", "../_config/config.json", false);
	xmlhttp.send();

	fcargarcampos(forma);
}

function fgrabar() {
	var datos = new FormData();
	var respuesta;

	if (document.getElementsByClassName("campo").length) {
		for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
			if (document.getElementsByClassName("campo")[campo].type=='checkbox'){
				datos.append(document.getElementsByClassName("campo")[campo].id, document.getElementsByClassName("campo")[campo].checked);
			} else {
				datos.append(document.getElementsByClassName("campo")[campo].id, document.getElementsByClassName("campo")[campo].value);
			}
		}
	}

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			respuesta = JSON.parse(this.responseText);
		} else {
			mensaje = '{"exito":"NO","mensaje":"Falló el registro del cupón, \ncomuniquese por Whatsapp al +584244071820","cupon":"0"}';
			respuesta = JSON.parse(mensaje);
			console.log(respuesta);
		}
	};
	xmlhttp.open("POST", "../php/registracupon.php", false);
	xmlhttp.send(datos);
	return respuesta;
}

function flimpiar() {
	if (document.getElementsByClassName("campo").length) {
		for (campo = 0; campo < document.getElementsByClassName("campo").length; campo++) {
			document.getElementsByClassName("campo")[campo].value = "";
		}
	}
}

function fcargarcampos(formulario){
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
		}
	};
	xmlhttp.open("GET", "../_config/formularios.json", false);
	xmlhttp.send();

	console.log(respuesta[formulario]);
	if (respuesta[formulario].etiquetas.length== document.getElementsByClassName("etiq").length) {
		for (campo = 0; campo < respuesta[formulario].etiquetas.length; campo++) {
			document.getElementsByClassName("etiq")[campo].innerHTML = respuesta[formulario].etiquetas[campo];
		}
		console.log(respuesta[formulario].campos.length);
		for (campo = 0; campo < respuesta[formulario].campos.length; campo++) {
			document.getElementsByClassName("campo")[campo].setAttribute("id",respuesta[formulario].campos[campo]);
			console.log(respuesta[formulario].campos[campo]);
		}
	} else {
		alert('Error en definición de etiquetas de campos');
	}
}
