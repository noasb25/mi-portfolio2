<?php
// Inicia la sesión para gestionar las variables de sesión.
session_start();

// Incluye el archivo de conexión a la base de datos.
require_once '../include/conexion.php';

// Verificar si el usuario es admin. Si no es admin, redirige a listado.php.
if ($_SESSION['usuario'] !== 'admin') {
    header("Location: listado.php");
    exit; // Detiene la ejecución del script.
}

// Variable para almacenar mensajes de error o éxito.
$mensaje = '';

// Obtiene el ID del producto desde la URL (GET), si no está definido, se asigna null.
$id = $_GET['id'] ?? null;

// Si no se proporciona un ID válido, redirige al listado de productos.
if (!$id) {
    header("Location: listado.php");
    exit;
}

try {
    // Consulta SQL para obtener los datos del producto según su ID.
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $producto = $stmt->fetch();

    // Si el producto no existe, redirige al listado de productos.
    if (!$producto) {
        header("Location: listado.php");
        exit;
    }
} catch (PDOException $e) {
    // Manejo de errores si falla la consulta a la base de datos.
    error_log("Error al obtener producto: " . $e->getMessage());
    exit;
}

// Si el formulario se ha enviado por método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario.
    $nombre = trim($_POST['nombre'] ?? ''); // Nombre del producto.
    $descripcion = trim($_POST['descripcion'] ?? ''); // Descripción del producto.
    $precio = trim($_POST['precio'] ?? ''); // Precio del producto.
    $imagen = $_FILES['imagen']['name'] ?? ''; // Nombre del archivo de imagen subido.

    // Validaciones de los campos.
    if (empty($nombre) || empty($descripcion) || empty($precio)) {
        // Se requiere que todos los campos estén llenos, excepto la imagen.
        $mensaje = 'Todos los campos son obligatorios, excepto la imagen si no deseas cambiarla.';
    } elseif (!is_numeric($precio) || $precio <= 0) {
        // Verifica que el precio sea un número positivo.
        $mensaje = 'El precio debe ser un número positivo.';
    } elseif (!empty($imagen) && !in_array(pathinfo($imagen, PATHINFO_EXTENSION), ['jpg', 'png', 'jpeg'])) {
        // Si se sube una imagen, verifica que tenga una extensión válida.
        $mensaje = 'El archivo debe ser una imagen válida (jpg, png, jpeg).';
    } else {
        // Si el usuario subió una nueva imagen.
        if (!empty($imagen)) {
            $rutaDestino = '../img/' . basename($imagen);
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                // Si la imagen se subió correctamente, se usa la nueva imagen.
                $imagenFinal = $imagen;
            } else {
                // Si hay un error al subir la imagen, se mantiene la imagen anterior.
                $mensaje = 'Error al subir la nueva imagen.';
                $imagenFinal = $producto['imagen'];
            }
        } else {
            // Si no se sube una nueva imagen, se mantiene la imagen anterior.
            $imagenFinal = $producto['imagen'];
        }

        try {
            // Consulta SQL para actualizar los datos del producto en la base de datos.
            $stmt = $pdo->prepare("UPDATE productos 
                                   SET nombre = :nombre, descripcion = :descripcion, precio = :precio, imagen = :imagen 
                                   WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'imagen' => $imagenFinal,
                'id' => $id,
            ]);
            // Mensaje de éxito si el producto se actualizó correctamente.
            $mensaje = 'Producto actualizado exitosamente.';
        } catch (PDOException $e) {
            // Manejo de errores en caso de fallo en la base de datos.
            error_log("Error al actualizar producto: " . $e->getMessage());
            $mensaje = 'Error al actualizar el producto.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class="index-body"> <!-- Clase especial para centrar el contenedor -->
    <div class="container">
        <h1>Actualizar Producto</h1>

        <!-- Muestra un mensaje de error o éxito si existe -->
        <?php if ($mensaje): ?>
            <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Formulario para actualizar los datos del producto -->
        <form method="POST" action="update.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>
            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" style="width: 97%; height: 35px; background-color: white; color: black; border-radius: 4px; margin-bottom: 15px; font-family: Arial, sans-serif;"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div>
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>">
            </div>
            <div>
                <label for="imagen">Imagen (deja vacío para no cambiar):</label>
                <input type="file" id="imagen" name="imagen">
            </div>

            <!-- Contenedor de botones alineados en paralelo -->
            <div class="botones-container">
                <button type="submit">Actualizar Producto</button>
                <a href="listado.php" class="logout-button">Volver al Listado</a>
            </div>
        </form>
    </div>
</body>
</html>
