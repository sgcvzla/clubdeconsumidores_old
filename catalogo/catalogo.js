//
// Variables públicas, constantes y función de transformación de números.
//
var cod = "";
var tasaiva = 0;
var ruta = "img/";
var opcfiltro = 'TODAS';
var items = 0;
var sbt = 0.00;
var iva = 0.00;
var tot = 0.00;
var orden = new Array();
// función para mostrar los decimales que se quieran
var formatNumber = {
	separador: ".", // separador para los miles
	sepDecimal: ',', // separador para los decimales

	formatear:function (num){
		num +='';
		var splitStr = num.split('.');
		var splitLeft = splitStr[0];
		var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : this.sepDecimal + '00';
		var regx = /(\d+)(\d{3})/;
		while (regx.test(splitLeft)) {
			splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
		}
		return this.simbol + splitLeft +splitRight;
	},
	new:function(num, simbol){
		this.simbol = simbol ||'';
		return this.formatear(num);
	}
}

//
// Función para cargar el catálogo
//
function cargadatos() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText=='No') {
				lista = '<h2 class="titulo">No hay productos que mostrar.</h2>';
				document.getElementById("productos").innerHTML = lista; 
			} else {
				datos = JSON.parse(this.responseText);
				tasaiva = datos.iva / 100;
				if (datos.filtros!=undefined) {
					for (var i = 0; i < datos.filtros.length; i++) {
						txtopc = document.createTextNode(datos.filtros[i]);
						opc = document.createElement('option');
						opc.appendChild(txtopc);
						document.getElementById('opciones_filtros').appendChild(opc);
					}
				}
				document.getElementById("productos").innerHTML = 'Cargando productos...'; 
				lista = '';
				if (datos.registros!=undefined) {
					for (var i = 0; i < datos.registros.length; i++) {
						if (datos.registros[i].familia=="pruebas") {
							cod = datos.tit_codigo;
							lista += '<div class="item '+datos.registros[i].familia+'" align="center">';
								lista += '<div class="imagen" align="center">';
									if (existeUrl(datos.registros[i].imagen)) {
										lista += '<img class="img_producto" src="'+datos.registros[i].imagen+'" alt=""/>';
									} else {
										lista += '<img class="img_producto" src="'+ruta+'sin_imagen.jpg'+'" alt=""/>';
									}
								lista += '</div>';
								lista += '<span class="detalle">';
									// lista += datos.registros[i].id_pro+'<br/>';
									lista += datos.registros[i].desc_corta+'<br>';

									precio  = Math.round((datos.registros[i].precio_pro) * 100) / 100;
									priva   = Math.round((datos.registros[i].precio_pro*tasaiva) * 100) / 100;
									totprc  = Math.round((datos.registros[i].precio_pro*(1+tasaiva)) * 100) / 100;

									lista += 'Precio Bs. '+formatNumber.new(precio)+'<br>';
									lista += 'I.V.A. '+formatNumber.new(priva)+'<br>';
									lista += 'Total Bs. '+formatNumber.new(totprc)+'<br>';
								lista += '</span>';
								lista += '<div class="botagr">';
									// lista += '<button id="'+datos.registros[i].id_pro+'" class="boton" onclick="agregaorden(this.id,event)">';
									lista += '<button id="'+datos.registros[i].id_pro+'" onclick="agregaorden(this.id,event)">';
										lista += 'Agregar a la órden';
									lista += '</button>';
								lista += '</div>';
							lista += '</div>';
						} else {
							lista += '<div class="item '+datos.registros[i].familia+'" align="center">';
								lista += '<div class="imagen" align="center">';
									if (existeUrl(datos.registros[i].imagen)) {
										lista += '<img class="img_producto" src="'+datos.registros[i].imagen+'" alt=""/>';
									} else {
										lista += '<img class="img_producto" src="'+ruta+'sin_imagen.jpg'+'" alt=""/>';
									}
								lista += '</div>';
								lista += '<span class="detalle">';
									// lista += datos.registros[i].id_pro+'<br/>';
									lista += datos.registros[i].desc_corta+'<br>';

									precio  = Math.round((datos.registros[i].precio_pro) * 100) / 100;
									priva   = Math.round((datos.registros[i].precio_pro*tasaiva) * 100) / 100;
									totprc  = Math.round((datos.registros[i].precio_pro*(1+tasaiva)) * 100) / 100;

									lista += 'Precio Bs. '+formatNumber.new(precio)+'<br>';
									lista += 'I.V.A. '+formatNumber.new(priva)+'<br>';
									lista += 'Total Bs. '+formatNumber.new(totprc)+'<br>';
								lista += '</span>';
								lista += '<div class="botagr">';
									lista += '<button id="'+datos.registros[i].id_pro+'" onclick="agregaorden(this.id,event)">';
										lista += 'Agregar a la órden';
									lista += '</button>';
								lista += '</div>';
							lista += '</div>';
						}
					}
					document.getElementById("productos").innerHTML = lista; 
					iva_a_mostrar = tasaiva*100;
					document.getElementById("tasa_iva1").innerHTML = iva_a_mostrar; 
				}
			}
		}
	}

	xmlhttp.open("GET", "../php/buscaproductos.php", true);
	xmlhttp.send();
}

