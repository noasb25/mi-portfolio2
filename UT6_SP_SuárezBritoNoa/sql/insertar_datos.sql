-- Usar la base de datos
USE valoraciones;

-- Insertar datos en la tabla usuarios
INSERT INTO usuarios (usuario, contrasena, rol) VALUES
('admin', MD5('admin123'), 'admin'),
('ana', MD5('ana123'), 'cliente'),
('juan', MD5('juan123'), 'cliente');

-- Insertar datos en la tabla productos
INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES
('Bicicleta de Montaña', 'Bicicleta resistente ideal para terrenos difíciles.', 499.99, 'bicicleta_montana.jpg'),
('Cámara Reflex Canon EOS', 'Cámara profesional para fotógrafos apasionados.', 899.99, 'camara_canon_eos.jpg'),
('Smartwatch Samsung Galaxy Watch', 'Reloj inteligente con múltiples funciones y diseño elegante.', 249.99, 'smartwatch_samsung.jpg'),
('Auriculares Sony WH-1000XM4', 'Auriculares con cancelación de ruido líder en la industria.', 349.99, 'auriculares_sony.jpg'),
('Monitor Dell 27"', 'Monitor ultrafino con resolución 4K y tecnología antirreflejo.', 399.99, 'monitor_dell_27.jpg'),
('Teclado Mecánico Razer', 'Teclado gamer con retroiluminación RGB personalizable.', 149.99, 'teclado_razer.jpg'),
('Mochila de Viaje Samsonite', 'Mochila espaciosa y cómoda, ideal para viajes largos.', 129.99, 'mochila_samsonite.jpg');

-- Producto de ejemplo para crear.php
INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES
('Silla Ergonómica Gaming', 'Silla con soporte lumbar y reposabrazos ajustable, ideal para largas sesiones de juego o trabajo.', 199.99, 'silla_gaming.jpg');
