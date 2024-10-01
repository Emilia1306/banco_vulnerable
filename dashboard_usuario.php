<?php
include 'conexion.php';
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$key = "clave"; // Clave secreta utilizada para firmar el JWT

try {
    // Obtener el JWT desde la cookie
    if (!isset($_COOKIE['token'])) {
        throw new Exception("No se encontró el token.");
    }

    $jwt = $_COOKIE['token'];

    // Decodificar el JWT
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Verificar si el usuario tiene rol de usuario
    if ($decoded->rol != 'usuario') {
        throw new Exception("Acceso denegado.");
    }

    // Obtener el usuario_id del token decodificado
    $usuario_id = $decoded->usuario_id;

    // Consultar el saldo actual del usuario desde la base de datos
    $query = "SELECT saldo FROM cuentas WHERE usuario_id = $usuario_id";
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $cuenta = $result->fetch_assoc();
        $saldo = $cuenta['saldo'];
    } else {
        $saldo = 0.00; // Si no hay cuenta asociada, el saldo es 0
    }

} catch (Exception $e) {
    echo "Error en la autenticación: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Usuario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Nunito", sans-serif;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
        }

        h1 {
            color: #f5a623;
            font-size: 3em;
            margin-bottom: 10px;
        }

        .saldo-container {
            margin-bottom: 50px;
        }

        .saldo {
            font-size: 4em;
            color: black;
            margin: 0;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
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
            min-width: 200px;
        }

        a:hover {
            background-color: #003d7a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido usuario</h1>
        <div class="saldo-container">
            <p>Saldo actual</p>
            <h3 class="saldo">$<?php echo number_format($saldo, 2); ?></h3>
        </div>
        <div class="btn-container">
            <a href="editar_perfil.php">Editar Perfil</a>
            <a href="transferir.php">Realizar Transferencias</a>
            <a href="movimientos.php">Ver movimientos</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>



