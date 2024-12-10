<?php
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<div class='error-message'>Conexión fallida: " . $conn->connect_error . "</div>");
}

$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examen_id = $_POST['examen'];
    $preguntas = $_POST['preguntas'];

    if (!empty($preguntas)) {
        foreach ($preguntas as $pregunta_id) {
            // Insertar las preguntas en la tabla 'examen_pregunta'
            $stmt = $conn->prepare("INSERT INTO examen_pregunta (examen_id, pregunta_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $examen_id, $pregunta_id);
            $stmt->execute();
        }
        $stmt->close();
        $response = "<div class='success-message'>Preguntas asignadas al examen exitosamente.</div>";
    } else {
        $response = "<div class='error-message'>Por favor, selecciona al menos una pregunta.</div>";
    }
}

// Consultar los exámenes disponibles
$examenes = $conn->query("SELECT id, nombre FROM examenes");

// Consultar las preguntas de tipo 'Drag and Drop'
$preguntas_dragdrop = $conn->query("SELECT id_pregunta FROM preguntas_dragdrop");

// Consultar las preguntas de tipo diferente a 'Drag and Drop'
$preguntas = $conn->query("SELECT id, pregunta FROM preguntas WHERE id NOT IN (SELECT id_pregunta FROM preguntas_dragdrop)");

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Preguntas al Examen</title>
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
        select {
            margin-bottom: 15px;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        .checkbox-list {
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
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
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .success-message, .error-message {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
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
        <h1>Asignar Preguntas al Examen</h1>
        <form method="post" action="asignarPreguntas.php">
            <label>Selecciona Examen:</label>
            <select name="examen" required>
                <?php while ($examen = $examenes->fetch_assoc()) : ?>
                    <option value="<?= $examen['id'] ?>"><?= $examen['nombre'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Selecciona Preguntas Comunes:</label>
            <div class="checkbox-list">
                <?php while ($pregunta = $preguntas->fetch_assoc()) : ?>
                    <input type="checkbox" name="preguntas[]" value="<?= $pregunta['id'] ?>">
                    <?= $pregunta['pregunta'] ?><br>
                <?php endwhile; ?>
            </div>

            <label>Selecciona Preguntas Drag and Drop:</label>
            <div class="checkbox-list">
                <?php while ($pregunta_dragdrop = $preguntas_dragdrop->fetch_assoc()) : ?>
                    <input type="checkbox" name="preguntas[]" value="<?= $pregunta_dragdrop['id'] ?>">
                    <?= $pregunta_dragdrop['pregunta'] ?><br>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="button">Asignar Preguntas</button>
        </form>

        <?php if (!empty($response)) echo $response; ?>

        <a href="profesor_dashboard.php" class="back-button">Volver al Panel de Profesor</a>
    </div>
</body>
</html>
