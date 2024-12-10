<?php
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id, nombre, ap1, ap2, correo, rol FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin-top: 50px;
        }
        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .button {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .modify-button { background-color: #28a745; }
        .modify-button:hover { background-color: #218838; }
        .delete-button { background-color: #dc3545; }
        .delete-button:hover { background-color: #c82333; }
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
    <h1>Gestión de Usuarios</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido 1</th>
            <th>Apellido 2</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Modificar</th>
            <th>Eliminar</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nombre'] ?></td>
                <td><?= $row['ap1'] ?></td>
                <td><?= $row['ap2'] ?></td>
                <td><?= $row['correo'] ?></td>
                <td><?= $row['rol'] ?></td>
                <td><a href="modificarUsuario.php?id=<?= $row['id'] ?>" class="button modify-button">Modificar</a></td>
                <td><a href="#" class="button delete-button" onclick="confirmarEliminacion(<?= $row['id'] ?>)">Eliminar</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="back-button">Volver al Panel de admin</a>


    <script>
        function confirmarEliminacion(id) {
            if (confirm("¿Está seguro de que desea eliminar este usuario?")) {
                window.location.href = "eliminarUsuario.php?id=" + id;
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
