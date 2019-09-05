var cards, tipomoneda = 'bs';
var idsocio = sessionStorage.getItem("idsocio");
var idproveedor = sessionStorage.getItem("idproveedor");

if (idsocio==undefined) { idsocio = 1; }
if (idproveedor==undefined) { idproveedor = 1; }

var pagobs = function() { pagoenlinea('bs'); }
var pagodolar = function() { pagoenlinea('dolar'); }
var reportebs = function () { reporte('bs'); }
var reportedolar = function () { reporte('dolar'); }
/*
navigator.serviceWorker.register("./sw.js");

let promptEvent = null;
window.addEventListener("beforeinstallprompt",(e)=>{
	console.log("lista para instalar");
	promptEvent = e;
	document.getElementById("instalar").classList.add("Active");
});

document.getElementById("instalar").addEventListener("click", (e)=>{
	promptEvent = e;
});
*/

function inicio() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			console.log(respuesta);
			document.getElementById("titulosocio").innerHTML = respuesta.nombresocio;

			cards = respuesta.cards;
			for (var i = 0; i < cards.length; i++) {
				// Parte superior
				// Logo
				lgc = document.createElement("img");
				lgc.classList.add("left");
				lgc.src = "../img/"+cards[i].logoproveedor;
				lgc.width = 60;
				lgc.height = 60;
				// Titulo
				txtgc = document.createTextNode("Tarjeta Prepagada");
				tgc = document.createElement("h2");
				tgc.classList.add("title");
				tgc.appendChild(txtgc);
				// Monto
				txtm = document.createTextNode(formatNumber.new(cards[i].saldo)+" "+cards[i].simbolomoneda);
				mgc = document.createElement("h3");
				mgc.id = cards[i].card+"-monto";
				mgc.classList.add("price");
				mgc.appendChild(txtm);
				// Parte alta de la tarjeta
				tp = document.createElement("div");
				tp.classList.add("top");
				tp.style.background = cards[i].color;
				tp.appendChild(lgc);
				tp.appendChild(tgc);
				tp.appendChild(mgc);
				// Parte inferior
				// simbolo
				sgc = document.createElement("img");
				sgc.id = cards[i].card+"-qr";
				sgc.style.margin = "-5.5px 5.5px";
				sgc.src = "../img/"+cards[i].dibujomoneda;
				sgc.width = 50;
				sgc.height = 50;
				console.log(sgc.margin);
				// seccion del qr
				msgc = document.createElement("div");
				msgc.classList.add("left");
				msgc.appendChild(sgc);
				// Status
				txtst = document.createTextNode(cards[i].status);
				stgc = document.createElement("h3");
				stgc.id = cards[i].card+"-status";
				stgc.classList.add("status");
				if (cards[i].status!="Lista para usar") {
					stgc.style.color = "red";
				}
				stgc.appendChild(txtst);
				// seccion del status
				dstgc = document.createElement("div");
				dstgc.classList.add("right");
				dstgc.appendChild(stgc);
				// Parte baja de la tarjeta
				bt = document.createElement("div");
				bt.classList.add("bottom");
				bt.appendChild(msgc);
				bt.appendChild(dstgc);
				// Cuadro tarjeta
				dcard = document.createElement("div");
				dcard.id = cards[i].card;
				dcard.classList.add("block");
				dcard.classList.add("front");
				dcard.title = "Haga click para seleccionar";
				dcard.addEventListener('click', function(){ abremodal(this.id) });
				dcard.appendChild(tp);
				dcard.appendChild(bt);
				// Agregar al catálogo
				document.getElementById("cards").appendChild(dcard);
			}
		}
	};
	xmlhttp.open("GET", "../php/buscaprepago.php?idsocio="+idsocio, true);
	xmlhttp.send();
}

function abremodal2() {
	document.getElementById("compraprepago").style.display = 'block';
	document.getElementById("formulariocompra").style.display = 'none';
}

function formulario(moneda) {
	tipomoneda = moneda;
	document.getElementById("monedas").style.display = 'none';
	document.getElementById("formulariocompra").style.display = 'block';
}

