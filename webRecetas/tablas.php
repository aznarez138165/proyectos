<?php
	function conexion(){

		$servidor = "dbserver";
		$bd = "db_grupo23";
		$user = "grupo23";
		$password = "Eique8ooTh";

		$con = mysqli_connect($servidor, $user, $password, $bd);

		if (!$con) {
			echo "Error de conexión de base de datos <br>";
			echo "Error número: " . mysqli_connect_errno();
			echo "Texto error: " . mysqli_connect_error();
			exit;
		}
		return $con;
	}

	$con = conexion();
/*
DELETE FROM bebida;
DELETE FROM bebida_ingredientes;
DELETE FROM bebida_categorias;
DELETE FROM platos;
DELETE FROM platos_ingredientes;
DELETE FROM platos_categorias;
DELETE FROM platos_areas;
*/
//--------------------------- ELIMINAR TABLAS ANTIGUAS
	// Función para vaciar todas las tablas
	function vaciarTablas($conexion) {
	    // Lista de todas las tablas
	    $tablas = array("final_bebida", "final_bebida_ingredientes", "final_bebida_categorias",
	     "final_platos", "final_platos_ingredientes", "final_platos_categorias", "final_platos_areas", "final_ultimaFecha");

	    // Iterar sobre cada tabla y ejecutar TRUNCATE
	    foreach ($tablas as $tabla) {
	        $query = "TRUNCATE TABLE $tabla";
	        mysqli_query($conexion, $query);
	    }	
		echo "<script>alert('Eliminación de tablas completa.');</script>";
	}
	/*
	$tabla_platos = "final_platos";
	$tabla_platos_ingredientes = "final_platos_ingredientes";
	$tabla_platos_categorias = "final_platos_categorias";
	$tabla_platos_areas = "final_platos_areas";
	*/
	vaciarTablas($con);

	// NUEVA FECHA ACTUALIZACION API

	// Consulta SQL para insertar la fecha actual en la tabla
	$fechaActual = date("Y-m-d");
	$sql = "INSERT INTO final_ultimaFecha (ultima_act_api) VALUES ('$fechaActual')";
	$result = $con->query($sql);

	// REQUEST CATEGORIAS_PLATOS ------------------------------------------------

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
			exit("ERROR");
		}


	// REQUEST AREAS_PLATOS ------------------------------------------------

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
			echo "Error de lectura";
		}

	// REQUEST INGREDIENTES_PLATOS ------------------------------------------------

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
			echo "Error de lectura";
		}
	// REQUEST PLATOS ------------------------------------------------

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
						echo "Error en la lectura";
					}
				}
			}
		}
	//--------------------------- REQUEST INGREDIENTES BEBIDAS

	$base_url = "https://www.thecocktaildb.com/api/json/v1/1/list.php?i=list";
	$json_data = file_get_contents($base_url);
	$datos = json_decode($json_data,true);
	$tiempo_espera = 100000;
	
	$ingrediente_url = "https://www.thecocktaildb.com/api/json/v1/1/search.php?i=";

	if($datos['drinks'] != null){
		for($i = 0; $i < sizeof($datos['drinks']); $i++){
			$nombre_ingrediente = $datos['drinks'][$i]['strIngredient1'];
			$url = $ingrediente_url . $nombre_ingrediente;
			$json_data_ing = file_get_contents($url);	
			$datos_ingredientes = json_decode($json_data_ing,true);
			if($datos_ingredientes['ingredients'] != null){
				$ingredienteBebida = array (
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
			usleep($tiempo_espera);
		}
	}
	else{
		echo "Error de lectura";
	}

	//--------------------------- REQUEST CATEGORIAS BEBIDAS

	$url = "https://www.thecocktaildb.com/api/json/v1/1/list.php?c=list";
	$json_data = file_get_contents($url);
	$datos = json_decode($json_data,true);
	if($datos['drinks'] != null){
		for($i = 0; $i < sizeof($datos['drinks']); $i++){
			if($datos != null){
				$categoria = array("nombreCategoriaBebida" => $datos['drinks'][$i]['strCategory']);
				$stmt = $con->prepare("insert into final_bebida_categorias (nombre_categoria_bebida) values (?)");
				$stmt->bind_param("s",$categoria['nombreCategoriaBebida']);
				$stmt->execute();
			}
			else{
				echo "Error en la lectura <br>";
			}
		}
	}
	//--------------------------- REQUEST BEBIDAS

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
					echo "Error en la lectura";
				}
			}
		}
	}
?>