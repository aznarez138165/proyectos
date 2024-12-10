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
    die("<div class='error-message'>Conexión fallida: " . $conn->connect_error . "</div>");
}

// Inicialización de variable para mensajes
$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $grupo_id = $_POST['grupo'];
    $alumnos = $_POST['alumnos'];

    if (!empty($alumnos)) {
        // Asignar los alumnos al grupo
        foreach ($alumnos as $alumno_id) {
            $stmt = $conn->prepare("INSERT INTO grupo_alumno (grupo_id, alumno_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $grupo_id, $alumno_id);
            $stmt->execute();
        }
        $stmt->close();
        $response = "<div class='success-message'>Alumnos asignados al grupo exitosamente.</div>";
    } else {
        $response = "<div class='error-message'>Por favor, selecciona al menos un alumno.</div>";
    }
}

$grupos = $conn->query("SELECT id, nombre FROM grupos");
$alumnos = $conn->query("SELECT id, nombre, ap1, ap2 FROM usuarios WHERE rol = 'alumno'");

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Alumnos a Grupo</title>
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
        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
        }
        .checkbox-container label {
            flex: 1 0 45%;
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asignar Alumnos a Grupo</h1>
        <form method="post" action="asignarGrupos.php">
            
            <label>Selecciona el Grupo:</label>
            <select name="grupo" required>
                <?php while ($grupo = $grupos->fetch_assoc()) : ?>
                    <option value="<?= $grupo['id'] ?>"><?= $grupo['nombre'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Selecciona los Alumnos:</label>
            <div class="checkbox-container">
                <?php while ($alumno = $alumnos->fetch_assoc()) : ?>
                    <label>
                        <input type="checkbox" name="alumnos[]" value="<?= $alumno['id'] ?>">
                        <?= $alumno['nombre'] . " " . $alumno['ap1'] . " " . $alumno['ap2'] ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="button">Asignar Alumnos</button>
        </form>

        <?php if (!empty($response)) echo $response; ?>

        <a href="admin_dashboard.php" class="back-button">Volver al Panel de Administrador</a>
    </div>
</body>
</html>
