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

    // Obtener los datos del formulario
    $email = $_POST['email'];
    $password = md5($_POST['password']);  // Usar md5 para la contraseña

    // Consulta a la base de datos para verificar al usuario
    $sql = "SELECT * FROM usuarios WHERE correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':correo' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $storedPassword = $user['contrasena'];  // Obtener la contraseña almacenada
        echo "Contraseña almacenada: $storedPassword<br>";

        if ($password === $storedPassword) {
            // Verificar si el usuario está autenticado o necesita confirmar su email
            if ($user['token_confirmacion'] !== null) {
                // El usuario necesita confirmar su autenticación
                echo "Por favor, confirma tu autenticación a través del enlace enviado a tu correo.";
                generarEnlaceConfirmacion($email, $pdo); // Llamamos la función aquí
                exit();
            }
            
            // Iniciar la sesión
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];

            // Obtener el rol
            $rol = $user['rol'];
            echo "Inicio de sesión exitoso como $rol.";

            // Redireccionar a la página correspondiente según el rol
            if ($rol === 'admin') {
                header('Location: /appExamenes/admin/admin_dashboard.php');
            } elseif ($rol === 'profesor') {
                header('Location: /appExamenes/profesor/profesor_dashboard.php');
            } else if ($rol === 'alumno') {
                header("Location: /appExamenes/alumno/alumno_dashboard.php?id=" . $user['id']);            
            }
            exit();
        } else {
            echo "Credenciales incorrectas o rol no válido.";
        }

    } else {
        echo "Usuario no encontrado.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


function generarEnlaceConfirmacion($email, $pdo) {
    // Generar token único
    $token = bin2hex(random_bytes(32));

    // Guardar token y su fecha de expiración (por ejemplo, 1 hora desde ahora) en la base de datos
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $query = "UPDATE usuarios SET token_confirmacion = :token, expiracion_token = :expiracion WHERE correo = :email";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':token' => $token, ':expiracion' => $expiracion, ':email' => $email]);

    // Crear enlace de confirmación
    $linkConfirmacion = "http://gpi1nashe.infinityfreeapp.com/appExamenes/confirmar.php?token=" . $token;

    // Agregar depuración para verificar el enlace
    echo "Enlace de confirmación generado: " . $linkConfirmacion . "<br>";

    // Enviar correo electrónico
    $asunto = "Confirma tu autenticación";
    $mensaje = "Haz clic en el siguiente enlace para confirmar tu autenticación: " . $linkConfirmacion;
    mail($email, $asunto, $mensaje);

    return "Correo de confirmación enviado a $email";
}

?>
