<?php
// Configuración de la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

// Conectar a la base de datos usando mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar y obtener el ID del alumno de forma segura
if (isset($_GET['alumno_id']) && is_numeric($_GET['alumno_id'])) {
    $alumno_id = intval($_GET['alumno_id']);
} else {
    die("ID de alumno no válido.");
}

// Consulta segura para obtener los exámenes asignados al alumno sin duplicados
$sql = "SELECT DISTINCT examenes.id, examenes.nombre 
        FROM examen_alumno 
        JOIN examenes ON examen_alumno.examen_id = examenes.id 
        WHERE examen_alumno.alumno_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $alumno_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Exámenes</title>
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
            font-size: 2rem;
            margin-bottom: 20px;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 800px;
            background: white;
            color: #333;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f4f4f4;
        }
        .button {
            text-decoration: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: bold;
            display: inline-block;
        }
        .start-button {
            background-color: #28a745;
        }
        .start-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .start-button:active {
            transform: translateY(0);
        }
        @media (max-width: 768px) {
            table {
                width: 100%;
                font-size: 0.9rem;
            }
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Exámenes Disponibles para el Alumno</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Nombre del Examen</th>
                <th>Acción</th>
            </tr>
            <?php while ($examen = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($examen['nombre']); ?></td>
                    <td>
                        <a class="button start-button" href="realizarExamen.php?examen_id=<?php echo htmlspecialchars($examen['id']); ?>">
                            Realizar Examen
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tienes exámenes asignados.</p>
    <?php endif; ?>

</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>

