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

    // Consulta SQL para obtener la última fecha de actualización de la API
    $sql = "SELECT ultima_act_api FROM final_ultimaFecha";
    $result = $con->query($sql);

    // Verificar si se obtuvo algún resultado
    if ($result->num_rows > 0) {
        // Obtener la fila de resultados
        $row = $result->fetch_assoc();
        // Obtener la fecha de la columna ultima_act_api
        $fechaActualizacion = $row["ultima_act_api"];

        // Devolver la fecha como respuesta
        echo $fechaActualizacion;
    } else {
        // Si no se encontraron resultados
        echo "No se encontraron resultados.";
    }

    // Cerrar conexión
    $con->close();
?>
