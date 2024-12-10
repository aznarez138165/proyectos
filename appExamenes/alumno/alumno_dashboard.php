<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige al inicio de sesión si no está autenticado
    exit;
}

// Obtener el ID del alumno desde la sesión
$id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Alumno</title>
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
    </style>
</head>
<body>
    <h1>Bienvenido al Panel de Alumno</h1>

    <ul>
        <li>
            <a class="button" href="verExamenes.php?alumno_id=<?php echo $id; ?>">Ver Exámenes</a>
        </li>
        <!-- Puedes añadir más opciones aquí -->
    </ul>
</body>
</html>

