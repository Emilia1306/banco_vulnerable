<?php
// logout.php

// Destruir la cookie del token
setcookie("token", "", time() - 3600, "/"); // Expira la cookie

// Redirigir al usuario al inicio de sesión o página principal
header("Location: index.php");
exit;
?>
