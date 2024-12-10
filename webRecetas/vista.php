<?php

	


	function cargarVista($archivo) {
		$contenido = file_get_contents($archivo);
		//echo "<br> <br> <br>";
		//echo "hola  <br>";
		if (mcomprobarsesion()) {
			//echo "hay sesion iniciada <br>";
			if($_SESSION['usuario'] == "admin") {
				//echo "es admin <br>";
				$navbarContent = file_get_contents("navbaradmin.html");
			} else {
				//echo "no es admin: " . $_SESSION['usuario'] . " <br>";
				$navbarContent = file_get_contents("navbarsinlogin.html");
			}	
		} else {
			//echo "no hay sesion iniciada <br>";
			$navbarContent = file_get_contents("navbarconlogin.html");
		}
		$contenido = str_replace('<div id="navbar-container"></div>', $navbarContent, $contenido);


		$headContent = file_get_contents("head.html");
		$contenido = str_replace('##javascript_desplegables##', $headContent, $contenido);

		return $contenido;
	}

	function vmostrarmensaje($titulo, $mensaje1, $mensaje2) {
		$mensajeContent = cargarVista("mensaje.html");

		$mensajeContent = str_replace("##titulo##", $titulo, $mensajeContent);
		$mensajeContent = str_replace("##mensaje1##", $mensaje1, $mensajeContent);
		$mensajeContent = str_replace("##mensaje2##", $mensaje2, $mensajeContent);
		echo $mensajeContent;
	}

	function vmostrarmenu() {
		echo cargarVista("menu.html");
	}

	function vmostrardatosusuario($resultado) {
		if (is_array($resultado)) {
			$cadena = cargarVista("resultadologin.html");
			$trozos = explode("##fila##", $cadena);
			$cuerpo = "";

			$aux = $trozos[1];
			$aux = str_replace("##idusuario##", $resultado["IDUsuario"], $aux);
			$aux = str_replace("##usuario##", $resultado["usuario"], $aux);
			$aux = str_replace("##correo##", $resultado["correo"], $aux);
			$cuerpo .= $aux;
			
			echo $trozos[0] . $cuerpo . $trozos[2];
		} else {
			if($resultado == -1){ 
				// contraseña incorrecta
				vmostrarmensaje("Login incorrecto", "Contraseña incorrecta", "Vuelva a intentarlo");	
			}else if($resultado == -2){
				// usuario no encontrado
				vmostrarmensaje("Login incorrecto", "Usuario no encontrado", "Vuelva a intentarlo");	
			}
		}
	}

	function vmostrarmodificarcontraseña($id){
		$cadena = cargarVista("modificarcontraseña.html");
		$trozos = explode("##fila##", $cadena);
		$cuerpo = "";

		$aux = $trozos[1];
		$aux = str_replace("##idusuario##", $id, $aux);
		$cuerpo .= $aux;
		
		echo $trozos[0] . $cuerpo . $trozos[2];	
	}


	function vresultadomodificarcontraseña($resultado){
		if($resultado == 1){
			vmostrarmensaje("Modificación de contraseña", "Se ha cambiado correctamente la contraseña","");
		}else if ($resultado == -1){
			vmostrarmensaje("Modificación de contraseña", "Se ha producido un error, usuario desconocido", "Vuevla a intentarlo","");
		}else{
			vmostrarmensaje("Modificación de contraseña", "Se ha producido un error", "Vuevla a intentarlo","");
		}
	}


	function vmostrarloginsignup() {
		
		echo cargarVista("loginsignup.html");
	}


	function vresultadologin($resultado) {
		if (is_array($resultado)) {
			if($_SESSION['usuario'] == "admin") {
				echo cargarVista("menu.html");
			}else{
				$cadena = cargarVista("resultadologin.html");
				$trozos = explode("##fila##", $cadena);
				$cuerpo = "";

				$aux = $trozos[1];
				$aux = str_replace("##idusuario##", $resultado["IDUsuario"], $aux);
				$aux = str_replace("##usuario##", $resultado["usuario"], $aux);
				$aux = str_replace("##correo##", $resultado["correo"], $aux);
				$cuerpo .= $aux;
				
				echo $trozos[0] . $cuerpo . $trozos[2];
			}
		} else {
			if($resultado == -1){ 
				// contraseña incorrecta
				vmostrarmensaje("Login incorrecto", "Contraseña incorrecta", "Vuelva a intentarlo");	
			}else if($resultado == -2){
				// usuario no encontrado
				vmostrarmensaje("Login incorrecto", "Usuario no encontrado", "Vuelva a intentarlo");	
			} else if($resultado == -3){
				// Datos de inicio de sesión incompletos
				vmostrarmensaje("Login incorrecto", "Datos incompletos", "Vuelva a intentarlo");	
			}
		}
	}


	function vresultadosignup($resultado) {
			if($resultado == -2){
				vmostrarmensaje("Signup incorrecto", "Las contraseñas no coinciden","");
			}else if($resultado == -3){
				vmostrarmensaje("Signup incorrecto", "El nombre de usuario ya existe","");
			}else if($resultado == 1){
				vmostrarmensaje("Signup correcto", "Se ha registrado correctamente","");	
			}else if($resultado == -4){
				vmostrarmensaje("Signup incorrecto", "Error en la consulta SQL", "");	
			}
			else{
				vmostrarmensaje("Signup incorrecto", "No se ha registrado correctamente, datos de registro incompletos", "Vuelva a intentarlo");	
			}
	}

	function vmostrarforgotpsswd() {
		echo cargarVista("forgotpassword.html");
	}
	
	function vresultadocambiopsswd($resultado){
		if ($resultado === true) { // Si la modificación fue exitosa
			vmostrarmensaje("Cambio de contraseña", "Se ha cambiado correctamente", "");
		} else {
			if ($resultado === -1) { // Si el nombre de usuario ya existe
				vmostrarmensaje("Modificación de persona", "No se ha podido modificar, el usuario no existe", "Vuelva a intentarlo");
			}else if($resultado === -2) { // las contraseñas no coinciden
				vmostrarmensaje("Cambio de contraseña", "No se ha podido cambiar, las contraseñas no coinciden", "Vuelva a intentarlo");
			}else if($resultado === -3) { // el usuario no existe
				vmostrarmensaje("Cambio de contraseña", "Datos incompletos", "Vuelva a intentarlo");
			}else{
				vmostrarmensaje("Cambio de contraseña", "No se ha podido cambiar, error sql", "Vuelva a intentarlo");
			}
		}
	}

	

	function vmostrarmodificar($resultado){
		if (is_array($resultado)) {
			$cadena = cargarVista("modificar.html");
			$trozos = explode("##fila##", $cadena);
			$cuerpo = "";

			$aux = $trozos[1];
			$aux = str_replace("##idusuario##", $resultado["IDUsuario"], $aux);
			$aux = str_replace("##usuario##", $resultado["usuario"], $aux);
			$aux = str_replace("##correo##", $resultado["correo"], $aux);
			$cuerpo .= $aux;
			
			echo $trozos[0] . $cuerpo . $trozos[2];
		} else {
			vmostrarmensaje("Modificación de persona", "Se ha producido un error", "Vuevla a intentarlo");
		}
	}


	function vresultadomodificar($resultado) {
		if ($resultado === true) { // Si la modificación fue exitosa
			vmostrarmensaje("Modificación de persona", "Se ha modificado correctamente", "");
		} else {
			if ($resultado === -1) { // Si el nombre de usuario ya existe
				vmostrarmensaje("Modificación de persona", "No se ha podido modificar, el nombre de usuario ya existe", "Vuelva a intentarlo");
			} else if ($resultado === 0) { // Si no se realizaron cambios
				vmostrarmensaje("Modificación de persona", "No se ha modificado nada", "Vuelva a intentarlo");
			} else { // Si ocurrió un error SQL u otro error
				vmostrarmensaje("Modificación de persona", "No se ha podido modificar, error SQL", "Vuelva a intentarlo");
			}
		}
	}

	function vresultadoeliminar($resultado) {
		if ($resultado) {
			vmostrarmensaje("Eliminación de persona", "Se ha eliminado correctamente", "");
		} else {
			vmostrarmensaje("Eliminación de persona", "No se ha podido eliminar", "Vuelva a intentarlo");
		}
	}

	function vlistadoplatos ($resultado) {
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Listado de platos", "Se ha producido un error", "Contacta con el administrador");
		} else {
			if(mcomprobarsesion()){
				$cadena = cargarVista("plantillaplatos.html");
			}
			else{
				$cadena = cargarVista("plantillaplatosNOLOGIN.html");
			}
            $trozos = explode("##fila##", $cadena);
            $cuerpo = "";

            while ($datos = $resultado->fetch_assoc()) {

                $aux = $trozos[1];
                $aux = str_replace("##idplato##", $datos["id_plato"], $aux);
                $aux = str_replace("##nombre##", $datos["nombre_plato"], $aux);
                $cuerpo .= $aux;
            }
            echo $trozos[0] . $cuerpo . $trozos[2];
			
        }
	}

	function vlistadobebidas ($resultado) {
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Listado de bebidas", "Se ha producido un error", "Contacta con el administrador");
		} else {
			if(mcomprobarsesion()){
				$cadena = cargarVista("plantillabebidas.html");
			}
			else{
				$cadena = cargarVista("plantillabebidasNOLOGIN.html");
			}
            $trozos = explode("##fila##", $cadena);
            $cuerpo = "";

            while ($datos = $resultado->fetch_assoc()) {

                $aux = $trozos[1];
                $aux = str_replace("##idbebida##", $datos["id_bebida"], $aux);
                $aux = str_replace("##nombre##", $datos["nombre_bebida"], $aux);
				//$aux = str_replace("##foto##", $datos["foto_bebida"], $aux);
                $cuerpo .= $aux;
            }
            echo $trozos[0] . $cuerpo . $trozos[2];
			
        }
	}


	function vlistartodo($resultado, $tipo){
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Listado de todo", "Se ha producido un error", "Contacta con el administrador");
		} else {
			
			$cadena = cargarVista("listadotodos.html");
			$trozos = explode("##fila##", $cadena);
			$cuerpo = "";
			
			if($tipo == "platos"){
				//Mostrar el listado de platos				
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##id##", $datos["id_plato"], $aux);
					$aux = str_replace("##nombre##", $datos["nombre_plato"], $aux);
					$aux = str_replace("##foto##", $datos["imagen_plato"], $aux);
					$aux = str_replace("##tipo##", "platos", $aux);
					$cuerpo .= $aux;
				}
			}else{
				//Mostrar el listado de bebidas
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##id##", $datos["id_bebida"], $aux);
					$aux = str_replace("##nombre##", $datos["nombre_bebida"], $aux);
					$aux = str_replace("##foto##", $datos["foto_bebida"], $aux);
					$aux = str_replace("##tipo##", "bebidas", $aux);

					$cuerpo .= $aux;
				}
			}
			
			echo $trozos[0] . $cuerpo . $trozos[2];
		}
	}

	function vlistado($resultado, $tipo) {
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Listado", "Se ha producido un error", "Contacta con el administrador");
		} else {

			//Mostrar el listado dependiendo del filtro
			if($tipo == "areas"){
				$cadena = cargarVista("listadoareas.html");
				$trozos = explode("##fila##", $cadena);

				$cuerpo = "";
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];

					$primera = strtolower($datos["nombre_area_plato"]);
					//$area = ucfirst($primera);
					//$aux = str_replace("##area##", $datos["nombre_area_plato"], $aux);
					$aux = str_replace("##area##", $primera, $aux);
					$cuerpo .= $aux;
				}
			}
			else if($tipo == "categorias"){
				$cadena = cargarVista("listadocategorias.html");
				$trozos = explode("##fila##", $cadena);
	
				$cuerpo = "";
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##idcategoria##", $datos["id_categoria_plato"], $aux);
					$aux = str_replace("##nombre##", $datos["nombre_categoria_plato"], $aux);
					$aux = str_replace("##foto##", $datos["foto_categoria_plato"], $aux);
					$cuerpo .= $aux;
				}
			}
			else if($tipo == "categoriasbebidas"){
				$cadena = cargarVista("listadocategoriasbebidas.html");
				$trozos = explode("##fila##", $cadena);
	
				$cuerpo = "";
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##nombre##", $datos["nombre_categoria_bebida"], $aux);
					$cuerpo .= $aux;
				}
			}
			else if($tipo == "alcoholicas"){
				$cadena = cargarVista("listadobebidasalcoholicas.html");
				$trozos = explode("##fila##", $cadena);
	
				$cuerpo = "";
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##idbebida##", $datos["id_bebida"], $aux);
					$aux = str_replace("##nombre##", $datos["nombre_bebida"], $aux);
					$cuerpo .= $aux;
				}
			}
			
			else if($tipo == "noalcoholicas"){
				$cadena = cargarVista("listadobebidasnoalcoholicas.html");
				$trozos = explode("##fila##", $cadena);
	
				$cuerpo = "";
				while ($datos = $resultado->fetch_assoc()) {
					$aux = $trozos[1];
					$aux = str_replace("##idbebida##", $datos["id_bebida"], $aux);
					$aux = str_replace("##nombre##", $datos["nombre_bebida"], $aux);
					$cuerpo .= $aux;
				}
			}
			echo $trozos[0] . $cuerpo . $trozos[2];
		}
	}

	function vinfoplato($resultado) {
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Info plato", "Se ha producido un error", "Contacta con el administrador");
		} else {

			//solo el logueado puede verlo por lo que muestro sin login
			$cadena = cargarVista("infoplato.html");
			$trozos = explode("##fila##", $cadena);

			$cuerpo = "";
			//echo "<br> <br> <br>";
			$datos = $resultado->fetch_assoc();
			if($datos) {
				$aux = $trozos[1];
				$aux = str_replace("##nombre##", $datos["nombre_plato"], $aux);
				$aux = str_replace("##foto##", $datos["imagen_plato"], $aux);
				$aux = str_replace("##area##", $datos["area_plato"], $aux);
				$aux = str_replace("##categoria##", $datos["categoria_plato"], $aux);
				$aux = str_replace("##instrucciones##", $datos["instrucciones_plato"], $aux);
				$aux = str_replace("##video##", $datos["video_plato"], $aux);
				$cuerpo .= $aux;
			
				echo $trozos[0] . $cuerpo . $trozos[2];
			}else{
				vmostrarmensaje("Info plato", "No se encontraron datos para el plato especificado", "Vuelva a intentarlo");
			}

		}
	}

	
	function vinfobebida($resultado) {
		if (!is_object($resultado)) {
			//Tenemos un error. Mostramos un mensaje
			vmostrarmensaje("Info bebida", "Se ha producido un error", "Contacta con el administrador");
		} else {
			
			//solo el logueado puede verlo por lo que muestro sin login
			$cadena = cargarVista("infobebida.html");
			$trozos = explode("##fila##", $cadena);

			$cuerpo = "";
			while ($datos = $resultado->fetch_assoc()) {
				$aux = $trozos[1];
				$aux = str_replace("##nombre##", $datos["nombre_bebida"], $aux);
				$aux = str_replace("##foto##", $datos["foto_bebida"], $aux);
				$aux = str_replace("##categoria##", $datos["categoria_bebida"], $aux);
				$aux = str_replace("##instrucciones##", $datos["instruccion_ing_bebida"], $aux);

				$cuerpo .= $aux;
			}
			echo $trozos[0] . $cuerpo . $trozos[2];
		}
	}

	function vlistadousuarios($resultado){
		$cadena = cargarVista("listadousuarios.html");
		$trozos = explode("##fila##", $cadena);

		$cuerpo = "";
		while ($datos = $resultado->fetch_assoc()) {
			$aux = $trozos[1];
			$aux = str_replace("##idusuario##", $datos["IDUsuario"], $aux);
			$aux = str_replace("##usuario##", $datos["usuario"], $aux);
			$aux = str_replace("##correo##", $datos["correo"], $aux);
			$cuerpo .= $aux;
		}
		echo $trozos[0] . $cuerpo . $trozos[2];
	}

	function vbbdd($resultado){
		if(!is_array($resultado)){
			vmostrarmensaje("BBDD", "Se ha producido un error", "Contacta con el administrador");
		}else{
			$cadena = cargarVista("menubbdd.html");
			$trozos = explode("##fila##", $cadena);
			$cuerpo = "";
			foreach ($resultado as $tabla) {
				$aux = $trozos[1];
				$aux = str_replace("##nombre##", $tabla["nombre"], $aux);
				$aux = str_replace("##entradas##", $tabla["entradas"], $aux);
				$cuerpo .= $aux;
			}
	
			echo $trozos[0] . $cuerpo . $trozos[2];
		}
	}

	function vgraficos($resultado){
		if(is_array($resultado)){
			$resultado_platos_categorias = $resultado[0];
			$categorias = $resultado_platos_categorias[0];
			$total_platos = $resultado_platos_categorias[1];
			$resultado_platos_areas = $resultado[1];
			$areas = $resultado_platos_areas[0];
			$total_platos_areas = $resultado_platos_areas[1];
			$resultado_bebidas_categorias = $resultado[2];
			$categorias_bebida = $resultado_bebidas_categorias[0];
			$total_bebidas = $resultado_bebidas_categorias[1];

			 // Crear un array con los datos
			 $data = array(
				"success" => true,
				"categorias" => $categorias,
				"totalPlatos" => $total_platos,
				"areas" => $areas,
				"totalPlatosAreas" => $total_platos_areas,
				"categoriasBebida" => $categorias_bebida,
				"totalBebidas" => $total_bebidas
			);

			$data_json = json_encode($data);

			echo '<script>const data = ' . $data_json . ';</script>';
			 // Incluir el JSON en el HTML
			 $html = cargarVista("graficos.html");
	 
			 // Mostrar el HTML
			 echo $html;
		} else {
			vmostrarmensaje("Graficos", "Se ha producido un error", "Contacta con el administrador");
		}
	}

?>





