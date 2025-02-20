<?php
// Inicia la sesión para poder manejar datos relacionados con el usuario.
session_start();

// Incluye el archivo de conexión a la base de datos.
require_once 'include/conexion.php';

// Verifica si el usuario ya está logueado.
if (isset($_SESSION['usuario'])) {
    header("Location: templates/listado.php"); // Redirige a la página listado.php si el usuario ya inició sesión.
    exit; // Detiene la ejecución del script.
}

// Inicializa variables para manejar mensajes de error y datos del formulario.
$mensaje = ''; // Mensaje para mostrar errores generales (como credenciales incorrectas).
$errores = ['usuario' => '', 'contrasena' => '']; // Errores específicos para cada campo del formulario.

// Comprueba si se ha enviado el formulario con el método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene los valores enviados por el formulario, eliminando espacios en blanco al inicio y final.
    $usuario = trim($_POST['usuario'] ?? ''); // Campo de usuario.
    $contrasena = trim($_POST['contrasena'] ?? ''); // Campo de contraseña.

    // Valida si el campo de usuario está vacío.
    if (empty($usuario)) {
        $errores['usuario'] = 'El campo Usuario es obligatorio.';
    }

    // Valida si el campo de contraseña está vacío.
    if (empty($contrasena)) {
        $errores['contrasena'] = 'El campo Contraseña es obligatorio.';
    }

    // Si no hay errores en los campos, procede con la validación en la base de datos.
    if (empty($errores['usuario']) && empty($errores['contrasena'])) {
        try {
            // Prepara una consulta SQL para verificar las credenciales.
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND contrasena = MD5(:contrasena)");
            $stmt->execute([
                'usuario' => $usuario, // Sustituye el parámetro :usuario por el valor ingresado.
                'contrasena' => $contrasena, // Sustituye el parámetro :contrasena por la contraseña ingresada.
            ]);

            $user = $stmt->fetch(); // Obtiene el resultado de la consulta.

            if ($user) {
                // Si las credenciales son correctas, guarda los datos del usuario en la sesión.
                $_SESSION['usuario'] = $user['usuario']; // Guarda el nombre de usuario en la sesión.
                $_SESSION['rol'] = $user['rol']; // Guarda el rol del usuario en la sesión.
                header("Location: templates/listado.php"); // Redirige a la página listado.php.
                exit; // Detiene la ejecución del script.
            } else {
                // Si las credenciales son incorrectas, muestra un mensaje de error.
                $mensaje = "Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            // Si ocurre un error en la consulta, registra el error en el log del servidor y muestra un mensaje genérico.
            $mensaje = "Error al validar las credenciales.";
            error_log("Error en login: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Valoraciones</title>
    <link rel="stylesheet" href="css/estilos.css"> 
</head>
<body class="index-body"> <!-- Clase especial para centrar el contenedor del formulario -->
    <div class="container"> <!-- Contenedor principal -->
        <h1>Iniciar Sesión</h1>
        <?php if ($mensaje): ?>
            <!-- Muestra el mensaje general de error si existe -->
            <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
        <form method="POST" action="index.php"> <!-- Formulario para iniciar sesión -->
            <div>
                <label for="usuario">Usuario:</label> <!-- Etiqueta para el campo de usuario -->
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario ?? ''); ?>"> <!-- Campo de entrada para el usuario -->
                <?php if ($errores['usuario']): ?>
                    <!-- Muestra un mensaje de error específico si el campo de usuario está vacío -->
                    <p class="error"><?php echo htmlspecialchars($errores['usuario']); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="contrasena">Contraseña:</label> <!-- Etiqueta para el campo de contraseña -->
                <input type="password" id="contrasena" name="contrasena"> <!-- Campo de entrada para la contraseña -->
                <?php if ($errores['contrasena']): ?>
                    <!-- Muestra un mensaje de error específico si el campo de contraseña está vacío -->
                    <p class="error"><?php echo htmlspecialchars($errores['contrasena']); ?></p>
                <?php endif; ?>
            </div>
            <div style="text-align: center;"> <!-- Botón centrado -->
                <button type="submit">Iniciar Sesión</button> <!-- Botón para enviar el formulario -->
            </div>
        </form>
    </div>
</body>
</html>
