<?php
// Inicia la sesión para acceder a las variables de sesión del usuario.
session_start();

// Destruir la sesión
session_unset(); // Limpiar variables de sesión
session_destroy(); // Destruir la sesión

// Redirigir al login
header("Location: ../index.php");
exit;
?>
