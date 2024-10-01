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

    // Obtener el usuario_id del token decodificado
    $usuario_id = $decoded->usuario_id;

    // Obtener los datos actuales del usuario
    $query = "SELECT correo FROM usuarios WHERE id = $usuario_id";
    $result = $conexion->query($query);
    $usuario = $result->fetch_assoc();

    // Si se ha enviado el formulario para actualizar correo y contraseña
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nuevo_correo = $_POST['correo'];
        $nueva_contrasena = $_POST['password'];

        // Actualizar los datos del usuario en la base de datos
        $conexion->query("UPDATE usuarios SET correo = '$nuevo_correo', password = '$nueva_contrasena' WHERE id = $usuario_id");
        echo "Cambios guardados correctamente.";
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
    <title>Editar Perfil</title>
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
            flex-direction: row;
            justify-content: center;
            gap: 30px;
            width: 80%;
        }

        label {
            font-size: 1.2em;
            color: #333;
        }

        input[type="text"], input[type="password"] {
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
    <h2>Editar Perfil</h2>
    <p>Tu correo electrónico <?php echo $usuario['correo']; ?> actualmente está vigente :)</p>
    <form method="POST">
        <div>
            <label for="correo">Correo</label><br>
            <input type="text" name="correo" value="<?php echo $usuario['correo']; ?>" required>
        </div>
        <div>
            <label for="password">Contraseña</label><br>
            <input type="password" name="password" value="<?php echo $usuario['correo']; ?>" required>
        </div>
        <div class="btn-container">
            <a href="dashboard_usuario.php" class="btn btn-primary">Volver al dashboard</a>
            <input type="submit" value="Guardar Cambios" class="btn btn-secondary">
        </div>
    </form>
</body>
</html>