//
// Esta función quita de la pantalla el catálogo y muestra la orden
//
function muestraorden(event) {
	event.preventDefault();
	document.getElementById("btn_ctlg").style.display = "block";
	document.getElementById("encabezado").innerHTML = "<h3>Agregar o eliminar productos</h3>";
	document.getElementById("btn_orden").style.display = "none";
	document.getElementById("btn_online").style.display = "block";
	document.getElementById("btn_cnfrm").style.display = "block";

	document.getElementById("productos").style.display = "none";
	document.getElementById("cintafiltros").style.display = "none";

	document.getElementById("cabeceraorden").style.display = "block";
	document.getElementById("cuerpoorden").style.display = "block";
	document.getElementById("pieorden").style.display = "grid";
}

//
// Esta función quita de la pantalla la orden y muestra el catálogo
//
function muestracatalogo(event) {
	event.preventDefault();
	document.getElementById("btn_ctlg").style.display = "none";
	document.getElementById("encabezado").innerHTML = "<h3>Catálogo</h3>";
	if (items>0) {
		document.getElementById("btn_orden").style.display = "block";
	} else {
		document.getElementById("btn_orden").style.display = "none";
	}

	document.getElementById("btn_online").style.display = "none";
	document.getElementById("btn_cnfrm").style.display = "none";

	document.getElementById("productos").style.display = "grid";
	document.getElementById("cintafiltros").style.display = "flex";

	document.getElementById("cabeceraorden").style.display = "none";
	document.getElementById("cuerpoorden").style.display = "none";
	document.getElementById("pieorden").style.display = "none";
	document.getElementById("ordenconfirmada").style.display = "none";
}

