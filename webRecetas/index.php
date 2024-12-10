<?php

	session_start();	
	include("vista.php");
	include("modelo.php");

	//Coger variables accion e id del menú con get 
	if(isset($_GET['accion'])) {
		$accion = $_GET['accion'];
	} else {
		if (isset($_POST['accion'])) {
			$accion = $_POST['accion'];
		} else {
			$accion = "menu";	
		}
	}


	if(isset($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		if (isset($_POST['id'])) {
			$id = $_POST['id'];
		} else {
			$id = 1;	
		}
	}


	if ($accion == "menu") {
		if ($id == 1) {
			//Mostramos el menú principal
			vmostrarmenu();
            
		}
	}

	if ($accion == "logout") {
		switch ($id) {
			case 1:
				session_unset();
				session_destroy();
				vmostrarmenu();
				break;
		}
	}
	

	if ($accion == "loginsignup") {
		switch ($id) {
			case 1:
				// Mostrar login o signup
                vmostrarloginsignup();
                break;
			case 2:
				// Validamos el login
				vresultadologin(mvalidarlogin());
				break;
            case 3:
                // Mostramos el validamiento de signup
                vresultadosignup(mvalidarsignup());
				break;
		}
	}

	if($accion == "datosusuario") {
		switch ($id) {
			case 1: 
				vmostrardatosusuario(mconsultarusuario());
				break;
		}
	}

	
	if ($accion == "bym") {
		switch ($id) {
			case 1: 
				//Mostramos modificar persona
				vmostrarmodificar(mdatosusuario());
				break;
			case 2: 
				//Validamos la modificación de persona
				vresultadomodificar(mmodificar());
				break;
			case 3: 
				//Eliminar persona
				vresultadoeliminar(meliminar());
				break;
			case 4:
				//modificar contraseña
				if(isset($_GET['idusuario'])) {
					$idusuario = $_GET['idusuario'];
				}
				vmostrarmodificarcontraseña($idusuario);
				break;
			case 5:
				//validar contraseña
				vresultadomodificarcontraseña(mmodificarcontraseña());
				break;
		}
	}

	if($accion == "forgotpsswd") {
		switch($id) {
			case 1:
				vmostrarforgotpsswd();
				break;
			case 2:
				vresultadocambiopsswd(mforgotpsswd());
				break;
		}
	}

	if($accion == "listado") {
		if(isset($_GET['tipo'])) {
			$tipo = $_GET['tipo'];
		} else {
			if (isset($_POST['tipo'])) {
				$tipo = $_POST['tipo'];
			} else {
				$tipo = "areas";	
			}
		}
		switch ($id) {
			case 1:
				vlistado(mlistado($tipo),$tipo);
				break;
		}
	
	}

	if ($accion == "listadoplatos") {
		if(isset($_GET['tipo'])) {
			$tipo = $_GET['tipo'];
		} else {
			if (isset($_POST['tipo'])) {
				$tipo = $_POST['tipo'];
			} else {
				$tipo = "areas";	
			}
		}
		switch ($id) {
			case 1:
				vlistadoplatos(mlistadoplatos($tipo));
				break;
		}
	}

	if ($accion == "listadobebidas") {
		if(isset($_GET['tipo'])) {
			$tipo = $_GET['tipo'];
		} else {
			if (isset($_POST['tipo'])) {
				$tipo = $_POST['tipo'];
			} else {
				$tipo = "categoria";	
			}
		}
		switch ($id) {
			case 1:
				vlistadobebidas(mlistadobebidas($tipo));
				break;
		}
	}

	
	if ($accion == "listadotodo"){
		if(isset($_GET['tipo'])) {
			$tipo = $_GET['tipo'];
		} else {
			if (isset($_POST['tipo'])) {
				$tipo = $_POST['tipo'];
			} else {
				$tipo = "platos";	
			}
		}
		switch($id){
			case 1:
			vlistartodo(mlistadotodo($tipo), $tipo);
			break;
		}
	}

	if ($accion == "listadotodobebidas"){
		switch($id){
			case 1:
			vlistartodasbebidas(mlistadotodasbebidas());
			break;
		}
	}

	if ($accion == "infoplato"){
		if(isset($_GET['idplato'])) {
			$idplato = $_GET['idplato'];
		} else {
			if (isset($_POST['idplato'])) {
				$idplato = $_POST['idplato'];
			} else {
				$idplato = "-1";	
			}
		}
		switch($id){
			case 1:
				if(mcomprobarsesion()){
					vinfoplato(minfoplato($idplato));
				}else{
					vmostrarmensaje("Usuario no identificado", "No puede ver la informacion", "Vuelva a intentarlo");
				}
			break;
		}
	}

	
	if ($accion == "infobebida"){
		if(isset($_GET['idbebida'])) {
			$idbebida = $_GET['idbebida'];
		} else {
			if (isset($_POST['idbebida'])) {
				$idbebida = $_POST['idbebida'];
			} else {
				$idbebida = "-1";	
			}
		}
		switch($id){
			case 1:
				if(mcomprobarsesion()){
					vinfobebida(minfobebida($idbebida));
				}else{
					vmostrarmensaje("Usuario no identificado", "No puede ver la informacion", "Vuelva a intentarlo");
				}
			break;
		}
	}

	if ($accion == "listadousuarios"){
		switch($id){
			case 1:
				vlistadousuarios(mlistadousuarios());
				break;
		}
	}

	if($accion == "bbdd"){
		if(isset($_GET['funcion'])) {
			$funcion = $_GET['funcion'];
		} else {
			if (isset($_POST['funcion'])) {
				$funcion = $_POST['funcion'];
			} else {
				$idbebida = "menu";	
			}
		}
		switch($id){
			case 1:
					vbbdd(mbbdd($funcion));
					break;
		}
	}

	if($accion == "tablas"){
		switch($id){
			case 1:
					mvaciartablas();
					break;
			case 2:
					mvaciartablas();
					mpoblartablas();
					break;
		}
	}

	if($accion == "graficos"){
		switch($id){
			case 1:
					vgraficos(mgraficos());
					break;
		}
	}

	

?>