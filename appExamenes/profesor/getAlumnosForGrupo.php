<?php
$host = 'sql104.infinityfree.com';
$dbname = 'if0_37518630_gpi1_nashe';
$username = 'if0_37518630';
$password = 'ubbfyRR3N8dd';

// Crear conexión a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha recibido el ID del grupo
if (isset($_GET['grupo_id']) && is_numeric($_GET['grupo_id'])) {
    $grupo_id = $_GET['grupo_id'];

    // Preparar la consulta SQL para obtener los alumnos del grupo
    $sql = "SELECT u.id, u.nombre, u.ap1, u.ap2 
            FROM usuarios u
            JOIN grupo_alumno ga ON u.id = ga.alumno_id
            WHERE ga.grupo_id = ? AND u.rol = 'alumno'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un array para almacenar los datos de los alumnos
    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'ap1' => $row['ap1'],
            'ap2' => $row['ap2']
        ];
    }

    // Devolver los resultados en formato JSON
    echo json_encode($alumnos);

    $stmt->close();
} else {
    // Si no se ha recibido el grupo_id o es inválido
    echo json_encode(['error' => 'Grupo no válido']);
}

// Cerrar la conexión
$conn->close();
?>
