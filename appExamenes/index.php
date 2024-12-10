<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        form {
            display: inline-block;
            text-align: left;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }
        button {
            width: 100%;
            padding: 14px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            transition: background 0.3s, transform 0.2s;
        }
        button:hover {
            background: linear-gradient(135deg, #0056b3, #004494);
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <h1>Bienvenido a la plataforma de exámenes</h1>
    <p>Inicie sesión para continuar:</p>

    <form action="authenticate.php" method="POST">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>
