<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            text-align: center;
            margin-top: 50px;
        }
        .admin-menu {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #333;
        }
        .button {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            transition: background-color 0.3s ease;
            margin: 10px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Panel de Admin</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="insertarUsuario.html">Crear Usuario</a></li>
                <li class="nav-item"><a class="nav-link" href="listarUsuarios.php">Gestionar Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="crearGrupo.php">Crear Grupo</a></li>
                <li class="nav-item"><a class="nav-link" href="asignarGrupos.php">Asignar a Grupos</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-menu">
        <h1>Bienvenido al Panel de Administrador</h1>
        <ul>
            <li><a class="button" href="insertarUsuario.html">Crear Nuevo Usuario</a></li>
            <li><a class="button" href="listarUsuarios.php">Gestionar Usuarios</a></li>
            <li><a class="button" href="crearGrupo.php">Crear Nuevo Grupo</a></li>
            <li><a class="button" href="asignarGrupos.php">Asignar Alumnos a Grupos</a></li>
        </ul>
    </div>
</body>
</html>
