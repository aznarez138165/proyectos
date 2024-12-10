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
	/*
	function conexion(){

		$servidor = "localhost";
		$bd = "prueba";
		$user = "root";
		$password = "";

		$con = mysqli_connect($servidor, $user, $password, $bd);

		if (!$con) {
			echo "Error de conexión de base de datos <br>";
			echo "Error número: " . mysqli_connect_errno();
			echo "Texto error: " . mysqli_connect_error();
			exit;
		}
		return $con;
	}
*/


 ?>
