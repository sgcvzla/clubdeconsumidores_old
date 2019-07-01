// Variables públicas, constantes y función de transformación de números.
var ruta = "img/";
var opcfiltro = 'Todas';

function cargadatos() {
	fparametros();
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText == 'No') {
				lista = '<h2 class="titulo">No hay comercios que mostrar.</h2>';
				document.getElementById("comercios").innerHTML = lista;
			} else {
				datos = JSON.parse(this.responseText);
				if (datos.filtros != undefined) {
					for (var i = 0; i < datos.filtros.length; i++) {
						txtopc = document.createTextNode(datos.filtros[i]);
						opc = document.createElement('option');
						opc.appendChild(txtopc);
						document.getElementById('opciones_filtros').appendChild(opc);
					}
				}
				document.getElementById("comercios").innerHTML = 'Cargando comercios...';
				lista = '';
				if (datos.registros != undefined) {
					for (var i = 0; i < datos.registros.length; i++) {
						lista += '<div class="item ' + datos.registros[i].categoria + '" align="center">';
						lista += '<div class="imagen" align="center">';
						lista += '<a id="' + datos.registros[i].id + '" href="cupones/cupon.html?id=' + datos.registros[i].id + '" onclick="sesionprov(this.id)">';
						if (fexisteUrl(ruta + datos.registros[i].imagen)) {
							if (datos.registros[i].imagen == "") {
								lista += '<img class="img_comercio" src="' + ruta + 'sin_imagen.jpg' + '" title="' + datos.registros[i].nombre + '" />';
							} else {
								lista += '<img class="img_comercio" src="' + ruta + datos.registros[i].imagen + '" title="' + datos.registros[i].nombre + '" />';
							}
						} else {
							if (datos.registros[i].imagen == "") {
								lista += '<img class="img_comercio" src="' + ruta + 'sin_imagen.jpg' + '" title="' + datos.registros[i].nombre + '" />';
							} else {
								lista += '<img class="img_comercio" src="' + ruta + datos.registros[i].imagen + '" title="' + datos.registros[i].nombre + '" />';
							}
						}
						lista += '</a>';
						lista += '</div>';
						lista += '</div>';
					}
					document.getElementById("comercios").innerHTML = lista;
				}
			}
		}
	}

	xmlhttp.open("GET", "php/buscacomercios.php", true);
	xmlhttp.send();
}

function filtrar(filtro) {
	var opcfiltro = filtro.value;
	prod = document.getElementsByClassName('item');
	for (var i = 0; i < prod.length; i++) {
		if (opcfiltro == "TODAS" || prod[i].className == 'item ' + opcfiltro) {
			prod[i].style.display = 'grid';
		} else {
			prod[i].style.display = 'none';
		}
	}
}

function sesionprov(valor) {
	sessionStorage.setItem("id_proveedor", valor);
}