//
// Esta función muestra el mensaje de confirmación para cerrar la orden
//
function confirmaorden(event) {
	event.preventDefault();
	aviso = "Ha seleccionado enviar la órden y pagar después,\n";
	aviso += "será despachada una vez que haya realizado el pago y\n";
	aviso += "este sea concliado por el área administrativa.\n\n";
	aviso += "¿Desea continuar?";
	if (confirm(aviso)) {
		sjson = '[';
		coma = false;
		for (var i = 0; i < orden.length; i++) {
			if (coma) {
				sjson += ',';
			}
			sjson += '{"id_pro":"'+orden[i][0]+'"'+',';
			sjson += '"precio_pro":'+orden[i][2]+',';
			sjson += '"cantidad":'+orden[i][3]+"}";
			coma = true;
		}
		sjson += "]"
		var envdatos = new FormData();
		envdatos.append("orden", sjson);

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				rsp = JSON.parse(this.responseText);
				if (rsp.exito=="SI") {
					document.getElementById('mensajeconfirmacion').innerHTML = "Se generó la orden No."+rsp.orden_id;
					var nodos = document.getElementById('cuerpoconforden');
					var longitud = nodos.childNodes.length
					for (var i = 0; i < longitud; i++) {
						nodos.removeChild(nodos.childNodes[0]);
					}
					for (var i = 0; i < orden.length; i++) {
						var dfila = document.createElement("div");

						var dcol1 = document.createElement("div");
						var dcol2 = document.createElement("div");
						var dcol3 = document.createElement("div");
						var dcol4 = document.createElement("div");
						var dcol5 = document.createElement("div");

						var dcol31 = document.createElement("div");
						var dcol51 = document.createElement("div");

						txt11 = 'Bs. '+formatNumber.new(orden[i][2]);

						txt21 = 'Bs. '+formatNumber.new(orden[i][4]);

						var dtxt1 = document.createTextNode("    "+orden[i][0]);
						var dtxt2 = document.createTextNode(orden[i][1]);
						var dtxt31 = document.createTextNode(txt11);
						var dtxt4 = document.createTextNode("    "+orden[i][3]+"    ");
						var dtxt51 = document.createTextNode(txt21);

						dfila.classList.add("fila");

						dcol1.classList.add("col1");
						dcol2.classList.add("col2");
						dcol3.classList.add("col3");
						dcol4.classList.add("col4");
						dcol5.classList.add("col5");

						dcol1.appendChild(dtxt1)
						dcol2.appendChild(dtxt2);

						dcol31.appendChild(dtxt31);
						dcol3.appendChild(dcol31);

						dcol4.appendChild(dtxt4);

						dcol51.appendChild(dtxt51);
						dcol5.appendChild(dcol51);

						dfila.appendChild(dcol1);
						dfila.appendChild(dcol2);
						dfila.appendChild(dcol3);
						dfila.appendChild(dcol4);
						dfila.appendChild(dcol5);

						document.getElementById('cuerpoconforden').appendChild(dfila);
					}
					sbt1 = 0.00;
					iva1 = 0.00;
					tot1 = 0.00;
					items = 0;
					for (i = 0; i < orden.length; i++) {
						items += orden[i][3];
						sbt1 += orden[i][4];
					}
					iva1 = Math.round((sbt1 * tasaiva) * 100) / 100;
					tot1 = Math.round((sbt1 + iva1) * 100) / 100;

					sbt = Math.round((sbt1) * 100) / 100;
					iva = Math.round((iva1) * 100) / 100;
					tot = Math.round((tot1) * 100) / 100;

					men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

					men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

					men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

					document.getElementById("tasa_iva2").innerHTML = iva_a_mostrar; 

					document.getElementById('piesbtcnf').innerHTML = men1;
					document.getElementById('pieivacnf').innerHTML = men2;
					document.getElementById('pietotcnf').innerHTML = men3;

					document.getElementById("encabezado").innerHTML = "<h3>Resumen de la órden</h3>";
					document.getElementById("btn_orden").style.display = "none";
					document.getElementById("btn_online").style.display = "none";
					document.getElementById("btn_cnfrm").style.display = "none";

					document.getElementById("ordenconfirmada").style.display = "block";
					document.getElementById("cabeceraorden").style.display = "none";
					document.getElementById("cuerpoorden").style.display = "none";
					document.getElementById("pieorden").style.display = "none";
					items = 0;
					orden = [];
					var nodos = document.getElementById('cuerpoorden');
					var longitud = nodos.childNodes.length
					for (var i = 0; i < longitud; i++) {
						nodos.removeChild(nodos.childNodes[0]);
					}
					document.getElementById("items_orden").style.display = "none";
				} else {
					document.getElementById("encabezado").innerHTML = "<h3>Resumen de la órden</h3>";
					document.getElementById("btn_orden").style.display = "none";
					document.getElementById("btn_online").style.display = "none";
					document.getElementById("btn_cnfrm").style.display = "none";

					document.getElementById("ordenconfirmada").style.display = "block";
					document.getElementById("cabeceraorden").style.display = "none";
					document.getElementById("cuerpoorden").style.display = "none";
					document.getElementById("pieorden").style.display = "none";
					document.getElementById('mensajeconfirmacion').innerHTML = "Ocurrió un error, comuniquese con soporte técnico al +584244071820";
				}
			}
		};
		xmlhttp.open("POST", "../php/colocaorden.php", true);
		xmlhttp.send(envdatos);
	}
}

