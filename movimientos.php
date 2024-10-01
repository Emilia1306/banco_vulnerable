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

    // Decodificar el JWT (usando el algoritmo HS256)
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

    // Obtener el usuario_id del token decodificado
    $usuario_id = $decoded->usuario_id;

    // Consultar los movimientos del usuario
    $query = "SELECT * FROM movimientos WHERE usuario_id = $usuario_id ORDER BY fecha DESC";
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
    <title>Historial de Movimientos</title>
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

        table, th, td {
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f5f5f5;
            color: #0056b3;
            font-size: 1.2em;
        }

        td {
            font-size: 1.1em;
        }

        .btn {
            text-decoration: none;
            padding: 15px 30px;
            background-color: #0056b3;
            color: white;
            border-radius: 8px;
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #003d7a;
        }

    </style>
</head>
<body>
    <h2>Historial de movimientos</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cuenta origen</th>
                    <th>Cuenta destino</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($movimiento = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $movimiento['fecha']; ?></td>
                        <td><?php echo $movimiento['cuenta_origen']; ?></td>
                        <td><?php echo $movimiento['cuenta_destino']; ?></td>
                        <td>$<?php echo number_format($movimiento['monto'], 2); ?></td>
                        <td><?php echo $movimiento['descripcion']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se han encontrado movimientos.</p>
    <?php endif; ?>
    
    <a href="dashboard_usuario.php" class="btn">Volver al dashboard</a>
</body>
</html>
