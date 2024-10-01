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

    // Obtener la cuenta de origen automáticamente para el usuario logueado
    $result = $conexion->query("SELECT id, saldo FROM cuentas WHERE usuario_id = $usuario_id");
    $cuenta = $result->fetch_assoc();

    if (!$cuenta) {
        throw new Exception("No se encontró ninguna cuenta asociada a este usuario.");
    }

    $cuenta_origen = $cuenta['id']; // ID de la cuenta de origen
    $saldo_actual = $cuenta['saldo']; // Saldo actual de la cuenta

    // Si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $cuenta_destino = $_POST['cuenta_destino'];
        $monto = $_POST['monto'];

        // Verificar que el monto es válido
        if ($monto <= 0) {
            echo "<script>alert('El monto debe ser mayor a 0.');</script>";
        } elseif ($saldo_actual >= $monto) {
            // Realizar la transferencia
            $conexion->query("UPDATE cuentas SET saldo = saldo - $monto WHERE id = '$cuenta_origen'");
            $conexion->query("UPDATE cuentas SET saldo = saldo + $monto WHERE id = '$cuenta_destino'");

            // Registrar el movimiento en la tabla de movimientos
            $descripcion = "Transferencia de cuenta $cuenta_origen a cuenta $cuenta_destino";
            $conexion->query("INSERT INTO movimientos (usuario_id, cuenta_origen, cuenta_destino, monto, descripcion) 
                              VALUES ($usuario_id, $cuenta_origen, $cuenta_destino, $monto, '$descripcion')");

            echo "<script>alert('Transferencia realizada correctamente.');</script>";
        } else {
            // Si el saldo es insuficiente, se muestra una alerta
            echo "<script>alert('Fondos insuficientes. No puedes transferir más de lo que tienes en tu saldo.');</script>";
        }
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
    <title>Transferir Dinero</title>
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

        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            width: 50%;
        }

        label {
            font-size: 1.2em;
            color: #333;
        }

        input[type="text"], input[type="hidden"] {
            width: 100%;
            padding: 10px;
            font-size: 1.1em;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            text-decoration: none;
            padding: 15px 30px;
            font-size: 1.2em;
            font-weight: bold;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            color: white;
        }

        .btn-primary {
            background-color: #0056b3;
        }

        .btn-primary:hover {
            background-color: #003d7a;
        }

        .btn-secondary {
            background-color: #f5a623;
        }

        .btn-secondary:hover {
            background-color: #d9941a;
        }

    </style>
</head>
<body>
    <h2>Transferir Dinero</h2>
    <form method="POST">
        <input type="hidden" name="cuenta_origen" value="<?php echo $cuenta_origen; ?>">
        <div>
            <label for="cuenta_destino">Cuenta destino</label><br>
            <input type="text" name="cuenta_destino" required>
        </div>
        <div>
            <label for="monto">Monto</label><br>
            <input type="text" name="monto" required>
        </div>
        <div class="btn-container">
            <a href="dashboard_usuario.php" class="btn btn-primary">Volver al dashboard</a>
            <input type="submit" value="Transferir" class="btn btn-secondary">
        </div>
    </form>
</body>
</html>
