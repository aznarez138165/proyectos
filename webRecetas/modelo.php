<?php
	
	include "basededatos.php";

	//LOGIN
	function mvalidarlogin() {
		if(isset($_POST['usuario']) && isset($_POST['password'])) {
			$con = conexion();
			$usr = $con->real_escape_string($_POST['usuario']);
			$pswd =$con->real_escape_string($_POST['password']);
			$pswd = md5($pswd);
			$consulta = "SELECT * FROM final_usuarios WHERE usuario = '$usr'";
			$resultado = $con->query($consulta);
			if($resultado->num_rows == 1) {
				$datos = $resultado->fetch_assoc();
				if ($datos['password'] == $pswd) {
					$_SESSION['tiempo'] = time();
					$_SESSION['usuario'] = $usr;
					return $datos;
				} else {
					return -1; // Contraseña incorrecta
				}
			} else {
				return -2; // Usuario no encontrado
			}
		} else {
			return -3; // Datos de inicio de sesión incompletos
		}
	
	}

	//SIGNUP
	function mvalidarsignup() {
		if(isset($_POST['usuario']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2'])) {
			$con = conexion();
			$usr = $con->real_escape_string($_POST['usuario']);
			$correo = $con->real_escape_string($_POST['email']);
			$pswd = $_POST['password'];
			$pswd2 = $_POST['password2'];
			
			// Comprobar si ya existe el usuario
			$consulta = "SELECT * FROM final_usuarios WHERE usuario = '$usr'";
			$resultado = $con->query($consulta);
			if ($resultado->num_rows > 0) {
				return -3; // Usuario ya existe
			} else {
				if ($pswd == $pswd2) {
					$pswd = md5($pswd);
					$consulta = "INSERT INTO final_usuarios (usuario, correo, password) VALUES ('$usr','$correo','$pswd')";
					if ($con->query($consulta)) {
						$_SESSION['tiempo'] = time();
						$_SESSION["usuario"] = $usr;
						return 1; // Registro exitoso
					} else {
						return -4; // Error en la consulta SQL
					}
				} else {
					return -2; // Contraseñas no coinciden
				}
			}
		} else {
			return -1; // Datos de registro incompletos
		}
	
	}	
	
	function mforgotpsswd(){
		$con = conexion();
		if(isset($_POST['usuario']) && isset($_POST['password']) && isset($_POST['password2'])) {
			$usr = $_POST['usuario'];
			$pswd = $_POST['password'];
			$pswd2 = $_POST['password2'];
			$consulta1 = "SELECT * FROM final_usuarios WHERE usuario = '$usr'";
			$resultado1 = $con->query($consulta1);
			if($resultado1->num_rows == 1) {
				if($pswd == $pswd2){
					$pswd = md5($pswd);
					$consulta = "UPDATE final_usuarios SET password = '$pswd' WHERE usuario = '$usr'";
					$resultado = $con->query($consulta);
					return $resultado;
				}else{
					return -2; //las contraseñas no coinciden
				}
			}else{
				return -1; //el usuario no existe
			}
		}else{
			return -3; //datos incommpletos
		}
	}
	
	//ELIMINAR
	function meliminar() {
		$con = conexion();
		$id = $_GET['idusuario'];
		$consulta = "DELETE FROM final_usuarios WHERE IDUsuario = '$id'";
		if($_SESSION['usuario'] != "admin") {
			session_unset();
			session_destroy();
		}
		return $con->query($consulta);
	}

	//MODIFICAR
	function mmodificar() {
		$con = conexion();
		$id = $_POST['idusuario'];
		$usr = $con->real_escape_string($_POST['usuario']);
		$correo = $con->real_escape_string($_POST['correo']);

		//Comparo los campos son los iniciales para ver cuales ha cambiado algo
		$consulta = "SELECT * FROM final_usuarios WHERE IDUsuario = '$id'";
		$resultado = $con->query($consulta);
		$datos = $resultado->fetch_assoc();
		if ($usr == $datos['usuario'] && $correo == $datos['correo']) {
			return 0; // No ha cambiado nada
		}	

		// Si ha cambiado el usuario comprobar que no haya otro con ese usuario
		if ($usr != $datos['usuario']) {
			$consulta = "SELECT * FROM final_usuarios WHERE usuario = '$usr'";
			$resultado = $con->query($consulta);
			if ($resultado->num_rows > 0) {
				return -1; // Usuario ya existe
			}	
			
			$consulta = "UPDATE final_usuarios SET usuario = '$usr', correo = '$correo' WHERE IDUsuario = '$id'";

			//si es el admin el que modifica no actualizo la sesion
			if($_SESSION['usuario'] != "admin"){
				$_SESSION['usuario'] = $usr;
			}

			
		}else if($correo != $datos['correo']){
			$consulta = "UPDATE final_usuarios SET correo = '$correo' WHERE IDUsuario = '$id'";
			
		}

		$resultado = $con->query($consulta);
			return $resultado;
	
	}

	function mmodificarcontraseña(){
		$con = conexion();
		if(isset($_POST['idusuario'])) {
			$id = $_POST['idusuario'];
		
		}else{
			return -1;
		}
		
		$pswd = $con->real_escape_string($_POST['password1']);
		$pswd2 = $con->real_escape_string($_POST['password2']);
		if($pswd == $pswd2){
			$pswd = md5($pswd);
			$consulta = "UPDATE final_usuarios SET password = '$pswd' WHERE IDUsuario = '$id'";
			$resultado = $con->query($consulta);
			return $resultado;
		}else{
			return -2; //las contraseñas no coinciden
		}
	}

	function mdatosusuario(){
		if(isset($_GET['idusuario'])) {
			$con = conexion();
			$id = $_GET['idusuario'];
			$consulta = "SELECT * FROM final_usuarios WHERE IDUsuario = '$id'";
			$resultado = $con->query($consulta);
			$datos = $resultado->fetch_assoc();
			return $datos;
		}else{
			return -1;
		}
	}
	
	function mconsultarusuario(){
		if (isset($_SESSION['usuario'])) {
			$usr = $_SESSION['usuario'];
			$con = conexion();
			$consulta = "SELECT * FROM final_usuarios WHERE usuario = '$usr'";
			$resultado = $con->query($consulta);
			if($resultado->num_rows == 1) {
				$datos = $resultado->fetch_assoc();
				return $datos;
			}else{
				return -1;
			}
		} else{
			return -2;
		}
	}
	
	function mcomprobarsesion(){
		if (isset($_SESSION['usuario']) && mverificarTiempoSesion() == true) {
			return true;
		} else{
			return false;
		}
	}

	function mverificarTiempoSesion() {
		// Tiempo de expiración de la sesión (en segundos)
		$tiempoExpiracion = 300; // 5 minutos
	
		// Verificar si existe una sesión y si ha pasado el tiempo de expiración
		if (isset($_SESSION['tiempo']) && (time() - $_SESSION['tiempo'] > $tiempoExpiracion)) {
			// Si ha pasado el tiempo de expiración, destruir la sesión
			session_unset();
			session_destroy();
			return false; // Sesión expirada
		}
	
		// Actualizar el tiempo de la sesión
		$_SESSION['tiempo'] = time();
		return true; // Sesión válida
	}


	function mlistadoplatos($tipo) {
		$con = conexion();
		if($tipo == "area"){
			$area = $_GET['nombreArea']; 
			$consulta = "SELECT * FROM final_platos WHERE area_plato = ? ORDER BY nombre_plato";
			$consulta_preparada = $con->prepare($consulta);
			$consulta_preparada->bind_param("s", $area);
			$consulta_preparada->execute();
			$resultado = $consulta_preparada->get_result();
		}else if($tipo == "categoria"){
			$categoria = $_GET['idcategoria']; 
			$consulta = "SELECT * FROM final_platos WHERE categoria_plato = (SELECT nombre_categoria_plato FROM final_platos_categorias WHERE id_categoria_plato = ?)";
			$consulta_preparada = $con->prepare($consulta);
			$consulta_preparada->bind_param("i", $categoria);
			$consulta_preparada->execute();
			$resultado = $consulta_preparada->get_result();
		}

		if ($resultado) {
			return $resultado;
		} else {
			return -1;
		}		
	}


	function mlistadobebidas($tipo) {
		$con = conexion();
		if($tipo == "categoria"){
			$categoria = $_GET['nombreCategoria']; 
			$consulta = "SELECT * FROM final_bebida WHERE categoria_bebida = ? ORDER BY nombre_bebida";
			$consulta_preparada = $con->prepare($consulta);
			$consulta_preparada->bind_param("s", $categoria);
			$consulta_preparada->execute();
			$resultado = $consulta_preparada->get_result();
		}else if($tipo == "alcoholica"){
			$categoria = $_GET['idcategoria']; 
			$consulta = "SELECT * FROM final_platos WHERE categoria_plato = (SELECT nombre_categoria_plato FROM final_platos_categorias WHERE id_categoria_plato = ?)";
			$consulta_preparada = $con->prepare($consulta);
			$consulta_preparada->bind_param("i", $categoria);
			$consulta_preparada->execute();
			$resultado = $consulta_preparada->get_result();
		}

		if ($resultado) {
			return $resultado;
		} else {
			return -1;
		}		
	}


	function mlistadotodo($tipo){
		$con = conexion();
		if($tipo == "platos"){
			$consulta = "SELECT id_plato,nombre_plato, imagen_plato FROM final_platos ORDER BY nombre_plato";
		}else if($tipo == "bebidas"){
			$consulta = "SELECT id_bebida,nombre_bebida, foto_bebida FROM final_bebida ORDER BY nombre_bebida";
		}
		if ($resultado = $con->query($consulta)) {
			return $resultado;
		} else {
			return -1;
		}
	}

	function mlistado($tipo){
		$con = conexion();
		if($tipo == "areas"){
			$consulta = "SELECT * FROM final_platos_areas ORDER BY nombre_area_plato";
		}else if($tipo == "categorias"){
			$consulta = "SELECT * FROM final_platos_categorias ORDER BY nombre_categoria_plato";
		}else if($tipo == "categoriasbebidas"){
			$consulta = "SELECT * FROM final_bebida_categorias ORDER BY nombre_categoria_bebida";
		}else if($tipo == "alcoholicas"){
			$consulta = "SELECT * FROM final_bebida WHERE alcoholica_bebida IS NOT NULL ORDER BY nombre_bebida";
		}else if($tipo == "noalcoholicas"){
			$consulta = "SELECT * FROM final_bebida WHERE alcoholica_bebida IS NULL ORDER BY nombre_bebida";
		}
		
		if ($resultado = $con->query($consulta)) {
			return $resultado;
		} else {
			return -1;
		}	
	}

	function minfoplato($idplato){
		$con = conexion();
		
		if($idplato == "-1"){
			return -1;
		}

		$consulta = "SELECT * FROM final_platos WHERE id_plato = ?";
		$consulta_preparada = $con->prepare($consulta);
		$consulta_preparada->bind_param("i", $idplato);
		$consulta_preparada->execute();
		$resultado = $consulta_preparada->get_result();
		if ($resultado && $resultado->num_rows > 0) {
			return $resultado;
		} else {
			return -1;
		}
	}

	function mlistadousuarios(){
		$con = conexion();
		$consulta = "SELECT * FROM final_usuarios";
		$resultado = $con->query($consulta);
		if ($resultado) {
			return $resultado;
		} else {
			return -1;
		}
	}

	

	function minfobebida($idbebida){
		$con = conexion();
		if($idbebida == "-1"){
			return -1;
		}

		$consulta = "SELECT * FROM final_bebida WHERE id_bebida = ?";
		$consulta_preparada = $con->prepare($consulta);
		$consulta_preparada->bind_param("i", $idbebida);
		$consulta_preparada->execute();
		$resultado = $consulta_preparada->get_result();
		if ($resultado) {
			return $resultado;
		} else {
			return -1;
		}
	}

	function mbbdd($funcion){
		$con = conexion();
		$database = "db_grupo23"; //es el nombre de la variable $bd en basededatos.php
		if($funcion == "menu"){
			$consulta = "SHOW TABLES FROM $database";
			$resultado = $con->query($consulta);
			$tables = array();
			while ($row = $resultado->fetch_row()) {
				$table = $row[0];
				$count_query = "SELECT count(*) FROM $table";
				$count_result = $con->query($count_query);
				$count = $count_result->fetch_row()[0];
				$tables[] = array("nombre" => $table, "entradas" => $count);
			}
			return $tables;
		}

		if ($resultado) {
			return $resultado;
		} else {
			return -1;
		}
	}

	function mvaciartablas(){
		$con = conexion();
		$database = "prueba"; 
		$consulta = "SHOW TABLES FROM $database";
		$resultado = $con->query($consulta);
		$tables = array();
		while ($row = $resultado->fetch_row()) {
			$table = $row[0];
			$tables[] = $table;
		}
	  	 
		//tengo que desactivar las foreign keys para que no me de error
		$query = "SET FOREIGN_KEY_CHECKS = 0";
		mysqli_query($con, $query);

		$borrada = true;

		// Iterar sobre cada tabla y ejecutar TRUNCATE
	  	foreach ($tables as $tabla) {
			if($tabla != "final_usuarios") {
				//echo $tabla . "<br>";
				$query = "TRUNCATE TABLE $tabla";
				mysqli_query($con, $query);
				// Verificar si ocurrió algún error al vaciar la tabla
				if (mysqli_error($con)) {
					//echo "Error al vaciar la tabla: " . mysqli_error($con);
					$borrada = $borrada && false;
				} else {
					//echo "Tabla vaciada correctamente.<br>";
					$borrada = $borrada && true;
				}
			}else{
				//echo "TABLA USUARIOS NO BORRAR<br>";
			}
	  	}

		//vuelvo a activar las foreign keys
		$query = "SET FOREIGN_KEY_CHECKS = 1";
		mysqli_query($con, $query);

		if($borrada){
			$response = array("success" => true); // O false si hubo un error
			header('Content-Type: application/json');
		}else{
			$response = array("success" => false); 
			header('Content-Type: application/json');
		}
		echo json_encode($response);
	}
	
	function mpoblartablas(){
		$con = conexion();
		$database = "prueba"; 
		$consulta = "SHOW TABLES FROM $database";
		$resultado = $con->query($consulta);
		$tables = array();
		while ($row = $resultado->fetch_row()) {
			$table = $row[0];
			$tables[] = $table;
		}
	  	 
		// Iterar sobre cada tabla y ejecutar TRUNCATE
		foreach ($tables as $tabla) {
			switch ($tabla) {
				case 'final_usuarios':
					//no hacer nada
					break;

				case 'final_bebida':
					$base_url = "https://www.thecocktaildb.com/api/json/v1/1/search.php?f=";

					for($ascii = ord('a'); $ascii <= ord('z'); $ascii++){
						$letra = chr($ascii);
						$url = $base_url . $letra;

						$json_data = file_get_contents($url);
						$datos = json_decode($json_data,true);

						if($datos['drinks'] != null){
							for($i = 0; $i < sizeof($datos['drinks']); $i++){
								if($datos != null){
									if($datos['drinks'][$i]['strIngredient1'] != NULL){
										$ingrediente1SinCortar = $datos['drinks'][$i]['strIngredient1'];
										$array_resultante = explode(",", $ingrediente1SinCortar);
										$ingrediente1Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente1Cortado = $datos['drinks'][$i]['strIngredient1'];
									}
									$ingredienteAux = $ingrediente1Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente1Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient2'] != NULL){
										$ingrediente2SinCortar = $datos['drinks'][$i]['strIngredient2'];
										$array_resultante = explode(",", $ingrediente2SinCortar);
										$ingrediente2Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente2Cortado = $datos['drinks'][$i]['strIngredient2'];
									}
									$ingredienteAux = $ingrediente2Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente2Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient3'] != NULL){
										$ingrediente3SinCortar = $datos['drinks'][$i]['strIngredient3'];
										$array_resultante = explode(",", $ingrediente3SinCortar);
										$ingrediente3Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente3Cortado = $datos['drinks'][$i]['strIngredient3'];
									}
									$ingredienteAux = $ingrediente3Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente3Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient4'] != NULL){
										$ingrediente4SinCortar = $datos['drinks'][$i]['strIngredient4'];
										$array_resultante = explode(",", $ingrediente4SinCortar);
										$ingrediente4Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente4Cortado = $datos['drinks'][$i]['strIngredient4'];
									}
									$ingredienteAux = $ingrediente4Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente4Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient5'] != NULL){
										$ingrediente5SinCortar = $datos['drinks'][$i]['strIngredient5'];
										$array_resultante = explode(",", $ingrediente5SinCortar);
										$ingrediente5Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente5Cortado = $datos['drinks'][$i]['strIngredient5'];
									}
									$ingredienteAux = $ingrediente5Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente5Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient6'] != NULL){
										$ingrediente6SinCortar = $datos['drinks'][$i]['strIngredient6'];
										$array_resultante = explode(",", $ingrediente6SinCortar);
										$ingrediente6Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente6Cortado = $datos['drinks'][$i]['strIngredient6'];
									}
									$ingredienteAux = $ingrediente6Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";;
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente6Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient7'] != NULL){
										$ingrediente7SinCortar = $datos['drinks'][$i]['strIngredient7'];
										$array_resultante = explode(",", $ingrediente7SinCortar);
										$ingrediente7Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente7Cortado = $datos['drinks'][$i]['strIngredient7'];
									}
									$ingredienteAux = $ingrediente7Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente7Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient8'] != NULL){
										$ingrediente8SinCortar = $datos['drinks'][$i]['strIngredient8'];
										$array_resultante = explode(",", $ingrediente8SinCortar);
										$ingrediente8Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente8Cortado = $datos['drinks'][$i]['strIngredient8'];
									}
									$ingredienteAux = $ingrediente8Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente8Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient9'] != NULL){
										$ingrediente9SinCortar = $datos['drinks'][$i]['strIngredient9'];
										$array_resultante = explode(",", $ingrediente9SinCortar);
										$ingrediente9Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente9Cortado = $datos['drinks'][$i]['strIngredient9'];
									}
									$ingredienteAux = $ingrediente9Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente9Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['drinks'][$i]['strIngredient10'] != NULL){
										$ingrediente10SinCortar = $datos['drinks'][$i]['strIngredient10'];
										$array_resultante = explode(",", $ingrediente10SinCortar);
										$ingrediente10Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente10Cortado = $datos['drinks'][$i]['strIngredient10'];
									}
									$ingredienteAux = $ingrediente10Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_bebida_ingredientes WHERE
									nombre_ingrediente_bebida = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_bebida) AS max_id FROM final_bebida_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente10Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida, descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida,abv_ingrediente_bebida) VALUES ($nuevo_id, '$nombre', '$descripcion', null, null, null)";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}
									$bebida = array (
											"id" => $datos['drinks'][$i]['idDrink'],
											"nombre" => $datos['drinks'][$i]['strDrink'],
											"categoria" => $datos['drinks'][$i]['strCategory'],
											"iba" => $datos['drinks'][$i]['strIBA'],
											"instruccionesING" => $datos['drinks'][$i]['strInstructions'],
											"instruccionesES" => $datos['drinks'][$i]['strInstructionsES'],
											"instruccionesDE" => $datos['drinks'][$i]['strInstructionsDE'],
											"instruccionesFR" => $datos['drinks'][$i]['strInstructionsFR'],
											"instruccionesIT" => $datos['drinks'][$i]['strInstructionsIT'],
											"imagen" => $datos['drinks'][$i]['strDrinkThumb'],
											"ingrediente1" => $datos['drinks'][$i]['strIngredient1'],
											"ingrediente2" => $datos['drinks'][$i]['strIngredient2'],
											"ingrediente3" => $datos['drinks'][$i]['strIngredient3'],
											"ingrediente4" => $datos['drinks'][$i]['strIngredient4'],
											"ingrediente5" => $datos['drinks'][$i]['strIngredient5'],
											"ingrediente6" => $datos['drinks'][$i]['strIngredient6'],
											"ingrediente7" => $datos['drinks'][$i]['strIngredient7'],
											"ingrediente8" => $datos['drinks'][$i]['strIngredient8'],
											"ingrediente9" => $datos['drinks'][$i]['strIngredient9'],
											"ingrediente10" => $datos['drinks'][$i]['strIngredient10'],
											"cantidad1" => $datos['drinks'][$i]['strMeasure1'],
											"cantidad2" => $datos['drinks'][$i]['strMeasure2'],
											"cantidad3" => $datos['drinks'][$i]['strMeasure3'],
											"cantidad4" => $datos['drinks'][$i]['strMeasure4'],
											"cantidad5" => $datos['drinks'][$i]['strMeasure5'],
											"cantidad6" => $datos['drinks'][$i]['strMeasure6'],
											"cantidad7" => $datos['drinks'][$i]['strMeasure7'],
											"cantidad8" => $datos['drinks'][$i]['strMeasure8'],
											"cantidad9" => $datos['drinks'][$i]['strMeasure9'],
											"cantidad10" => $datos['drinks'][$i]['strMeasure10']
									);
									$stmt = $con->prepare("insert into final_bebida (id_bebida, nombre_bebida,
											categoria_bebida, alcoholica_bebida, vaso_bebida, instruccion_ing_bebida, instruccion_es_bebida, instruccion_de_bebida, instruccion_fr_bebida, instruccion_it_bebida, foto_bebida, ingrediente1_bebida, ingrediente2_bebida, ingrediente3_bebida, ingrediente4_bebida, ingrediente5_bebida, ingrediente6_bebida, ingrediente7_bebida, ingrediente8_bebida, ingrediente9_bebida, ingrediente10_bebida, cantidad1_bebida, cantidad2_bebida, cantidad3_bebida, cantidad4_bebida, cantidad5_bebida, cantidad6_bebida, cantidad7_bebida, cantidad8_bebida, cantidad9_bebida, cantidad10_bebida)
											values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
									$stmt->bind_param("sssssssssssssssssssssssssssssss",$bebida['id'],$bebida['nombre'],$bebida['categoria'],$bebida['iba'],$bebida['alcoholico'],$bebida['instruccionesING'],$bebida['instruccionesES'],$bebida['instruccionesDE'],$bebida['instruccionesFR'],$bebida['instruccionesIT'],$bebida['imagen'],$bebida['ingrediente1'],$bebida['ingrediente2'],$bebida['ingrediente3'],$bebida['ingrediente4'],$bebida['ingrediente5'],$bebida['ingrediente6'],$bebida['ingrediente7'],$bebida['ingrediente8'],$bebida['ingrediente9'],$bebida['ingrediente10'],$bebida['cantidad1'],$bebida['cantidad2'],$bebida['cantidad3'],$bebida['cantidad4'],$bebida['cantidad5'],$bebida['cantidad6'],$bebida['cantidad7'],$bebida['cantidad8'],$bebida['cantidad9'],$bebida['cantidad10']);
									$stmt->execute();
								}
								else{
									//echo "Error en la lectura";
								}
							}
						}
					}
					break;

				case 'bebida_ingredientes':
					$base_url = "https://www.thecocktaildb.com/api/json/v1/1/list.php?i=list";
					$json_data = file_get_contents($base_url);
					$datos = json_decode($json_data,true);

					$ingrediente_url = "https://www.thecocktaildb.com/api/json/v1/1/search.php?i=";

					if($datos['drinks'] != null){
						for($i = 0; $i < sizeof($datos['drinks']); $i++){
							$nombre_ingrediente = $datos['drinks'][$i]['strIngredient1'];
							$url = $ingrediente_url . $nombre_ingrediente;
							$json_data_ing = file_get_contents($url);	
							$datos_ingredientes = json_decode($json_data_ing,true);
							if($datos_ingredientes['ingredients'] != null){
								$ingrediente = array (
									"idIngrediente" => $datos_ingredientes['ingredients'][0]['idIngredient'],
									"nombreIngrediente" => $datos_ingredientes['ingredients'][0]['strIngredient'], 
									"descripcionIngrediente" => $datos_ingredientes['ingredients'][0]['strDescription'],
									"tipoIngrediente" => $datos_ingredientes['ingredients'][0]['strType'],
									"alcoholico" => $datos_ingredientes['ingredients'][0]['strAlcohol'],
									"ABV" => $datos_ingredientes['ingredients'][0]['strABV']
								);
								$stmt = $con->prepare("insert into final_bebida_ingredientes (id_ingrediente_bebida, nombre_ingrediente_bebida,
									descripcion_ingrediente_bebida, tipo_ingrediente_bebida, alcoholica_ingrediente_bebida, abv_ingrediente_bebida)
									values (?,?,?,?,?,?)");
								$stmt->bind_param("ssssss",$ingredienteBebida['idIngrediente'],$ingredienteBebida['nombreIngrediente'],$ingredienteBebida['descripcionIngrediente'],$ingredienteBebida['tipoIngrediente'],$ingredienteBebida['alcoholico'],$ingredienteBebida['ABV']);
								$stmt->execute();
							}
						}
					}else{
						//echo "Error en la lectura <br>";
					}
					break;

				case 'bebida_categorias':
					$url = "https://www.thecocktaildb.com/api/json/v1/1/list.php?c=list";
					$json_data = file_get_contents($url);
					$datos = json_decode($json_data,true);
					if($datos['drinks'] != null){
						for($i = 0; $i < sizeof($datos['drinks']); $i++){
							if($datos != null){
								$categoria = array("nombreCategoriaBebida" => $datos['drinks'][$i]['strCategory']);
								$stmt = $con->prepare("INSERT INTO final_bebida_categorias (nombre_categoria_bebida) VALUES (?)");
								$stmt->bind_param("s",$categoria['nombreCategoriaBebida']);
								$stmt->execute();
							}
							else{
								//echo "Error en la lectura <br>";
							}
						}
					}
					break;

				case 'platos':
					$base_url = "https://www.themealdb.com/api/json/v1/1/search.php?f=";

					for($ascii = ord('a'); $ascii <= ord('z'); $ascii++){
						$letra = chr($ascii);
						$url = $base_url . $letra;

						$json_data = file_get_contents($url);
						$datos = json_decode($json_data,true);

						if($datos['meals'] != null){
							for($i = 0; $i < sizeof($datos['meals']); $i++){
								if($datos != null){

									if($datos['meals'][$i]['strIngredient1'] != NULL){
										$ingrediente1SinCortar = $datos['meals'][$i]['strIngredient1'];
										$array_resultante = explode(",", $ingrediente1SinCortar);
										$ingrediente1Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente1Cortado = $datos['meals'][$i]['strIngredient1'];
									}
									$ingredienteAux = $ingrediente1Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente1Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient2'] != NULL){
										$ingrediente2SinCortar = $datos['meals'][$i]['strIngredient2'];
										$array_resultante = explode(",", $ingrediente2SinCortar);
										$ingrediente2Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente2Cortado = $datos['meals'][$i]['strIngredient2'];
									}
									$ingredienteAux = $ingrediente2Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente2Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient3'] != NULL){
										$ingrediente3SinCortar = $datos['meals'][$i]['strIngredient3'];
										$array_resultante = explode(",", $ingrediente3SinCortar);
										$ingrediente3Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente3Cortado = $datos['meals'][$i]['strIngredient3'];
									}
									$ingredienteAux = $ingrediente3Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente3Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient4'] != NULL){
										$ingrediente4SinCortar = $datos['meals'][$i]['strIngredient4'];
										$array_resultante = explode(",", $ingrediente4SinCortar);
										$ingrediente4Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente4Cortado = $datos['meals'][$i]['strIngredient4'];
									}
									$ingredienteAux = $ingrediente4Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente4Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient5'] != NULL){
										$ingrediente5SinCortar = $datos['meals'][$i]['strIngredient5'];
										$array_resultante = explode(",", $ingrediente5SinCortar);
										$ingrediente5Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente5Cortado = $datos['meals'][$i]['strIngredient5'];
									}
									$ingredienteAux = $ingrediente5Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente5Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient6'] != NULL){
										$ingrediente6SinCortar = $datos['meals'][$i]['strIngredient6'];
										$array_resultante = explode(",", $ingrediente6SinCortar);
										$ingrediente6Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente6Cortado = $datos['meals'][$i]['strIngredient6'];
									}
									$ingredienteAux = $ingrediente6Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {

										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";;
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente6Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient7'] != NULL){
										$ingrediente7SinCortar = $datos['meals'][$i]['strIngredient7'];
										$array_resultante = explode(",", $ingrediente7SinCortar);
										$ingrediente7Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente7Cortado = $datos['meals'][$i]['strIngredient7'];
									}
									$ingredienteAux = $ingrediente7Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente7Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient8'] != NULL){
										$ingrediente8SinCortar = $datos['meals'][$i]['strIngredient8'];
										$array_resultante = explode(",", $ingrediente8SinCortar);
										$ingrediente8Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente8Cortado = $datos['meals'][$i]['strIngredient8'];
									}
									$ingredienteAux = $ingrediente8Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente8Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient9'] != NULL){
										$ingrediente9SinCortar = $datos['meals'][$i]['strIngredient9'];
										$array_resultante = explode(",", $ingrediente9SinCortar);
										$ingrediente9Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente9Cortado = $datos['meals'][$i]['strIngredient9'];
									}
									$ingredienteAux = $ingrediente9Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente9Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}

									if($datos['meals'][$i]['strIngredient10'] != NULL){
										$ingrediente10SinCortar = $datos['meals'][$i]['strIngredient10'];
										$array_resultante = explode(",", $ingrediente10SinCortar);
										$ingrediente10Cortado = trim($array_resultante[0]);
									}
									else{
										$ingrediente10Cortado = $datos['meals'][$i]['strIngredient10'];
									}
									$ingredienteAux = $ingrediente10Cortado;
									$sql = "SELECT COUNT(*) AS cantidad FROM final_platos_ingredientes WHERE
									nombre_ingrediente_plato = '$ingredienteAux' ";
									$resultado = $con->query($sql);
									if ($resultado->num_rows > 0) {
										$fila = $resultado->fetch_assoc();
										$cantidad = $fila['cantidad'];
										if ($cantidad == 0) {
											$sql = "SELECT MAX(id_ingrediente_plato) AS max_id FROM final_platos_ingredientes";
											$resultado = $con->query($sql);
											if ($resultado->num_rows > 0) {
												$fila = $resultado->fetch_assoc();
												$max_id = $fila['max_id'];
												$nuevo_id = $max_id + 1;
												$nombre = $ingrediente10Cortado;
												$descripcion = "Sin descripcion";
												$sql_insert = "INSERT INTO final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) VALUES ($nuevo_id, '$nombre', '$descripcion')";
												$resultado_insert = $con->query($sql_insert);
											}
										}
									}
									$plato = array (
											"id" => $datos['meals'][$i]['idMeal'],
											"nombre" => $datos['meals'][$i]['strMeal'],
											"categoria" => $datos['meals'][$i]['strCategory'],
											"area" => $datos['meals'][$i]['strArea'],
											"instrucciones" => $datos['meals'][$i]['strInstructions'],
											"imagen" => $datos['meals'][$i]['strMealThumb'],
											"video" => $datos['meals'][$i]['strYoutube'],
											"ingrediente1" => $ingrediente1Cortado,
											"ingrediente2" => $ingrediente2Cortado,
											"ingrediente3" => $ingrediente3Cortado,
											"ingrediente4" => $ingrediente4Cortado,
											"ingrediente5" => $ingrediente5Cortado,
											"ingrediente6" => $ingrediente6Cortado,
											"ingrediente7" => $ingrediente7Cortado,
											"ingrediente8" => $ingrediente8Cortado,
											"ingrediente9" => $ingrediente9Cortado,
											"ingrediente10" => $ingrediente10Cortado,
											"cantidad1" => $datos['meals'][$i]['strMeasure1'],
											"cantidad2" => $datos['meals'][$i]['strMeasure2'],
											"cantidad3" => $datos['meals'][$i]['strMeasure3'],
											"cantidad4" => $datos['meals'][$i]['strMeasure4'],
											"cantidad5" => $datos['meals'][$i]['strMeasure5'],
											"cantidad6" => $datos['meals'][$i]['strMeasure6'],
											"cantidad7" => $datos['meals'][$i]['strMeasure7'],
											"cantidad8" => $datos['meals'][$i]['strMeasure8'],
											"cantidad9" => $datos['meals'][$i]['strMeasure9'],
											"cantidad10" => $datos['meals'][$i]['strMeasure10']
									);
									$stmt = $con->prepare("insert into final_platos 
										(id_plato, nombre_plato, categoria_plato, area_plato, instrucciones_plato, imagen_plato, video_plato, 
										ingrediente1_plato,ingrediente2_plato,ingrediente3_plato,ingrediente4_plato,ingrediente5_plato,
										ingrediente6_plato,ingrediente7_plato,ingrediente8_plato,ingrediente9_plato,ingrediente10_plato,
										cantidad1_plato,cantidad2_plato,cantidad3_plato,cantidad4_plato,cantidad5_plato,cantidad6_plato,
										cantidad7_plato,cantidad8_plato,cantidad9_plato,cantidad10_plato)
										values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
									$stmt->bind_param("sssssssssssssssssssssssssss", 
									$plato['id'],$plato['nombre'],$plato['categoria'],$plato['area'],
									$plato['instrucciones'],$plato['imagen'],$plato['video'],
									$plato['ingrediente1'],$plato['ingrediente2'],$plato['ingrediente3'],$plato['ingrediente4'],$plato['ingrediente5'],
									$plato['ingrediente6'],$plato['ingrediente7'],$plato['ingrediente8'],$plato['ingrediente9'],$plato['ingrediente10'],
									$plato['cantidad1'],$plato['cantidad2'],$plato['cantidad3'],$plato['cantidad4'],$plato['cantidad5'],
									$plato['cantidad6'],$plato['cantidad7'],$plato['cantidad8'],$plato['cantidad9'],$plato['cantidad10']);
						
									$stmt->execute();
								}
								else{
									//echo "Error en la lectura";
								}
							}
						}
					}
					break;

				case 'platos_areas':
					$base_url = "https://www.themealdb.com/api/json/v1/1/list.php?a=list";
					$json_data = file_get_contents($base_url);
					$datos = json_decode($json_data,true);

					if($datos['meals'] != null){
						for($i = 0; $i < sizeof($datos['meals']); $i++){
							$area = array (
								"nombreArea" => $datos['meals'][$i]['strArea']
							);
							$stmt = $con->prepare("insert into final_platos_areas (nombre_area_plato) values (?)");
							$stmt->bind_param("s", $area['nombreArea']);
							$stmt->execute();
						}
					}
					else{
						//echo "Error de lectura";
					}
					break;

				case 'platos_ingredientes':
					$base_url = "https://www.themealdb.com/api/json/v1/1/list.php?i=list";
					$json_data = file_get_contents($base_url);
					$datos = json_decode($json_data,true);

					if($datos['meals'] != null){
						for($i = 0; $i < sizeof($datos['meals']); $i++){
							$ingrediente = array (
								"idIngrediente" => $datos['meals'][$i]['idIngredient'],
								"nombreIngrediente" => $datos['meals'][$i]['strIngredient'], 
								"descripcionIngrediente" => $datos['meals'][$i]['strDescription'] 
							);
							$stmt = $con->prepare("insert into final_platos_ingredientes (id_ingrediente_plato, nombre_ingrediente_plato, descripcion_ingrediente_plato) values (?,?,?)");
							$stmt->bind_param("sss", $ingrediente['idIngrediente'], $ingrediente['nombreIngrediente'], $ingrediente['descripcionIngrediente']);
							$stmt->execute();
						}
					}
					else{
						//echo "Error de lectura";
					}
					break;

				case 'platos_categorias':
					$base_url = "https://www.themealdb.com/api/json/v1/1/categories.php";
					$json_data = file_get_contents($base_url);
					$datos = json_decode($json_data,true);

					if($datos['categories'] != null){
						for($i = 0; $i < sizeof($datos['categories']); $i++){
							$categoria = array (
								"idCategoria" => $datos['categories'][$i]['idCategory'], 
								"nombreCategoria" => $datos['categories'][$i]['strCategory'],
								"fotoCategoria" => $datos['categories'][$i]['strCategoryThumb'],
								"descripcionCategoria" => $datos['categories'][$i]['strCategoryDescription']
							);
							$stmt = $con->prepare("insert into final_platos_categorias (id_categoria_plato, nombre_categoria_plato, foto_categoria_plato, descripcion_categoria_plato) values (?,?,?,?)");
							$stmt->bind_param("ssss", $categoria['idCategoria'],$categoria['nombreCategoria'],$categoria['fotoCategoria'],$categoria['descripcionCategoria']);
							$stmt->execute();
						}
					}
					else{
						//echo "Error de lectura";
					}
					break;
			}
		}

		$response = array("success" => true); 
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	function mgraficos() {
		$resultado_platos_categorias = mgrafico_platos_categorias();
		$resultado_platos_areas = mgrafico_platos_areas();
		$resultado_bebidas_categorias = mgrafico_bebidas_categorias();
		$resultado = [$resultado_platos_categorias, $resultado_platos_areas, $resultado_bebidas_categorias];
		return $resultado;
	}

	function mgrafico_platos_categorias() {
		$con = conexion();
		$consulta_categorias_platos = "SELECT pc.nombre_categoria_plato AS categoria, COUNT(p.id_plato) AS total_platos
					FROM final_platos_categorias pc
					LEFT JOIN final_platos p ON pc.nombre_categoria_plato = p.categoria_plato
					GROUP BY pc.nombre_categoria_plato";
		$resultado = $con->query($consulta_categorias_platos);
	
		$categorias = [];
		$total_platos = [];
		if ($resultado && $resultado->num_rows > 0) {
			while ($row = $resultado->fetch_assoc()) {
				$categorias[] = $row['categoria'];
				$total_platos[] = $row['total_platos'];
			}
		}
	
		
		$con->close();
	
		return [$categorias, $total_platos];
	}
	
	function mgrafico_platos_areas() {
		$con = conexion();
		$consulta_areas_platos = "SELECT pa.nombre_area_plato AS area, COUNT(p.id_plato) AS total_platos
								FROM final_platos_areas pa
								LEFT JOIN final_platos p ON pa.nombre_area_plato = p.area_plato
								GROUP BY pa.nombre_area_plato";
		$resultado = $con->query($consulta_areas_platos);
	
		$areas = [];
		$total_platos_areas = [];
		if ($resultado && $resultado->num_rows > 0) {
			while ($row = $resultado->fetch_assoc()) {
				$areas[] = $row['area'];
				$total_platos_areas[] = $row['total_platos'];
			}
		}
	
		
		$con->close();
	
		return [$areas, $total_platos_areas];
	}
	
	function mgrafico_bebidas_categorias() {
		$con = conexion();
		$consulta_bebidas_categorias = "SELECT bc.nombre_categoria_bebida AS categoria, COUNT(b.id_bebida) AS total_bebidas
								FROM final_bebida_categorias bc
								LEFT JOIN final_bebida b ON bc.nombre_categoria_bebida = b.categoria_bebida
								GROUP BY bc.nombre_categoria_bebida";
		$resultado = $con->query($consulta_bebidas_categorias);
	
		$categorias_bebida = [];
		$total_bebidas = [];
		if ($resultado && $resultado->num_rows > 0) {
			while ($row = $resultado->fetch_assoc()) {
				$categorias_bebida[] = $row['categoria'];
				$total_bebidas[] = $row['total_bebidas'];
			}
		}
	
		$con->close();
	
		return [$categorias_bebida, $total_bebidas];
	}


?>
