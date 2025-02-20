<?php
// Inicia la sesión para acceder a las variables de sesión del usuario.
session_start();

// Incluye el archivo de conexión a la base de datos.
require_once '../include/conexion.php';

// Verifica si el usuario está logueado. Si no lo está, devuelve un error en formato JSON.
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Debes iniciar sesión para valorar.']); // Devuelve un mensaje de error.
    exit; // Detiene la ejecución del script.
}

// Lee los datos enviados mediante una solicitud AJAX en formato JSON.
$input = json_decode(file_get_contents('php://input'), true); // Decodifica el JSON recibido.
$idProducto = $input['idProducto'] ?? null; // Obtiene el ID del producto o asigna `null` si no está presente.
$valor = $input['valor'] ?? null; // Obtiene la valoración o asigna `null` si no está presente.
$usuario = $_SESSION['usuario']; // Obtiene el nombre de usuario de la sesión.

// Verifica si faltan datos obligatorios (ID del producto o valoración).
if (!$idProducto || !$valor) {
    echo json_encode(['error' => 'Datos incompletos.']); // Devuelve un mensaje de error.
    exit; // Detiene la ejecución del script.
}

// Intenta validar y registrar la valoración en la base de datos.
try {
    // Consulta para verificar si el usuario ya valoró este producto.
    $stmt = $pdo->prepare("SELECT * FROM votos WHERE idPr = :idPr AND idUs = :idUs");
    $stmt->execute(['idPr' => $idProducto, 'idUs' => $usuario]); // Ejecuta la consulta con los parámetros.
    $voto = $stmt->fetch(); // Obtiene el resultado.

    if ($voto) {
        // Si ya existe un voto para este producto y usuario, devuelve un error en formato JSON.
        echo json_encode(['error' => 'Ya has valorado este producto.']);
        exit; // Detiene la ejecución del script.
    }

    // Inserta un nuevo registro en la tabla `votos` con la valoración del usuario.
    $stmt = $pdo->prepare("INSERT INTO votos (cantidad, idPr, idUs) VALUES (:cantidad, :idPr, :idUs)");
    $stmt->execute([
        'cantidad' => $valor, // La valoración seleccionada por el usuario.
        'idPr' => $idProducto, // El ID del producto valorado.
        'idUs' => $usuario, // El nombre del usuario que realizó la valoración.
    ]);

    // Calcula la nueva valoración promedio y el número total de votos para el producto.
    $stmt = $pdo->prepare("SELECT AVG(cantidad) AS media, COUNT(*) AS votos FROM votos WHERE idPr = :idPr");
    $stmt->execute(['idPr' => $idProducto]); // Ejecuta la consulta con el ID del producto.
    $valoracion = $stmt->fetch(); // Obtiene los resultados (media y número de votos).

    // Devuelve la nueva valoración promedio y el número de votos en formato JSON.
    echo json_encode([
        'media' => round($valoracion['media'], 1), // Redondea la media a un decimal.
        'votos' => $valoracion['votos'], // Número total de votos.
    ]);
} catch (PDOException $e) {
    // Si ocurre un error con la base de datos, registra el error en el log del servidor.
    error_log("Error al valorar producto: " . $e->getMessage());
    // Devuelve un mensaje de error en formato JSON.
    echo json_encode(['error' => 'Hubo un problema al procesar tu valoración.']);
}
?>
