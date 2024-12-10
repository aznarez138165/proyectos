<?php
// Configuración de la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

session_start(); // Asegúrate de que la sesión esté iniciada
$alumno_id = $_SESSION['user_id'] ?? null; // Verifica que el ID del alumno esté disponible
if (!$alumno_id) {
    die("No se encontró el ID del alumno en la sesión. Por favor, inicia sesión.");
}

try {
    // Conexión con la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar y obtener el ID del examen
    if (isset($_GET['examen_id']) && is_numeric($_GET['examen_id'])) {
        $examen_id = intval($_GET['examen_id']);
    } else {
        die("ID de examen no válido.");
    }

    // Consultar la información del examen
    $sqlExamen = "SELECT nombre, fecha_inicio, fecha_fin, duracion FROM examenes WHERE id = :examen_id";
    $stmtExamen = $pdo->prepare($sqlExamen);
    $stmtExamen->execute([':examen_id' => $examen_id]);
    $examen = $stmtExamen->fetch();

    if (!$examen) {
        die("Examen no encontrado.");
    }

    // Consultar las preguntas asociadas al examen
    $sqlPreguntas = "
        SELECT p.id, p.pregunta 
        FROM preguntas p 
        JOIN examen_pregunta ep ON p.id = ep.pregunta_id 
        WHERE ep.examen_id = :examen_id";
    $stmtPreguntas = $pdo->prepare($sqlPreguntas);
    $stmtPreguntas->execute([':examen_id' => $examen_id]);
    $preguntas = $stmtPreguntas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el índice actual de la pregunta
    $total_preguntas = count($preguntas);
    if ($total_preguntas == 0) {
        die("No hay preguntas disponibles para este examen.");
    }

    $indice_actual = isset($_GET['indice']) ? intval($_GET['indice']) : 0;
    if ($indice_actual < 0 || $indice_actual >= $total_preguntas) {
        die("Índice de pregunta inválido.");
    }

    $pregunta_actual = $preguntas[$indice_actual];
    $sqlOpciones = "SELECT id, respuesta FROM respuestas WHERE pregunta_id = :pregunta_id";
    $stmtOpciones = $pdo->prepare($sqlOpciones);
    $stmtOpciones->execute([':pregunta_id' => $pregunta_actual['id']]);
    $opciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);

    // Guardar respuestas temporales en la sesión
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuesta'])) {
        $_SESSION['respuestas'][$pregunta_actual['id']] = $_POST['respuesta']; // Guardar la respuesta en la sesión
    }

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Examen</title>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        padding: 20px;
    }
    h1 {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    form {
        background: white;
        padding: 20px;
        margin: auto;
        max-width: 600px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    button {
        font-size: 16px;
        padding: 10px 20px;
        margin: 10px 0;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    button:hover {
        background-color: #218838;
    }

    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($examen['nombre']); ?></h1>
    <p>Duración: <?php echo htmlspecialchars($examen['duracion']); ?> minutos</p>
    <p>Disponible desde <?php echo htmlspecialchars($examen['fecha_inicio']); ?> hasta <?php echo htmlspecialchars($examen['fecha_fin']); ?></p>

    <form method="POST">
        <div class="pregunta">
            <strong><?php echo htmlspecialchars($pregunta_actual['pregunta']); ?></strong><br>
            <?php foreach ($opciones as $opcion): ?>
                <div class="opcion">
                    <input type="radio" name="respuesta" 
                           value="<?php echo $opcion['id']; ?>" 
                           required
                        <?php 
                            // Verifica si esta opción fue seleccionada previamente
                            // Asegúrate de que la respuesta guardada en la sesión para esta pregunta sea la misma
                            echo (isset($_SESSION['respuestas'][$pregunta_actual['id']]) && $_SESSION['respuestas'][$pregunta_actual['id']] == $opcion['id']) ? 'checked' : ''; 
                        ?>>
                    <?php echo htmlspecialchars($opcion['respuesta']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div>
            <?php if ($indice_actual > 0): ?>
                <a href="?examen_id=<?php echo $examen_id; ?>&indice=<?php echo $indice_actual - 1; ?>">
                    <button type="button">Anterior</button>
                </a>
            <?php endif; ?>

            <?php if ($indice_actual < $total_preguntas - 1): ?>
                <button type="submit" formaction="?examen_id=<?php echo $examen_id; ?>&indice=<?php echo $indice_actual + 1; ?>">Siguiente</button>
            <?php else: ?>
                <input type="hidden" name="examen_id" value="<?php echo $examen_id; ?>">
                <input type="hidden" name="alumno_id" value="<?php echo $alumno_id; ?>">
                <button type="submit" formaction="guardarRespuestas.php">Finalizar</button>
            <?php endif; ?>

        </div>
    </form>
</body>
</html>



