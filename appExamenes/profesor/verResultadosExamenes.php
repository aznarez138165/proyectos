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

    // Obtener la lista de exámenes
    $sqlExamenes = "SELECT id, nombre FROM examenes";
    $stmtExamenes = $pdo->query($sqlExamenes);
    $examenes = $stmtExamenes->fetchAll();

    // Obtener el examen seleccionado y cargar los resultados
    $resultados = [];
    if (isset($_GET['examen_id']) && is_numeric($_GET['examen_id'])) {
        $examen_id = intval($_GET['examen_id']);
        
        // Consulta para obtener el promedio de notas de cada alumno y escalarlo a 10 puntos
        $sqlResultados = "
            SELECT a.nombre AS alumno_nombre, ROUND((SUM(r.nota) / COUNT(r.nota)) * 10 / MAX(r.nota), 2) AS nota_total
            FROM respuestas_usuario r
            JOIN usuarios a ON r.alumno_id = a.id
            WHERE r.examen_id = :examen_id
            GROUP BY r.alumno_id";
        
        $stmtResultados = $pdo->prepare($sqlResultados);
        $stmtResultados->execute([':examen_id' => $examen_id]);
        $resultados = $stmtResultados->fetchAll();
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
    <title>Resultados de Exámenes</title>
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
        .examenes-list {
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
    </style>
</head>
<body>
    <h1>Resultados de Exámenes</h1>

    <!-- Lista de exámenes para seleccionar -->
    <div class="examenes-list">
        <h2>Selecciona un examen</h2>
        <?php foreach ($examenes as $examen): ?>
            <a class="select-button" href="verResultadosExamenes.php?examen_id=<?php echo $examen['id']; ?>">
                <?php echo htmlspecialchars($examen['nombre']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Mostrar los resultados del examen seleccionado -->
    <?php if (isset($examen_id) && count($resultados) > 0): ?>
        <h2>Resultados para el examen seleccionado</h2>
        <table>
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Nota Total (sobre 10)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $resultado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($resultado['alumno_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($resultado['nota_total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($examen_id)): ?>
        <p>No hay resultados para este examen.</p>
    <?php endif; ?>
    <a href="profesor_dashboard.php" class="back-button">Volver al Panel de Profesor</a>
</body>
</html>
