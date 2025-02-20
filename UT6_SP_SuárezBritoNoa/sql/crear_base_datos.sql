-- Crear la base de datos
CREATE DATABASE valoraciones;
USE valoraciones;

-- Tabla: usuarios
CREATE TABLE usuarios (
    usuario VARCHAR(20) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente' NOT NULL
);

-- Tabla: productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2),
    imagen VARCHAR(255) DEFAULT NULL
);

-- Tabla: votos
CREATE TABLE votos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    cantidad INT DEFAULT 0,
    idPr INT NOT NULL,
    idUs VARCHAR(20) NOT NULL,
    CONSTRAINT fk_votos_usu FOREIGN KEY(idUs) REFERENCES usuarios(usuario) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_votos_pro FOREIGN KEY(idPr) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE
);