//
// Agrega un elemento del catálogo a la orden y si ya está, incrementa la cantidad
//
function agregaorden(id,event) {
	event.preventDefault();
	var seguir = true;
	for (var i = 0; i < datos.registros.length; i++) {
		if (datos.registros[i].id_pro==id) {
			fila = i;
			break;
		}
	}
	if (datos.registros[fila].aviso) {
		aviso = "Este producto se despachará el día ";
		aviso += datos.registros[i].fecha_aviso.substr(8,2)+"/"+datos.registros[i].fecha_aviso.substr(5,2)+"/"+datos.registros[i].fecha_aviso.substr(0,4)
		aviso += ".\n\n ¿Está usted de acuerdo?"
		if (confirm(aviso)) {
			seguir = true;
		} else {
			seguir = false;
		}
	} else {
		seguir = true;
	}
	if (seguir) {
		if (orden.length>0) {
			nuevo = true;
			for (i = 0; i < orden.length; i++) {
				if (orden[i][0]==id) {
					orden = cambiaorden(orden,id,false,i);
					nuevo = false;
					break;
				}
			}
			if (nuevo) {
				orden = cambiaorden(orden,id,true,0);
			}
		} else {
			orden = cambiaorden(orden,id,true,0);
		}
		if (items>0) {
			document.getElementById("items_orden").style.display = "block";
			document.getElementById("btn_orden").style.display = "block";
		} else {
			document.getElementById("items_orden").style.display = "none";
			document.getElementById("btn_orden").style.display = "none";
		}
	}
}

