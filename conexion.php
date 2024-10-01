<?php
// Datos de conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "banco";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
