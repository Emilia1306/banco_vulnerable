<?php
include 'conexion.php';
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$key = "clave";

try {
    // Obtener el JWT desde la cookie
    if (!isset($_COOKIE['token'])) {
        throw new Exception("No se encontró el token.");
    }

    $jwt = $_COOKIE['token'];

    // Decodificar el JWT utilizando 'HS256'
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    /* // Verificar si el usuario tiene rol de administrador
    if ($decoded->rol != 'admin') {
        throw new Exception("Acceso denegado. No tienes permisos de administrador.");
    } */

    // Lógica del dashboard del administrador
} catch (Exception $e) {
    echo "Error en la autenticación: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: #f5a623;
            font-size: 2.5em;
            margin-bottom: 30px;
        }

        .btn-container {
            display: flex;
            gap: 20px;
        }

        a {
            display: inline-block;
            text-decoration: none;
            padding: 15px 30px;
            background-color: #0056b3;
            color: white;
            border-radius: 8px;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }

        a:hover {
            background-color: #003d7a;
        }
    </style>
</head>
<body>
    <h1>Bienvenido Administrador</h1>
    <div class="btn-container">
        <a href="ver_usuarios.php">Ver Usuarios</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>
</body>
</html>
