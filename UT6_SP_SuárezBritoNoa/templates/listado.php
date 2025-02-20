<?php
// Inicia la sesión para acceder a las variables de sesión.
session_start();

// Incluye el archivo de conexión a la base de datos.
require_once '../include/conexion.php';

// Verifica si el usuario está logueado. Si no está logueado, redirige al index.php.
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit; // Detiene la ejecución del script.
}

// Intenta obtener todos los productos desde la base de datos.
try {
    // Prepara la consulta SQL para seleccionar todos los productos.
    $stmt = $pdo->prepare("SELECT * FROM productos");
    $stmt->execute(); // Ejecuta la consulta.
    $productos = $stmt->fetchAll(); // Obtiene los resultados como un array.
} catch (PDOException $e) {
    // Si hay un error al obtener los productos, lo registra en el log de errores.
    error_log("Error al obtener productos: " . $e->getMessage());
    // Muestra un mensaje genérico al usuario.
    echo "No se pudieron cargar los productos. Intente más tarde.";
    exit; // Detiene la ejecución del script.
}

// Obtiene el nombre del usuario desde la sesión y asigna la imagen correspondiente.
$usuario = htmlspecialchars($_SESSION['usuario']); // Escapa caracteres especiales para evitar XSS.
$imagenUsuario = ''; // Variable para almacenar la ruta de la imagen del usuario.

switch ($usuario) {
    // Asigna una imagen específica según el usuario logueado.
    case 'admin':
        $imagenUsuario = 'user_admin.png';
        break;
    case 'ana':
        $imagenUsuario = 'user_ana.png';
        break;
    case 'juan':
        $imagenUsuario = 'user_juan.png';
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <!-- Muestra la imagen del usuario logueado -->
        <img src="../img/<?php echo $imagenUsuario; ?>" alt="Usuario" class="user-icon">
        <!-- Muestra el nombre del usuario logueado -->
        <span class="user-name">Bienvenido, <?php echo $usuario; ?></span>
        <!-- Enlace para cerrar sesión -->
        <a href="../templates/logout.php" class="logout-button">Salir</a>
    </div>

    <div class="container">
        <h1>Listado de Productos</h1>

        <?php if ($usuario === 'admin'): ?>
        <!-- Opciones de administración para el usuario admin -->
        <div class="admin-options">
            <a href="crear.php" class="btn-admin">Crear Producto</a>
        </div>
        <?php endif; ?>

        <!-- Tabla para mostrar los productos -->
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Valoración</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Itera sobre cada producto obtenido de la base de datos -->
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <!-- Muestra el ID del producto -->
                        <td><?php echo $producto['id']; ?></td>
                        <!-- Muestra la imagen del producto -->
                        <td>
                            <img src="../img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                 class="product-image" onclick="openModal(this)">
                        </td>
                        <!-- Muestra el nombre del producto -->
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <!-- Muestra la descripción del producto -->
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <!-- Muestra el precio del producto formateado -->
                        <td><?php echo number_format($producto['precio'], 2); ?> €</td>
                        <!-- Muestra la valoración del producto -->
                        <td id="valoracion-<?php echo $producto['id']; ?>">
                            <?php
                            try {
                                // Consulta para calcular la valoración promedio y el número de votos del producto.
                                $stmtValoracion = $pdo->prepare("SELECT AVG(cantidad) AS media, COUNT(*) AS votos FROM votos WHERE idPr = :idPr");
                                $stmtValoracion->execute(['idPr' => $producto['id']]);
                                $valoracion = $stmtValoracion->fetch();

                                if ($valoracion['votos'] > 0) {
                                    // Calcula y muestra las estrellas según la media.
                                    $media = round($valoracion['media'], 1);
                                    $estrellasLlenas = floor($media);
                                    $mediaRestante = $media - $estrellasLlenas;
                                    $mediaEstrella = $mediaRestante >= 0.5 ? 1 : 0;
                                    $estrellasVacias = 5 - ($estrellasLlenas + $mediaEstrella);

                                    for ($i = 0; $i < $estrellasLlenas; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }
                                    if ($mediaEstrella) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    }
                                    for ($i = 0; $i < $estrellasVacias; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }

                                    // Muestra el texto con el número de valoraciones.
                                    $textoValoraciones = $valoracion['votos'] === 1 ? '1 valoración' : "{$valoracion['votos']} valoraciones";
                                    echo " <span class='rating-text'>($textoValoraciones)</span>";
                                } else {
                                    echo "Sin valorar"; // Si no hay valoraciones.
                                }
                            } catch (PDOException $e) {
                                // Maneja errores al calcular la valoración.
                                error_log("Error al calcular la valoración: " . $e->getMessage());
                                echo "Error en valoración";
                            }
                            ?>
                        </td>
                        <!-- Botones para valorar y acciones del admin -->
                        <td>
                            <?php if ($usuario === 'admin'): ?>
                                <div class="botones-container">
                                    <a href="update.php?id=<?php echo $producto['id']; ?>" class="btn-admin">Editar</a>
                                    <a href="borrar.php?id=<?php echo $producto['id']; ?>" class="btn-admin" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Borrar</a>
                                </div>
                            <?php else: ?>
                                <div class="valorar-container">
                                    <select class="select-valoracion" data-id="<?php echo $producto['id']; ?>">
                                        <option value="">Selecciona</option>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> estrella<?php echo $i > 1 ? 's' : ''; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button class="btn-valorar" data-id="<?php echo $producto['id']; ?>">Valorar</button>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para mostrar imágenes ampliadas -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Imagen ampliada">
    </div>

    <script src="../js/valoraciones.js"></script>
    <script>
        // Funciones para el modal
        function openModal(image) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modal.style.opacity = '1';
            modalImage.src = image.src;
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    </script>
</body>
</html>
