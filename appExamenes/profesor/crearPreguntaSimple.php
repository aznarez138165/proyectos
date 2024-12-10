<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pregunta = $_POST['pregunta'];
    $puntuacion = $_POST['puntuacion'];
    $respuesta_correcta = $_POST['respuesta_correcta'];
    $respuestas = [$_POST['respuesta1'], $_POST['respuesta2'], $_POST['respuesta3'], $_POST['respuesta4']];

    $host = 'sql104.infinityfree.com';
    $dbname = 'if0_37518630_gpi1_nashe';
    $username = 'if0_37518630';
    $password = 'ubbfyRR3N8dd';

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO preguntas (pregunta, respuesta_correcta, puntuacion) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $pregunta, $respuesta_correcta, $puntuacion);

    if ($stmt->execute()) {
        $pregunta_id = $conn->insert_id;

        foreach ($respuestas as $key => $respuesta) {
            $stmt = $conn->prepare("INSERT INTO respuestas (pregunta_id, respuesta) VALUES (?, ?)");
            $stmt->bind_param("is", $pregunta_id, $respuesta);
            $stmt->execute();
        }

        $response = "<div class='success-message'>Pregunta creada exitosamente.</div>";
    } else {
        $response = "<div class='error-message'>Error al crear la pregunta. Por favor, inténtelo de nuevo.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pregunta</title>
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
        <h1>Crear Nueva Pregunta Simple</h1>
        <form method="post" action="crearPreguntaSimple.php">
            <label>Pregunta:</label>
            <input type="text" name="pregunta" required>
            
            <label>Puntuación:</label>
            <input type="number" name="puntuacion" step="0.01" required>

            <label>Respuestas:</label>
            <input type="text" name="respuesta1" required placeholder="Respuesta 1">
            <input type="text" name="respuesta2" required placeholder="Respuesta 2">
            <input type="text" name="respuesta3" required placeholder="Respuesta 3">
            <input type="text" name="respuesta4" required placeholder="Respuesta 4">
            
            <label>Respuesta Correcta (número):</label>
            <input type="number" name="respuesta_correcta" min="1" max="4" required>
            
            <button type="submit" class="button">Crear Pregunta</button>
        </form>

        <a href="profesor_dashboard.php" class="button return-button">Volver al Panel de Profesor</a>


        <!-- Mensaje de respuesta -->
        <?php if (isset($response)) echo $response; ?>
    </div>
</body>
</html>