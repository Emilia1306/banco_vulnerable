<?php
include 'conexion.php';
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

$key = "clave"; // Clave secreta (no usada con 'none')

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE correo = '$correo' AND password = '$password'";
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Datos que incluirás en el JWT
        $payload = array(
            "usuario_id" => $usuario['id'],
            "rol" => $usuario['rol'],
            "exp" => time() + (60 * 60)  // Expiración en una hora
        );

        // Generar JWT sin firma (algoritmo 'none')
        $jwt = JWT::encode($payload, $key, 'HS256');
        setcookie("token", $jwt);  // Almacenar JWT en una cookie
        
        // Redirigir al dashboard adecuado
        if ($usuario['rol'] == 'admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_usuario.php");
        }
        exit;
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container h1 {
            color: #f8a12d;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .login-container label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        .login-container input[type="text"], 
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .login-container input[type="submit"] {
            background-color: #0056b3;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .login-container input[type="submit"]:hover {
            background-color: #003d80;
        }

        .labels{
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Banco</h1>
        <form method="POST" action="index.php">
            <label class="labels">Correo</label>
            <input type="text" name="correo" required>
            <label class="labels">Contraseña</label>
            <input type="password" name="password" required>
            <input type="submit" value="Iniciar Sesión">
        </form>
    </div>
</body>
</html>