//
// Esta función crea los elementos que se muestran en la orden o los modifica si a están
//
function cambiaorden(orden,id,nuevo,fila) {
	if (nuevo) {
//		precio = Math.round((Math.random()*10) * 100) / 100;
		orden.push(Array(5));
		fila = orden.length-1;
		for (var i = 0; i < datos.registros.length; i++) {
			if (datos.registros[i].id_pro==id) {
				precio = datos.registros[i].precio_pro;
				orden[fila][0] = id;
				orden[fila][1] = datos.registros[i].desc_corta;
				orden[fila][2] = Math.round(datos.registros[i].precio_pro * 100) / 100;
				orden[fila][3] = 1;
				orden[fila][4] = Math.round(precio * 100) / 100;
				break;
			}
		}

		var dfila = document.createElement("div");

		var dcol1 = document.createElement("div");
		var dcol2 = document.createElement("div");
		var dcol3 = document.createElement("div");
		var dcol4 = document.createElement("div");
		var dcol5 = document.createElement("div");

		var dcol31 = document.createElement("div");
		var dcol32 = document.createElement("div");
		var dcol33 = document.createElement("div");
		var dcol51 = document.createElement("div");
		var dcol52 = document.createElement("div");
		var dcol53 = document.createElement("div");

		var ielim = document.createElement('img');
		var imas = document.createElement('img');
		var imenos = document.createElement('img');

		ielim.src = "times-solid.svg";
		imas.src = "plus-solid.svg";
		imenos.src = "minus-solid.svg";

		ielim.width = 16;
		imas.width = 16;
		imenos.width = 16;

		ielim.style.verticalAlign = 'middle';
		imas.style.verticalAlign = 'middle';
		imenos.style.verticalAlign = 'middle';

		ielim.addEventListener('click', function() { eliminar(this,orden); }, false);
		imas.addEventListener('click',  function() { sumar(this,orden); }, false);
		imenos.addEventListener('click',  function() { restar(this,orden); }, false);

		ielim.style.cursor = 'pointer';
		ielim.title = 'Eliminar este renglón de la órden';

		imas.style.cursor = 'pointer';
		imas.title = 'Agregar otro item a la órden';

		imenos.style.cursor = 'pointer';
		imenos.title = 'Quitar un item de la órden';

		txt11 = 'Bs. '+formatNumber.new(orden[fila][2]);

		txt21 = 'Bs. '+formatNumber.new(orden[fila][4]);

		var dtxt1 = document.createTextNode("    "+orden[fila][0]);
		var dtxt2 = document.createTextNode(orden[fila][1]);
		var dtxt31 = document.createTextNode(txt11);
		var dtxt4 = document.createTextNode("    "+orden[fila][3]+"    ");
		var dtxt51 = document.createTextNode(txt21);

		fila++;
		fl =  "fila" + fila;
		cl = "col" + fila;

		dfila.id = id;
		dfila.classList.add("fila");

		dcol1.classList.add("col1");
		dcol2.classList.add("col2");
		dcol3.classList.add("col3");
		dcol4.classList.add("col4");
		dcol5.classList.add("col5");

		dcol1.appendChild(ielim);
		dcol1.appendChild(dtxt1)

		dcol2.appendChild(dtxt2);

		dcol31.appendChild(dtxt31);
		dcol3.appendChild(dcol31);

		dcol4.appendChild(imenos);
		dcol4.appendChild(dtxt4);
		dcol4.appendChild(imas);

		dcol51.appendChild(dtxt51);
		dcol5.appendChild(dcol51);

		dfila.appendChild(dcol1);
		dfila.appendChild(dcol2);
		dfila.appendChild(dcol3);
		dfila.appendChild(dcol4);
		dfila.appendChild(dcol5);
		
		document.getElementById('cuerpoorden').appendChild(dfila);
	} else {
		cant = orden[fila][3];
		if (cant+1<=10) {
			cant++;
			orden[fila][3] = cant;
			orden[fila][4] = Math.round((orden[fila][2]*orden[fila][3]) * 100) / 100;

			txt21 = 'Bs. '+formatNumber.new(orden[fila][4]);

			var dtxt4 = document.createTextNode("    "+orden[fila][3]+"    ");
			var dtxt51 = document.createTextNode(txt21);

			var dcol5 = document.createElement("div");
			var dcol51 = document.createElement("div");

			dcol51.appendChild(dtxt51);

			dcol5.appendChild(dcol51);

			aux = document.getElementById('cuerpoorden').childNodes;
			for (var i = 0; i < aux.length; i++) {
				if (aux[i].id==id) {
					doldtxt1 = aux[i].childNodes[3].childNodes[1];
					doldtxt2 = aux[i].childNodes[4].childNodes[0];

					aux[i].childNodes[3].replaceChild(dtxt4,doldtxt1);
					aux[i].childNodes[4].replaceChild(dcol51,doldtxt2);
					break;
				}
			}
		}
	}

	sbt1 = 0.00;
	iva1 = 0.00;
	tot1 = 0.00;
	items = 0;
	for (i = 0; i < orden.length; i++) {
		items += orden[i][3];
		sbt1 += orden[i][4];
	}
	iva1 = Math.round((sbt1 * tasaiva) * 100) / 100;
	tot1 = Math.round((sbt1 + iva1) * 100) / 100;

	sbt = Math.round((sbt1) * 100) / 100;
	iva = Math.round((iva1) * 100) / 100;
	tot = Math.round((tot1) * 100) / 100;

	men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

	men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

	men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

	document.getElementById('piesbt').innerHTML = men1;
	document.getElementById('pieiva').innerHTML = men2;
	document.getElementById('pietot').innerHTML = men3;
	document.getElementById("cantidad").innerHTML = items;
	if (items>0) {
		document.getElementById("items_orden").style.display = "block";
	} else {
		document.getElementById("items_orden").style.display = "none";
	}
	return orden;
}

//
// Comportamiento del botón sumar
//
function sumar(x,orden) {
	xitem = x.parentElement.parentElement.id;
	var fila=0;
	for (var i = 0; i < orden.length; i++) {
		if (orden[i][0]==xitem) {
			fila = i;
			break;
		}
	}
	orden = cambiaorden(orden,xitem,false,fila);
}

