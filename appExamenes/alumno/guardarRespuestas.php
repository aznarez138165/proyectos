<?php
// Configuración de la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

try {
    // Conexión con la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el ID del examen
    if (isset($_POST['examen_id']) && is_numeric($_POST['examen_id'])) {
        $examen_id = intval($_POST['examen_id']);
    } else {
        die("ID de examen no válido.");
    }

    // Verificar si el alumno está autenticado
    session_start();
    if (isset($_SESSION['user_id'])) {
        $alumno_id = $_SESSION['user_id'];
    } else {
        die("ID de alumno no válido.");
    }

    // Inicializar variables para el cálculo de la nota
    $nota_total = 0;

    // Recorrer cada respuesta enviada por el alumno
    foreach ($_POST['respuesta'] as $pregunta_id => $respuesta_id) {
        // Consultar la respuesta correcta y la puntuación de la pregunta
        $sqlPregunta = "SELECT respuesta_correcta, puntuacion FROM preguntas WHERE id = :pregunta_id";
        $stmtPregunta = $pdo->prepare($sqlPregunta);
        $stmtPregunta->execute([':pregunta_id' => $pregunta_id]);
        $pregunta = $stmtPregunta->fetch(PDO::FETCH_ASSOC);

        if ($pregunta) {
            // Verificar si la respuesta es correcta
            $respuestaCorrecta = $pregunta['respuesta_correcta'];
            $puntuacion = $pregunta['puntuacion'];
            $esCorrecta = ($respuesta_id == $respuestaCorrecta);

            // Sumar la puntuación si la respuesta es correcta
            if ($esCorrecta) {
                $nota_total += $puntuacion;
            }

            // Guardar la respuesta del alumno en la base de datos
            $sqlInsert = "INSERT INTO respuestas_usuario (examen_id, alumno_id, pregunta_id, respuesta_id, nota) 
                          VALUES (:examen_id, :alumno_id, :pregunta_id, :respuesta_id, :nota)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                ':examen_id' => $examen_id,
                ':alumno_id' => $alumno_id,
                ':pregunta_id' => $pregunta_id,
                ':respuesta_id' => $respuesta_id,
                ':nota' => $esCorrecta ? $puntuacion : 0,
            ]);
        }
    }

    // Redirigir al archivo nota.php con la nota calculada
    header("Location: nota.php?nota=" . urlencode($nota_total));
    exit;

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>

