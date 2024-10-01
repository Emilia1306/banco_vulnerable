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

    // Verificar si el usuario tiene rol de administrador
    if ($decoded->rol != 'admin') {
        throw new Exception("Acceso denegado. No tienes permisos de administrador.");
    }

    // Consulta para obtener todos los usuarios de la base de datos
    $query = "SELECT id, correo, rol FROM usuarios";
    $result = $conexion->query($query);

} catch (Exception $e) {
    // Si ocurre un error, redirigir al inicio de sesión o mostrar un mensaje de error
    echo "Error en la autenticación: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
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

        h2 {
            color: #0056b3;
            font-size: 2.5em;
            margin-bottom: 30px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        th {
            background-color: #f5f5f5;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        td {
            text-align: center;
        }

        .btn {
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

        .btn:hover {
            background-color: #003d7a;
        }

    </style>
</head>
<body>
    <h2>Lista de usuarios</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Correo</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['correo']; ?></td>
                        <td><?php echo ucfirst($usuario['rol']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
    
    <br>
    <a href="dashboard_admin.php" class="btn">Volver al dashboard</a><br>
</body>
</html>