//
// Comportamiento del botón restar
//
function restar(x,orden) {
	xitem = x.parentElement.parentElement.id;
	var fila=0;
	for (var i = 0; i < orden.length; i++) { 
		if (orden[i][0]==xitem) {
			fila = i;
			break;
		}
	}
	cant = orden[fila][3];
	if (cant-1>0) {
		cant--;
		orden[fila][3] = cant;
		orden[fila][4] = Math.round((orden[fila][2]*orden[fila][3]) * 100) / 100;

		txt21 = 'Bs. '+formatNumber.new(orden[fila][4]);

		var dtxt4 = document.createTextNode("    "+orden[fila][3]+"    ");
		var dtxt51 = document.createTextNode(txt21);

		var dcol5 = document.createElement("div");
		var dcol51 = document.createElement("div");

		dcol51.appendChild(dtxt51);

		dcol5.appendChild(dcol51);

////////////////////////////////
		aux = document.getElementById('cuerpoorden').childNodes;
		for (var i = 0; i < aux.length; i++) {
			if (aux[i].id==xitem) {
				doldtxt1 = aux[i].childNodes[3].childNodes[1];
				doldtxt2 = aux[i].childNodes[4].childNodes[0];

				aux[i].childNodes[3].replaceChild(dtxt4,doldtxt1);
				aux[i].childNodes[4].replaceChild(dcol51,doldtxt2);
				break;
			}
		}
////////////////////////////////
		sbt1 = 0.00;
		iva1 = 0.00;
		tot1 = 0.00;
		items = 0;

		for (i = 0; i < orden.length; i++) {
			items += orden[i][3];
			sbt1 += orden[i][4];
		}
		iva1 = Math.round((sbt1 * tasaiva) * 100) / 100;
		tot1 = Math.round((sbt1 + iva1) * 100) / 100;

		sbt = Math.round((sbt1) * 100) / 100;
		iva = Math.round((iva1) * 100) / 100;
		tot = Math.round((tot1) * 100) / 100;

		men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

		men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

		men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

		document.getElementById('piesbt').innerHTML = men1;
		document.getElementById('pieiva').innerHTML = men2;
		document.getElementById('pietot').innerHTML = men3;
		document.getElementById("cantidad").innerHTML = items;
		if (items>0) {
			document.getElementById("items_orden").style.display = "block";
		} else {
			document.getElementById("items_orden").style.display = "none";
		}
	}
}

//
// Comportamiento del botón eliminar
//
function eliminar(x,orden) {
	xitem = x.parentElement.parentElement.id;
	for (var i = 0; i < orden.length; i++) { 
		if (orden[i][0]==xitem) {
			fila = i;
			break;
		}
	}
	orden.splice(fila,1);
	document.getElementById('cuerpoorden').removeChild(document.getElementById('cuerpoorden').childNodes[fila]);
	if (orden.length>0) {
		sbt1 = 0.00;
		iva1 = 0.00;
		tot1 = 0.00;
		items = 0;
		for (i = 0; i < orden.length; i++) {
			items += orden[i][3];
			sbt1 += orden[i][4];
		}
		iva1 = Math.round((sbt1 * tasaiva) * 100) / 100;
		tot1 = Math.round((sbt1 + iva1) * 100) / 100;

		sbt = Math.round((sbt1) * 100) / 100;
		iva = Math.round((iva1) * 100) / 100;
		tot = Math.round((tot1) * 100) / 100;

		men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

		men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

		men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

		document.getElementById('piesbt').innerHTML = men1;
		document.getElementById('pieiva').innerHTML = men2;
		document.getElementById('pietot').innerHTML = men3;
		document.getElementById("cantidad").innerHTML = items;
		if (items>0) {
			document.getElementById("items_orden").style.display = "block";
		} else {
			document.getElementById("items_orden").style.display = "none";
		}
	} else {
		sbt1 = 0.00;
		iva1 = 0.00;
		tot1 = 0.00;
		items = 0;

		sbt = Math.round((sbt1) * 100) / 100;
		iva = Math.round((iva1) * 100) / 100;
		tot = Math.round((tot1) * 100) / 100;

		men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

		men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

		men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

		// document.getElementById('pts_orden').innerHTML = 'Al cancelar esta orden usted sumará '+pts+' puntos a su cuenta personal (PM)';
		// document.getElementById('pts_orden_conf').innerHTML = 'Al cancelar esta orden usted sumará '+pts+' puntos a su cuenta personal (PM)';
		document.getElementById('piesbt').innerHTML = men1;
		document.getElementById('pieiva').innerHTML = men2;
		document.getElementById('pietot').innerHTML = men3;
		document.getElementById("cantidad").innerHTML = items;

		document.getElementById("btn_ctlg").style.display = "none";
		document.getElementById("encabezado").innerHTML = "CATÁLOGO";
		if (items>0) {
			document.getElementById("items_orden").style.display = "block";
			document.getElementById("btn_orden").style.display = "block";
		} else {
			document.getElementById("items_orden").style.display = "none";
			document.getElementById("btn_orden").style.display = "none";
		}

		document.getElementById("btn_online").style.display = "none";
		document.getElementById("btn_cnfrm").style.display = "none";

		document.getElementById("productos").style.display = "grid";
		document.getElementById("cintafiltros").style.display = "flex";

		document.getElementById("cabeceraorden").style.display = "none";
		document.getElementById("cuerpoorden").style.display = "none";
		document.getElementById("pieorden").style.display = "none";
		document.getElementById("ordenconfirmada").style.display = "none";
	}
}

