<?php
header('Content-Type: application/json');

$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

$conn = new mysqli($host, $username, $password, $dbname);

$response = [];
// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);  // Este mensaje debería aparecer si hay un problema
}

// Verificar si es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $ap1 = $_POST['ap1'];
    $ap2 = $_POST['ap2'];
    $correo = $_POST['correo'];  
    $contrasena = md5($_POST['contrasena']);  // Hash de la contraseña
    $rol = $_POST['rol'];

    error_log("Nombre: $nombre, Apellido1: $ap1, Apellido2: $ap2, Correo: $correo, Rol: $rol");

    // Comprobar si el correo ya existe
    $checkCorreoQuery = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($checkCorreoQuery);
    if ($result->num_rows > 0) {
        $response['status'] = 'error';
        $response['message'] = 'El correo ya está registrado.';
    } else {
        // Insertar el usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, ap1, ap2, correo, contrasena,rol) VALUES ('$nombre', '$ap1', '$ap2','$correo','$contrasena','$rol')";
        if ($conn->query($sql) === TRUE) {
            $response['status'] = 'success';
            $response['message'] = 'Registrado correctamente.';
        } else {
            error_log("Error en el registro: " . $conn->error); // Muestra el error en el log
            $response['status'] = 'error';
            $response['message'] = 'Error en el registro: ' . $conn->error;
        }
    }

    $conn->close();
}

echo json_encode($response);
?>
