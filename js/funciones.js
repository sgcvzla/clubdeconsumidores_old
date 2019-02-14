// Función para mostrar los decimales que se quieran
var formatNumber = {
	separador: ".", // separador para los miles
	sepDecimal: ',', // separador para los decimales
	formatear: function (num) {
		num += '';
		var splitStr = num.split('.');
		var splitLeft = splitStr[0];
		var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : this.sepDecimal + '00';
		var regx = /(\d+)(\d{3})/;
		while (regx.test(splitLeft)) {
			splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
		}
		return this.simbol + splitLeft + splitRight;
	},
	new: function (num, simbol) {
		this.simbol = simbol || '';
		return this.formatear(num);
	}
}

// Cargar los parámetros generales del sistema
function fparametros() {
	// cargar parámetros de la tabla
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
	xmlhttp.open("GET", "php/parametros.php", false);
	xmlhttp.send();
}

function fexisteUrl(url) {
	var http = new XMLHttpRequest();
	http.open('HEAD', url, false);
	http.send();
	return http.status != 404;
}

function fmensaje(texto) {
	var mensaje = '';
	for (let index = 0; index < texto.length; index++) {
		mensaje += texto[index];
		if (texto.length > 1 && index + 1 < texto.length) {
			mensaje += '\n';
		}
	}
	return mensaje;
}

function fparamurl(xurl) {
	var paramstr = xurl;
	var paramarr = paramstr.split ("&");
	var params = {};

	for ( var i = 0; i < paramarr.length; i++) {
	    var tmparr = paramarr[i].split("=");
	    params[tmparr[0]] = decodeURI(tmparr[1]);
	}
	return params;
}