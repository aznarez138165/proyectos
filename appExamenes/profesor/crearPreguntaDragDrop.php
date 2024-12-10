<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $pregunta = $_POST['pregunta'];
    $puntuacion = $_POST['puntuacion'];
    $elementos = $_POST['elementos']; // Array de elementos del formulario
    $posiciones = $_POST['posiciones']; // Array de posiciones del formulario

    $host = 'sql104.infinityfree.com';
    $dbname = 'if0_37518630_gpi1_nashe';
    $username = 'if0_37518630';
    $password = 'ubbfyRR3N8dd';

    // Establecer conexión a la base de datos
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar la pregunta en la tabla 'preguntas_dragdrop'
    $stmt = $conn->prepare("INSERT INTO preguntas_dragdrop (pregunta, puntuacion) VALUES (?, ?)");
    $stmt->bind_param("si", $pregunta, $puntuacion);

    if ($stmt->execute()) {
        $id_pregunta = $conn->insert_id; // Obtener el ID de la pregunta recién creada

        // Insertar los elementos relacionados en la tabla 'elementos_dragdrop'
        $stmt_elemento = $conn->prepare("INSERT INTO elementos_dragdrop (pregunta_id, texto_elemento, posicion_correcta) VALUES (?, ?, ?)");
        foreach ($elementos as $index => $elemento) {
            $posicion_correcta = $posiciones[$index];
            $stmt_elemento->bind_param("isi", $id_pregunta, $elemento, $posicion_correcta);
            $stmt_elemento->execute();
        }
        $stmt_elemento->close();

        $response = "<div class='success-message'>Pregunta Drag & Drop creada exitosamente.</div>";
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
    <title>Crear Pregunta Drag & Drop</title>
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
        textarea {
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
    <script>
        function addElemento() {
            const container = document.getElementById('elementos-container');
            const index = container.children.length;

            const div = document.createElement('div');
            div.innerHTML = `
                <label>Elemento ${index + 1}:</label>
                <input type="text" name="elementos[]" required>
                <label>Posición Correcta:</label>
                <input type="number" name="posiciones[]" required>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Crear Nueva Pregunta Drag & Drop</h1>
    <form method="post" action="crearPreguntaDragDrop.php">
        <label>Pregunta:</label>
        <input type="text" name="pregunta" required>
        
        <label>Puntuación:</label>
        <input type="number" name="puntuacion" step="1" required>
        
        <div id="elementos-container"></div>
        <button type="button" class="button" onclick="addElemento()">Agregar Elemento</button>
        
        <button type="submit" class="button">Crear Pregunta</button>
    </form>

    <a href="profesor_dashboard.php" class="button return-button">Volver al Panel de Profesor</a>

    <!-- Mensaje de respuesta -->
    <?php if (isset($response)) echo $response; ?>
</div>
</body>
</html>
