<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'valoraciones';
$username = 'root';
$password = '';

try {
    // Crear una nueva instancia de PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Configurar opciones para PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Manejar errores de conexión
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    echo "Hubo un problema al conectar con la base de datos. Intenta nuevamente más tarde.";
}
?>
