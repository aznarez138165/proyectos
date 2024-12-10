<?php
// Configuración de la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la lista de preguntas abiertas
    $sqlPreguntas = "SELECT id, pregunta FROM preguntas_abiertas";
    $stmtPreguntas = $pdo->query($sqlPreguntas);
    $preguntas = $stmtPreguntas->fetchAll();

    // Mostrar las respuestas para una pregunta seleccionada
    if (isset($_GET['pregunta_id']) && is_numeric($_GET['pregunta_id'])) {
        $pregunta_id = intval($_GET['pregunta_id']);

        // Obtener las respuestas de los alumnos a la pregunta seleccionada
        $sqlRespuestas = "
            SELECT r.id AS respuesta_id, u.nombre AS alumno_nombre, r.respuesta 
            FROM respuestas_abiertas_usuario r
            JOIN usuarios u ON r.alumno_id = u.id
            WHERE r.pregunta_id = :pregunta_id";
        
        $stmtRespuestas = $pdo->prepare($sqlRespuestas);
        $stmtRespuestas->execute([':pregunta_id' => $pregunta_id]);
        $respuestas = $stmtRespuestas->fetchAll();
    }

    // Guardar las puntuaciones si el formulario es enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $respuesta_id = $_POST['respuesta_id'];
        $puntuacion = $_POST['puntuacion'];

        // Actualizar la puntuación en la base de datos
        $sqlPuntuacion = "UPDATE respuestas_abiertas_usuario SET puntuacion = :puntuacion WHERE id = :respuesta_id";
        $stmtPuntuacion = $pdo->prepare($sqlPuntuacion);
        $stmtPuntuacion->execute([
            ':puntuacion' => $puntuacion,
            ':respuesta_id' => $respuesta_id
        ]);

        // Mensaje de éxito
        echo "<p>Puntuación guardada exitosamente.</p>";
    }
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corregir Respuestas Abiertas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .pregunta-list {
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .select-button {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .select-button:hover {
            background-color: #0056b3;
        }
        .submit-button {
            padding: 5px 10px;
            font-size: 14px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Corregir Respuestas Abiertas</h1>

    <!-- Lista de preguntas abiertas -->
    <div class="pregunta-list">
        <h2>Selecciona una pregunta para corregir</h2>
        <?php foreach ($preguntas as $pregunta): ?>
            <a class="select-button" href="corregirRespuestasAbiertas.php?pregunta_id=<?php echo $pregunta['id']; ?>">
                <?php echo htmlspecialchars($pregunta['pregunta']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Mostrar respuestas para una pregunta seleccionada -->
    <?php if (isset($pregunta_id) && count($respuestas) > 0): ?>
        <h2>Respuestas para la pregunta seleccionada</h2>
        <table>
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Respuesta</th>
                    <th>Puntuación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($respuestas as $respuesta): ?>
                    <form method="POST">
                        <tr>
                            <td><?php echo htmlspecialchars($respuesta['alumno_nombre']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($respuesta['respuesta'])); ?></td>
                            <td><input type="number" name="puntuacion" value="0" min="0" max="10" step="0.1"></td>
                            <td>
                                <input type="hidden" name="respuesta_id" value="<?php echo $respuesta['respuesta_id']; ?>">
                                <button type="submit" class="submit-button">Guardar Puntuación</button>
                            </td>
                        </tr>
                    </form>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($pregunta_id)): ?>
        <p>No hay respuestas para esta pregunta.</p>
    <?php endif; ?>

</body>
</html>

