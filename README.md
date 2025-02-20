# ⭐ Sistema de Valoración con Estrellas

📌 **Descripción**  
Este proyecto es una plataforma web que permite a los clientes valorar productos con un sistema de puntuación de 1 a 5 estrellas.  
Incluye autenticación de usuarios y actualizaciones dinámicas en tiempo real con **Xajax**, evitando que un mismo cliente valore dos veces el mismo producto.  

---

## 🚀 Tecnologías Utilizadas
- 🖥 **Frontend:** HTML, CSS, JavaScript  
- ⚙️ **Backend:** PHP  
- 🗄 **Base de Datos:** MySQL  
- 🔄 **AJAX Dinámico:** Xajax  
- 📦 **Servidor Local:** XAMPP, Apache  

---

## 🛠 Instalación y Ejecución
Sigue estos pasos para instalar y ejecutar el proyecto en tu entorno local:

1️⃣ **Descargar y configurar el servidor**  
   - Instala [XAMPP](https://www.apachefriends.org/es/index.html) si aún no lo tienes.  
   - Asegúrate de que los módulos **Apache** y **MySQL** estén activos.  

2️⃣ **Clonar o copiar el proyecto**  
   - Coloca los archivos en el directorio del servidor web, por ejemplo:  
     ```
     C:\xampp\htdocs\UT6_SP_SuárezBritoNoa
     ```  

3️⃣ **Configurar la base de datos**  
   - Abre **phpMyAdmin** y crea la base de datos ejecutando el siguiente comando SQL:  
     ```sql
     CREATE DATABASE valoraciones;
     USE valoraciones;
     ```
   - Importa el archivo `.sql` incluido en el proyecto para crear las tablas necesarias.  

4️⃣ **Configurar la conexión a la base de datos**  
   - Edita los archivos de configuración en PHP (`conexion.php` o similar).  
   - Asegúrate de que los datos coincidan con tu entorno:  
     ```php
     $hostDB = 'localhost';       // Dirección del servidor MySQL
     $nombreDB = 'valoraciones';  // Nombre de la base de datos
     $usuarioDB = 'root';         // Usuario de la base de datos
     $contraDB = '';              // Contraseña (vacío en XAMPP por defecto)
     ```

5️⃣ **Ejecutar el proyecto**  
   - Abre tu navegador y accede a:  
     ```
     http://localhost/UT6_SP_SuárezBritoNoa
     ```

---

## 📌 Funcionalidades Principales
✅ **Autenticación de usuarios** con control de errores en tiempo real mediante Xajax  
✅ **Sistema de votación por estrellas**, con actualizaciones dinámicas  
✅ **Evita votos duplicados** por usuario en un mismo producto  
✅ **Base de datos optimizada** para almacenar y gestionar valoraciones  
✅ **Cálculo automático** de la media de valoraciones y total de votos  
✅ **Diseño responsive** con iconos de **FontAwesome** para las estrellas  

---

🚀 _¡Gracias por visitar este proyecto! Si tienes dudas o sugerencias, no dudes en compartirlas._  
