<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $pregunta = $_POST['pregunta'];
    $puntuacion = $_POST['puntuacion'];

    // Establecer conexión a la base de datos
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener las respuestas para preguntas de múltiples respuestas correctas
    $respuestas = $_POST['respuestas_multiples']; // Respuestas seleccionadas
    $respuestas_correctas = isset($_POST['respuestas_correctas']) ? $_POST['respuestas_correctas'] : []; // Respuestas correctas seleccionadas
    foreach ($respuestas as $respuesta) {
        // Verifica si la respuesta está en el array de respuestas correctas
        $es_correcta = in_array($respuesta, $respuestas_correctas) ? 1 : 0;

        // Inserta la respuesta en la tabla
        $stmt = $conn->prepare("INSERT INTO respuestas_multiple (pregunta_id, respuesta, es_correcta) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $pregunta_id, $respuesta, $es_correcta);
        $stmt->execute();
    }

    // Insertar la pregunta en la tabla 'preguntas_multiple'
    $stmt = $conn->prepare("INSERT INTO preguntas_multiple (pregunta, puntuacion) VALUES (?, ?)");
    $stmt->bind_param("si", $pregunta, $puntuacion);

    if ($stmt->execute()) {
        $pregunta_id = $conn->insert_id;

        // Insertar las respuestas en la tabla 'respuestas_multiple'
        foreach ($respuestas as $respuesta) {
            $es_correcta = in_array($respuesta, $respuestas_correctas) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO respuestas_multiple (pregunta_id, respuesta, es_correcta) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $pregunta_id, $respuesta, $es_correcta);
            $stmt->execute();
        }

        $response = "<div class='success-message'>Pregunta de múltiples respuestas creada exitosamente.</div>";
    } else {
        $response = "<div class='error-message'>Error al crear la pregunta. Por favor, inténtelo de nuevo.</div>";
    }
    
    $stmt->close();
    $conn->close();
}

// Establecer conexión para obtener respuestas con paginación
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el número de página desde la URL (si no existe, se asume la página 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Número de respuestas por página
$offset = ($page - 1) * $limit; // Cálculo del desplazamiento para la consulta

// Consulta para obtener las respuestas con LIMIT y OFFSET
$query = "SELECT respuesta FROM respuestas LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
$respuestas_db = [];
while ($row = $result->fetch_assoc()) {
    $respuestas_db[] = $row['respuesta'];
}

// Obtener el total de respuestas para calcular el número de páginas
$query_total = "SELECT COUNT(*) as total FROM respuestas";
$result_total = $conn->query($query_total);
$row_total = $result_total->fetch_assoc();
$total_respuestas = $row_total['total'];
$total_paginas = ceil($total_respuestas / $limit); // Número total de páginas

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pregunta</title>
    <style>
        /* CSS Mejorado */
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
            max-width: 600px;
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
        input[type="number"],
        textarea,
        select {
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .return-button {
            margin-top: 15px;
            background-color: #6c757d;
        }
        .return-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Pregunta</h1>
        <form method="POST" action="crearPreguntaMultiple.php">
            <label for="pregunta">Pregunta:</label>
            <input type="text" id="pregunta" name="pregunta" required>

            <label for="puntuacion">Puntuación:</label>
            <input type="number" id="puntuacion" name="puntuacion" required>

            <label for="respuestas_multiples[]">Respuestas:</label>
            <?php foreach ($respuestas_db as $respuesta): ?>
                <div>
                    <input type="checkbox" name="respuestas_multiples[]" value="<?= $respuesta ?>"> <?= $respuesta ?>
                </div>
            <?php endforeach; ?>

            <label for="respuestas_correctas[]">Respuestas Correctas:</label>
            <input type="checkbox" name="respuestas_correctas[]" value="respuesta1"> Respuesta 1
            <input type="checkbox" name="respuestas_correctas[]" value="respuesta2"> Respuesta 2
            <input type="checkbox" name="respuestas_correctas[]" value="respuesta3"> Respuesta 3
            <input type="checkbox" name="respuestas_correctas[]" value="respuesta4"> Respuesta 4

            <button type="submit" class="button">Crear Pregunta</button>
        </form>

        <a href="profesor_dashboard.php" class="button return-button">Volver al Panel de Profesor</a>


        <!-- Paginación -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1">Primera</a>
                <a href="?page=<?= $page - 1 ?>">Anterior</a>
            <?php endif; ?>

            <?php if ($page < $total_paginas): ?>
                <a href="?page=<?= $page + 1 ?>">Siguiente</a>
                <a href="?page=<?= $total_paginas ?>">Última</a>
            <?php endif; ?>
        </div>

        <?php if (isset($response)) echo $response; ?>
    </div>
</body>
</html>
