<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectar a la base de datos
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el token desde la URL
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        // Verificar el token en la base de datos
        $sql = "SELECT * FROM usuarios WHERE token_confirmacion = :token";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            // Verificar si el token ha expirado
            if (new DateTime() < new DateTime($user['expiracion_token'])) {
                // Marcar al usuario como autenticado
                $sql = "UPDATE usuarios SET autenticado = 1, token_confirmacion = NULL, expiracion_token = NULL WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $user['id']]);
                echo "Autenticación confirmada exitosamente.";
            } else {
                echo "El enlace de confirmación ha expirado.";
            }
        } else {
            echo "El enlace de confirmación es inválido.";
        }
    } else {
        echo "Token no proporcionado.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
