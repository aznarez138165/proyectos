<?php
session_start();

// Verificar si se ha proporcionado la nota
if (!isset($_GET['nota'])) {
    die("No se ha proporcionado la nota.");
}

// Obtener la nota y sanitizarla
$nota = htmlspecialchars($_GET['nota']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Obtenida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin-top: 50px;
        }
        h1 {
            color: #333;
        }
        .back-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            text-decoration: none;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>¡Examen Completado!</h1>
    <p>Su nota es: <strong><?php echo number_format($nota, 2); ?></strong></p>
    <a href="alumno_dashboard.php" class="back-button">Volver al Panel de Alumno</a>
</body>
</html>

