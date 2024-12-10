<?php
// Configuración de la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

// Conexión a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicialización de variable para mensajes
$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_grupo = $_POST['nombre_grupo'];

    if (!empty($nombre_grupo)) {
        // Comprobar si ya existe un grupo con ese nombre
        $stmt = $conn->prepare("SELECT id FROM grupos WHERE nombre = ?");
        $stmt->bind_param("s", $nombre_grupo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response = "<div class='error-message'>Ya existe un grupo con ese nombre. Por favor, elige otro.</div>";
        } else {
            // Preparar y ejecutar la consulta para insertar el grupo si no existe
            $stmt = $conn->prepare("INSERT INTO grupos (nombre) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $nombre_grupo);

                if ($stmt->execute()) {
                    $response = "<div class='success-message'>Grupo creado exitosamente.</div>";
                } else {
                    $response = "<div class='error-message'>Error al crear el grupo: " . $stmt->error . "</div>";
                }

                $stmt->close();
            } else {
                $response = "<div class='error-message'>Error en la preparación de la consulta: " . $conn->error . "</div>";
            }
        }

        $stmt->close();
    } else {
        $response = "<div class='error-message'>El nombre del grupo no puede estar vacío.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Grupo</title>
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
        input[type="text"] {
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
            width: 100%;
            background-color: #ccc;
            color: black;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .back-button:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Grupo</h1>
        <form method="post" action="crearGrupo.php">
            <label>Nombre del Grupo:</label>
            <input type="text" name="nombre_grupo" required>
            <button type="submit" class="button">Crear Grupo</button>
        </form>
        
        <!-- Mensaje de respuesta -->
        <?php if (!empty($response)) echo $response; ?>

        <!-- Botón Volver -->
        <a href="admin_dashboard.php">
            <button type="button" class="back-button">Volver</button>
        </a>
    </div>
</body>
</html>
