<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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
    $alumnos = $_POST['alumnos'] ?? [];
    $grupo = $_POST['grupo'] ?? null;

    if ($grupo) {
        // Asignar a los alumnos de un grupo
        $stmt = $conn->prepare("SELECT alumno_id FROM grupo_alumno WHERE grupo_id = ?");
        $stmt->bind_param("i", $grupo);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row['alumno_id'];
        }
        $stmt->close();
    }

    if (!empty($alumnos)) {
        foreach ($alumnos as $alumno_id) {
            $stmt = $conn->prepare("INSERT INTO examen_alumno (examen_id, alumno_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $examen_id, $alumno_id);
            $stmt->execute();
        }
        $stmt->close();
        $response = "<div class='success-message'>Examen asignado a los alumnos exitosamente.</div>";
    } else {
        $response = "<div class='error-message'>Por favor, selecciona al menos un alumno.</div>";
    }
}

$examenes = $conn->query("SELECT id, nombre FROM examenes");
$alumnos_query = $conn->query("SELECT id, nombre, ap1, ap2 FROM usuarios WHERE rol = 'alumno'");
$grupos_query = $conn->query("SELECT id, nombre FROM grupos");
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Examenes a Alumnos</title>
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
        select {
            margin-bottom: 15px;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        .checkbox-list {
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            padding: 10px;
            margin-bottom: 15px;
        }
        .checkbox-item {
            margin: 5px 0;
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
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .success-message, .error-message {
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
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
        <h1>Asignar Examen a Alumnos</h1>
        <form method="post" action="asignarExamenes.php">
            <label>Selecciona Examen:</label>
            <select name="examen" required>
                <?php while ($examen = $examenes->fetch_assoc()) : ?>
                    <option value="<?= $examen['id'] ?>"><?= $examen['nombre'] ?></option>
                <?php endwhile; ?>
            </select>

            <label>Asignar A:</label>
            <select id="asignar_a" name="asignar_a" required onchange="toggleAssignmentOptions()">
                <option value="alumnos">Alumnos</option>
                <option value="grupos">Grupos</option>
            </select>

            <div id="alumnos-section">
                <label>Selecciona Alumnos:</label>
                <div class="checkbox-list">
                    <?php while ($alumno = $alumnos_query->fetch_assoc()) : ?>
                        <div class="checkbox-item">
                            <input type="checkbox" name="alumnos[]" value="<?= $alumno['id'] ?>">
                            <?= $alumno['nombre'] . " " . $alumno['ap1'] . " " . $alumno['ap2'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div id="grupos-section" style="display:none;">
                <label>Selecciona Grupo:</label>
                <select id="grupo" name="grupo" onchange="showStudentsForGroup()">
                    <option value="">--Seleccionar Grupo--</option>
                    <?php while ($grupo = $grupos_query->fetch_assoc()) : ?>
                        <option value="<?= $grupo['id'] ?>"><?= $grupo['nombre'] ?></option>
                    <?php endwhile; ?>
                </select>

                <div id="alumnos-grupo-section" style="display:none;">
                    <label>Alumnos del Grupo:</label>
                    <div class="checkbox-list" id="alumnos-grupo-list"></div>
                </div>
            </div>

            <button type="submit" class="button">Asignar Examen</button>
        </form>

        <!-- Mensaje de respuesta -->
        <?php if (!empty($response)) echo $response; ?>

        <!-- Botón para volver al profesorDashboard.php -->
        <a href="profesor_dashboard.php" class="button return-button">Volver al Panel de Profesor</a>
    </div>

    <script>
        function toggleAssignmentOptions() {
            var asignar_a = document.getElementById("asignar_a").value;
            if (asignar_a === "alumnos") {
                document.getElementById("alumnos-section").style.display = "block";
                document.getElementById("grupos-section").style.display = "none";
            } else {
                document.getElementById("alumnos-section").style.display = "none";
                document.getElementById("grupos-section").style.display = "block";
            }
        }

        function showStudentsForGroup() {
            var grupo = document.getElementById("grupo").value;
            if (grupo) {
                fetch(`getAlumnosForGrupo.php?grupo_id=${grupo}`)
                    .then(response => response.json())
                    .then(data => {
                        var lista = document.getElementById("alumnos-grupo-list");
                        lista.innerHTML = "";
                        data.forEach(alumno => {
                            var item = document.createElement("div");
                            item.classList.add("checkbox-item");
                            item.innerHTML = `<input type="checkbox" name="alumnos[]" value="${alumno.id}"> ${alumno.nombre} ${alumno.ap1} ${alumno.ap2}`;
                            lista.appendChild(item);
                        });
                        document.getElementById("alumnos-grupo-section").style.display = "block";
                    });
            } else {
                document.getElementById("alumnos-grupo-section").style.display = "none";
            }
        }

        toggleAssignmentOptions();
    </script>
</body>
</html>