function online(online) {
	var continuar = true, vacios = 0, campo = "";
	if (document.getElementById("montocompra").value=="" || document.getElementById("montocompra").value==undefined) {
		alert("El campo monto no puede quedar en blanco");
		vacios++;
		campo = "montocompra";
		continuar = false;
	}
	if (!document.getElementById("propietario").checked) {
		if ((document.getElementById("nombres2").value=="" || document.getElementById("nombres2").value==undefined) && vacios == 0) {
			alert("El campo nombre no puede quedar en blanco");
			vacios++;
			campo = "nombres2";
		}
		if ((document.getElementById("apellidos2").value=="" || document.getElementById("apellidos2").value==undefined) && vacios == 0) {
			alert("El campo apellidos no puede quedar en blanco");
			vacios++;
			campo = "apellidos2";
		}
		if ((document.getElementById("telefono2").value=="" || document.getElementById("telefono2").value==undefined) && vacios == 0) {
			alert("El campo teléfono no puede quedar en blanco");
			vacios++;
			campo = "telefono2";
		}
		if ((document.getElementById("email2").value=="" || document.getElementById("email2").value==undefined) && vacios == 0) {
			alert("El campo e-mail no puede quedar en blanco");
			vacios++;
			campo = "email2";
		}
		if (vacios>0) {
			continuar = false;
		}
	}
	if (continuar) {
		// document.getElementById("formulariocompra").style.display = 'none';
		// document.getElementById("monedas").style.display = 'flex';
		if (online) {
			pagoenlinea(tipomoneda); 
			// document.getElementById("bolivares").removeEventListener('click', reportebs);
			// // document.getElementById("dolares").removeEventListener('click', reportedolar);
			// document.getElementById("bolivares").addEventListener('click', pagobs);
			// // document.getElementById("dolares").addEventListener('click', pagodolar);
		} else {
			reportepago(tipomoneda); 
			// document.getElementById("bolivares").removeEventListener('click', pagobs);
			// // document.getElementById("dolares").removeEventListener('click', pagodolar);
			// document.getElementById("bolivares").addEventListener('click', reportebs);
			// // document.getElementById("dolares").addEventListener('click', reportedolar);
		}
	} else {
		document.getElementById(campo).focus();
	}
}

function pagoenlinea(divisa) {
	var datos = new FormData(), monto = document.getElementById("montocompra").value;
	datos.append("monto", document.getElementById("montocompra").value);
	if (document.getElementById("propietario").checked) {
		datos.append("propietario", "SI");
	} else {
		datos.append("propietario", "NO");
	}
	datos.append("nombres", document.getElementById("nombres2").value);
	datos.append("apellidos", document.getElementById("apellidos2").value);
	datos.append("telefono", document.getElementById("telefono2").value);
	datos.append("email", document.getElementById("email2").value);
	datos.append("idsocio", idsocio);
	datos.append("idproveedor", idproveedor);
	datos.append("moneda", divisa);

	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			respuesta = JSON.parse(this.responseText);
			if (respuesta.exito == 'SI') {
				propiedades="top=20%, left=50%, width=450, height=635, menubar=0, resizable=0, status=0, titlebar=0, toolbar=0";
				window.open("../php/formapagoenlinea.php?card="+respuesta.card+"&monto="+monto+"&ruta=prepago","_blank",propiedades);
			} else {
				alert('Ocurrió un error, por favor intentalo de nuevo.');
			}
		}
	};
	if (divisa=="bs") {
		xmlhttp.open("POST", "../php/pagoenlinea.php", true);
	// } else {
	// 	xmlhttp.open("POST", "../php/reportapago.php", true);
	}
	xmlhttp.send(datos);
}

function reportepago(divisa) {
	alert('reporte '+divisa);
}

function cerrarmodal2() {
	document.getElementById("monedas").style.display = 'flex';
	document.getElementById("formulariocompra").style.display = 'none';
	document.getElementById("compraprepago").style.display = 'none';
}

function abremodal(id) {
	document.getElementById(id+"-monto").innerHTML = document.getElementById(id+"-monto").innerHTML;
	document.getElementById(id+"-qr").src = document.getElementById(id+"-qr").src;
	document.getElementById("presentaprepago").style.display = 'block';
}

function cerrarmodal() {
	document.getElementById("presentaprepago").style.display = 'none';
}

function habilitar(siono) {
	if (siono) {
		document.getElementById("nombres2").value = "";
		document.getElementById("apellidos2").value = "";
		document.getElementById("telefono2").value = "";
		document.getElementById("email2").value = "";

		document.getElementById("nombres2").disabled = true;
		document.getElementById("apellidos2").disabled = true;
		document.getElementById("telefono2").disabled = true;
		document.getElementById("email2").disabled = true;
	} else {
		document.getElementById("nombres2").disabled = false;
		document.getElementById("apellidos2").disabled = false;
		document.getElementById("telefono2").disabled = false;
		document.getElementById("email2").disabled = false;
	}
}
