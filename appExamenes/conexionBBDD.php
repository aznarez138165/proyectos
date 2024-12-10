<?php
// Datos de conexión
$host = 'sql104.infinityfree.com';  // Nombre del servidor de base de datos
$dbname = 'if0_37518630_gpi1_nashe';  // Nombre de la base de datos
$username = 'if0_37518630';  // Usuario de la base de datos (notar que eliminé el espacio al final)
$password = 'ubbfyRR3N8dd';  // Contraseña de la base de datos

// Crear la conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} 
echo "Conexión exitosa a la base de datos '$dbname'.";
?>