function existeUrl(url) {
   var http = new XMLHttpRequest();
   http.open('HEAD', url, false);
   http.send();
   return http.status!=404;
}

function filtrar(filtro) {
	var opcfiltro = filtro.value;
	prod = document.getElementsByClassName('item');
	for (var i = 0; i < prod.length; i++) {
		if (opcfiltro=="TODAS" || prod[i].className=='item '+opcfiltro) {
			prod[i].style.display = 'grid';
		} else {
			prod[i].style.display = 'none';
		}
	}
}

function Abrir_ventana(event){
	event.preventDefault();
//	if (cod=="00005") {
		aviso = "Ha seleccionado enviar la órden y pagar en linea,\n";
		aviso += "se abrirá una ventana para introducir los datos de\n";
		aviso += "su tarjeta de crédito y se procesará el pago,\n\n";
		aviso += "¿Desea continuar?";
		if (confirm(aviso)) {
			sjson = '[';
			coma = false;
			for (var i = 0; i < orden.length; i++) {
				if (coma) {
					sjson += ',';
				}
				sjson += '{"id_pro":"'+orden[i][0]+'"'+',';
				sjson += '"precio_pro":'+orden[i][2]+',';
				sjson += '"cantidad":'+orden[i][3]+"}";
				coma = true;
			}
			sjson += "]"
			var envdatos = new FormData();
			envdatos.append("orden", sjson);

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					rsp = JSON.parse(this.responseText);
					if (rsp.exito=="SI") {
						document.getElementById('mensajeconfirmacion').innerHTML = "Se generó la orden No."+rsp.orden_id;
						var nodos = document.getElementById('cuerpoconforden');
						var longitud = nodos.childNodes.length
						for (var i = 0; i < longitud; i++) {
							nodos.removeChild(nodos.childNodes[0]);
						}
						for (var i = 0; i < orden.length; i++) {
							var dfila = document.createElement("div");

							var dcol1 = document.createElement("div");
							var dcol2 = document.createElement("div");
							var dcol3 = document.createElement("div");
							var dcol4 = document.createElement("div");
							var dcol5 = document.createElement("div");

							var dcol31 = document.createElement("div");
							var dcol32 = document.createElement("div");
							var dcol33 = document.createElement("div");
							var dcol51 = document.createElement("div");
							var dcol52 = document.createElement("div");
							var dcol53 = document.createElement("div");

							txt11 = 'Bs. '+formatNumber.new(orden[i][2]);

							txt21 = 'Bs. '+formatNumber.new(orden[i][4]);

							var dtxt1 = document.createTextNode("    "+orden[i][0]);
							var dtxt2 = document.createTextNode(orden[i][1]);
							var dtxt31 = document.createTextNode(txt11);
							var dtxt4 = document.createTextNode("    "+orden[i][3]+"    ");
							var dtxt51 = document.createTextNode(txt21);

							dfila.classList.add("fila");

							dcol1.classList.add("col1");
							dcol2.classList.add("col2");
							dcol3.classList.add("col3");
							dcol4.classList.add("col4");
							dcol5.classList.add("col5");

							dcol1.appendChild(dtxt1)
							dcol2.appendChild(dtxt2);

							dcol31.appendChild(dtxt31);
							dcol3.appendChild(dcol31);
							dcol3.appendChild(dcol33);

							dcol4.appendChild(dtxt4);

							dcol51.appendChild(dtxt51);
							dcol5.appendChild(dcol51);

							dfila.appendChild(dcol1);
							dfila.appendChild(dcol2);
							dfila.appendChild(dcol3);
							dfila.appendChild(dcol4);
							dfila.appendChild(dcol5);

							document.getElementById('cuerpoconforden').appendChild(dfila);
						}
						sbt1 = 0.00;
						iva1 = 0.00;
						tot1 = 0.00;
						items = 0;
						for (i = 0; i < orden.length; i++) {
							items += orden[i][3];
							sbt1 += orden[i][4];
						}
						iva1 = Math.round((sbt1 * tasaiva) * 100) / 100;
						tot1 = Math.round((sbt1 + iva1) * 100) / 100;

						sbt = Math.round((sbt1) * 100) / 100;
						iva = Math.round((iva1) * 100) / 100;
						tot = Math.round((tot1) * 100) / 100;

						men1 = '<b>Bs. '+ formatNumber.new(sbt)+'<br/>';

						men2 = '<b>Bs. '+ formatNumber.new(iva)+'<br/>';

						men3 = '<b>Bs. '+ formatNumber.new(tot)+'<br/>';

						document.getElementById("tasa_iva2").innerHTML = iva_a_mostrar; 

						document.getElementById('piesbtcnf').innerHTML = men1;
						document.getElementById('pieivacnf').innerHTML = men2;
						document.getElementById('pietotcnf').innerHTML = men3;

						document.getElementById("encabezado").innerHTML = "<h3>Resumen de la órden</h3>";
						document.getElementById("btn_orden").style.display = "none";
						document.getElementById("btn_online").style.display = "none";
						document.getElementById("btn_cnfrm").style.display = "none";

						document.getElementById("ordenconfirmada").style.display = "block";
						document.getElementById("cabeceraorden").style.display = "none";
						document.getElementById("cuerpoorden").style.display = "none";
						document.getElementById("pieorden").style.display = "none";
						items = 0;
						orden = [];
						var nodos = document.getElementById('cuerpoorden');
						var longitud = nodos.childNodes.length
						for (var i = 0; i < longitud; i++) {
							nodos.removeChild(nodos.childNodes[0]);
						}
						document.getElementById("items_orden").style.display = "none";
	//////////////////////////////////////////////////////////////////////////////////
						cdg = rsp.tit_codigo;
						monto = Math.round((rsp.monto_orden) * 100) / 100;
//						if (cdg=='00005') {
							propiedades="top=50, left=200, width=800, height=600";
							window.open("../php/formapagoenlinea.php?orden="+rsp.orden_id+"&monto="+monto,"_blank",propiedades);
//						}
	//////////////////////////////////////////////////////////////////////////////////					
					} else {
						document.getElementById("encabezado").innerHTML = "<h3>Resumen de la órden</h3>";
						document.getElementById("btn_orden").style.display = "none";
						document.getElementById("btn_online").style.display = "none";
						document.getElementById("btn_cnfrm").style.display = "none";

						document.getElementById("ordenconfirmada").style.display = "block";
						document.getElementById("cabeceraorden").style.display = "none";
						document.getElementById("cuerpoorden").style.display = "none";
						document.getElementById("pieorden").style.display = "none";
						document.getElementById('mensajeconfirmacion').innerHTML = "Ocurrió un error, comuniquese con soporte técnico al +584244071820";
					}
				}
			};
			xmlhttp.open("POST", "../php/colocaorden.php", true);
			xmlhttp.send(envdatos);
		}		
//	} else {
//		alert('Esta opción está temporalmente deshabilitada, disculpe los inconvenientes.');
//	}
} 
