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

// Inicializa la variable de mensaje para mostrar errores o confirmaciones.
$mensaje = '';

// Verifica si el formulario ha sido enviado mediante el método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario.
    $nombre = trim($_POST['nombre'] ?? ''); // Nombre del producto.
    $descripcion = trim($_POST['descripcion'] ?? ''); // Descripción del producto.
    $precio = trim($_POST['precio'] ?? ''); // Precio del producto.
    $imagen = $_FILES['imagen']['name'] ?? ''; // Nombre del archivo de imagen subido.

    // Validaciones del formulario
    if (empty($nombre) || empty($descripcion) || empty($precio) || empty($imagen)) {
        // Verifica que todos los campos estén completos.
        $mensaje = 'Todos los campos, incluida la imagen, son obligatorios.';
    } elseif (!is_numeric($precio) || $precio <= 0) {
        // Verifica que el precio sea un número positivo.
        $mensaje = 'El precio debe ser un número positivo.';
    } elseif (!in_array(pathinfo($imagen, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg'])) {
        // Verifica que la imagen sea un archivo válido (jpg, png, jpeg).
        $mensaje = 'El archivo debe ser una imagen válida (jpg, png, jpeg).';
    } else {
        // Ruta donde se guardará la imagen en el servidor.
        $rutaDestino = '../img/' . basename($imagen);

        // Intenta mover la imagen subida a la carpeta de imágenes.
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            try {
                // Prepara la consulta para insertar el producto en la base de datos.
                $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) 
                                       VALUES (:nombre, :descripcion, :precio, :imagen)");
                $stmt->execute([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio' => $precio,
                    'imagen' => $imagen,
                ]);
                // Mensaje de éxito si el producto se creó correctamente.
                $mensaje = 'Producto creado exitosamente.';
            } catch (PDOException $e) {
                // Manejo de errores en la base de datos.
                error_log("Error al crear producto: " . $e->getMessage());
                $mensaje = 'Error al crear el producto.';
            }
        } else {
            // Mensaje de error si la imagen no se pudo subir.
            $mensaje = 'Error al subir la imagen.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="../css/estilos.css"> 
</head>
<body class="index-body"> <!-- Clase especial para centrar el contenedor del formulario -->
    <div class="container"> <!-- Contenedor principal del formulario -->
        <h1>Crear Producto</h1>

        <!-- Muestra un mensaje de error o éxito si existe -->
        <?php if ($mensaje): ?>
            <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Formulario para la creación de productos -->
        <form method="POST" action="crear.php" enctype="multipart/form-data">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre">
            </div>
            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" style="width: 97%; height: 35px; background-color: white; color: black; border-radius: 4px; margin-bottom: 15px;"></textarea>
            </div>
            <div>
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01">
            </div>
            <div>
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen">
            </div>

            <!-- Contenedor de botones alineados en paralelo -->
            <div class="botones-container">
                <button type="submit">Crear Producto</button>
                <a href="listado.php" class="logout-button">Volver al Listado</a>
            </div>
        </form>
    </div>
</body>
</html>
