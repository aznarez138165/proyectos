<?php
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";
$mensajeClase = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $ap1 = $_POST['ap1'];
    $ap2 = $_POST['ap2'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET nombre = ?, ap1 = ?, ap2 = ?, correo = ?, rol = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $ap1, $ap2, $correo, $rol, $id);

    if ($stmt->execute()) {
        $mensaje = "Usuario actualizado correctamente.";
        $mensajeClase = "success";
    } else {
        $mensaje = "Error al actualizar el usuario: " . $stmt->error;
        $mensajeClase = "error";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Usuario</title>
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
            width: 80%;
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .message {
            font-weight: bold;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modificar Usuario</h1>

        <?php if ($mensaje): ?>
            <div class="message <?= $mensajeClase ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>
            </div>
            <div class="form-group">
                <label>Primer Apellido:</label>
                <input type="text" name="ap1" value="<?= $usuario['ap1'] ?>" required>
            </div>
            <div class="form-group">
                <label>Segundo Apellido:</label>
                <input type="text" name="ap2" value="<?= $usuario['ap2'] ?>" required>
            </div>
            <div class="form-group">
                <label>Correo:</label>
                <input type="email" name="correo" value="<?= $usuario['correo'] ?>" required>
            </div>
            <div class="form-group">
                <label>Rol:</label>
                <select name="rol" required>
                    <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="profesor" <?= $usuario['rol'] == 'profesor' ? 'selected' : '' ?>>Profesor</option>
                    <option value="alumno" <?= $usuario['rol'] == 'alumno' ? 'selected' : '' ?>>Alumno</option>
                </select>
            </div>
            <button type="submit" class="button">Guardar Cambios</button>
        </form>
    </div>
    <a href="admin_dashboard.php" class="back-button">Volver al Panel de admin</a>
</body>
</html>
