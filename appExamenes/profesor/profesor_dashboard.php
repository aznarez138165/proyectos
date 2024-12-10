<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_pregunta = $_POST['tipo_pregunta'];

    // Dependiendo del tipo de pregunta seleccionada, redirigimos al archivo correspondiente
    switch ($tipo_pregunta) {
        case 'unica':
            header('Location: crearPreguntaSimple.php');
            exit;
        case 'multiple':
            header('Location: crearPreguntaMultiple.php');
            exit;
        case 'abierta':
            header('Location: crearPreguntaAbierta.php');
            exit;
        case 'dragdrop':
            header('Location: crearPreguntaDragDrop.php');
            exit;
        default:
            echo "Selecciona un tipo de pregunta válido.";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Profesor</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            transition: background 0.3s, transform 0.2s;
            font-size: 1rem;
            font-weight: bold;
        }
        .button:hover {
            background: linear-gradient(135deg, #0056b3, #004494);
            transform: translateY(-3px);
        }
        .button:active {
            transform: translateY(0);
        }
        form {
            display: inline-block;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            text-align: left;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        select:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }
        button:hover {
            background: linear-gradient(135deg, #0056b3, #004494);
            transform: translateY(-3px);
        }
        button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <h1>Bienvenido al Panel de Profesor</h1>
    <ul>
        <li><a class="button" href="crearExamen.php">Crear Nuevo Examen</a></li>
        <li>
            <form method="POST">
                <label for="tipo_pregunta">Selecciona el tipo de pregunta:</label>
                <select id="tipo_pregunta" name="tipo_pregunta">
                    <option value="unica">Pregunta Única Respuesta Correcta</option>
                    <option value="multiple">Pregunta Múltiples Respuestas Correctas</option>
                    <option value="abierta">Pregunta Abierta</option>
                    <option value="dragdrop">Pregunta Drag and Drop</option>
                </select>
                <button type="submit">Crear Nueva Pregunta</button>
            </form>
        </li>
        <li><a class="button" href="asignarPreguntas.php">Asignar Preguntas a Examen</a></li>
        <li><a class="button" href="asignarExamenes.php">Asignar Examen a Alumnos</a></li>
        <li><a class="button" href="verResultadosExamenes.php">Ver Resultados de Exámenes</a></li>
        <li><a class="button" href="corregirRespuestasAbiertas.php">Corregir Respuestas Abiertas</a></li>
    </ul>
</body>
</html>
