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

//--------------------------- ELIMINAR TABLAS ANTIGUAS
	// Función para vaciar todas las tablas
	function vaciarTablas($conexion) {
	    // Lista de todas las tablas
	    $tablas = array("final_bebida", "final_bebida_ingredientes", "final_bebida_categorias",
	     "final_platos", "final_platos_ingredientes", "final_platos_categorias", "final_platos_areas","final_ultimaFecha");

	    // Iterar sobre cada tabla y ejecutar TRUNCATE
	    foreach ($tablas as $tabla) {
	        $query = "TRUNCATE TABLE $tabla";
	        mysqli_query($conexion, $query);
	    }	
		echo "<script>alert('Eliminación de tablas completa.');</script>";
	}
    vaciarTablas($con);
?>