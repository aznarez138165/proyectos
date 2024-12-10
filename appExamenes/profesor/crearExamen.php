<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $duracion = $_POST['duracion'];

    $host = 'sql104.infinityfree.com';
    $dbname = 'if0_37518630_gpi1_nashe';
    $username = 'if0_37518630';
    $password = 'ubbfyRR3N8dd';

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        $response = "<div class='error-message'>Conexión fallida: " . $conn->connect_error . "</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO examenes (nombre, fecha_inicio, fecha_fin, duracion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $fecha_inicio, $fecha_fin, $duracion);

        if ($stmt->execute()) {
            $response = "<div class='success-message'>Examen creado exitosamente.</div>";
        } else {
            $response = "<div class='error-message'>Error al crear el examen. Por favor, inténtelo de nuevo.</div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Examen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="datetime-local"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .button {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .back-button {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
            transition: color 0.3s;
        }
        .back-button:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Examen</h1>
        <form method="post" action="crearExamen.php">
            <label>Nombre del Examen:</label>
            <input type="text" name="nombre" required>
            
            <label>Fecha y Hora de Inicio:</label>
            <input type="datetime-local" name="fecha_inicio" required>
            
            <label>Fecha y Hora de Fin:</label>
            <input type="datetime-local" name="fecha_fin" required>
            
            <label>Duración (en minutos):</label>
            <input type="number" name="duracion" required>
            
            <button type="submit" class="button">Crear Examen</button>
        </form>

        <!-- Mensaje de respuesta -->
        <?php if (isset($response)) echo $response; ?>
        <a href="profesor_dashboard.php" class="back-button">Volver al Panel de Profesor</a>

    </div>
</body>
</html>
