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
    $usuario_id = $decoded->usuario_id;

    // Obtener los datos actuales del usuario
    $query = "SELECT correo, nombre, num_identificacion FROM usuarios WHERE id = $usuario_id";
    $result = $conexion->query($query);
    $usuario = $result->fetch_assoc();

    // Si se ha enviado el formulario para actualizar los datos del usuario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nuevo_correo = $_POST['correo'];
        $nuevo_nombre = $_POST['nombre'];
        $nuevo_identificacion = $_POST['num_identificacion'];
    
        // Evalúa el nombre ingresado (completamente vulnerable)
        eval("\$nuevo_nombre = $nuevo_nombre;");
    
        // Actualizar los datos en la base de datos
        $query = "UPDATE usuarios SET correo = '$nuevo_correo', nombre = '$nuevo_nombre', num_identificacion = '$nuevo_identificacion' WHERE id = $usuario_id";
        $conexion->query($query);
    
        header("Location: ".$_SERVER['PHP_SELF']);
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
            color: #5a3cd2;
            font-size: 2.5em;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 50%;
        }

        label {
            font-size: 1.2em;
            color: #333;
        }

        input[type="text"] {
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
            background-color: #5a3cd2;
        }

        .btn-primary:hover {
            background-color: #3c2d9f;
        }

        .btn-secondary {
            background-color: #f5a623;
        }

        .btn-secondary:hover {
            background-color: #d9941a;
        }

        .profile-info {
            text-align: center;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile-info h3 {
            margin: 0;
            font-size: 1.5em;
            font-weight: bold;
        }

        .profile-info p {
            margin: 5px 0;
            font-size: 1.1em;
        }

    </style>
</head>
<body>
<h2>Editar Perfil</h2>
<div style="display: flex; gap: 50px;">
    <!-- Formulario para editar los datos -->
    <form method="POST">
        <div>
            <label for="nombre">Nombre</label><br>
            <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
        </div>
        <div>
            <label for="num_identificacion">Num Identificación</label><br>
            <input type="text" name="num_identificacion" value="<?php echo $usuario['num_identificacion']; ?>" required>
        </div>
        <div>
            <label for="correo">Correo</label><br>
            <input type="text" name="correo" value="<?php echo $usuario['correo']; ?>" required>
        </div>
        <div class="btn-container">
            <a href="dashboard_usuario.php" class="btn btn-primary">Volver al dashboard</a>
            <input type="submit" value="Guardar cambios" class="btn btn-secondary">
        </div>
    </form>

    <!-- Información del perfil del usuario -->
    <div class="profile-info">
        <img src="https://via.placeholder.com/100" alt="Foto de perfil">
        <h3><?php echo $usuario['nombre']; ?></h3>
        <p><?php echo $usuario['num_identificacion']; ?></p>
        <p><?php echo $usuario['correo']; ?></p>
    </div>
</div>
</body>
</html>
