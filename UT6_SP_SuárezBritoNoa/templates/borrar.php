<?php
// Inicia la sesión para gestionar las variables de sesión.
session_start();

// Incluye el archivo de conexión a la base de datos.
require_once '../include/conexion.php';

// Verifica si el usuario tiene permisos de administrador.
// Si no es admin, redirige al listado de productos.
if ($_SESSION['usuario'] !== 'admin') {
    header("Location: listado.php");
    exit; // Detiene la ejecución del script.
}

// Obtiene el ID del producto que se desea eliminar desde la URL (GET).
$id = $_GET['id'] ?? null; // Si no se proporciona un ID, la variable será null.

if ($id) { // Verifica que el ID no sea null antes de continuar.
    try {
        // Verifica si el producto existe antes de intentar eliminarlo.
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch(); // Obtiene el producto si existe.

        if ($producto) { // Si el producto existe, procede con la eliminación.
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->execute(['id' => $id]); // Ejecuta la eliminación en la base de datos.
        }
    } catch (PDOException $e) {
        // Captura y registra cualquier error que ocurra durante la eliminación.
        error_log("Error al eliminar producto: " . $e->getMessage());
    }
}

// Redirige de vuelta a la página de listado después de la eliminación.
header("Location: listado.php");
exit; // Termina el script para evitar cualquier ejecución adicional.
?>
