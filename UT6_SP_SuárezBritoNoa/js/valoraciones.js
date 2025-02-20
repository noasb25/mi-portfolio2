// Espera a que el contenido del DOM esté completamente cargado antes de ejecutar el código.
document.addEventListener('DOMContentLoaded', () => {
    // Selecciona todos los botones con la clase 'btn-valorar' en la página.
    const botonesValorar = document.querySelectorAll('.btn-valorar');

    // Obtiene el elemento del modal de imagen ampliada por su ID.
    const modal = document.getElementById('imageModal');

    // Obtiene la imagen dentro del modal para mostrar la imagen ampliada.
    const modalImage = document.getElementById('modalImage');

    // Selecciona el botón de cierre del modal por su clase.
    const closeModal = document.querySelector('.modal-close');

    // Itera sobre cada botón de valorar.
    botonesValorar.forEach(boton => {
        // Añade un evento de clic a cada botón.
        boton.addEventListener('click', () => {
            // Obtiene el ID del producto desde el atributo `data-id` del botón.
            const idProducto = boton.dataset.id;

            // Selecciona el campo `<select>` correspondiente al producto por su ID.
            const selectValoracion = document.querySelector(`.select-valoracion[data-id="${idProducto}"]`);

            // Obtiene el valor seleccionado en el `<select>`.
            const valor = selectValoracion.value;

            // Si no se ha seleccionado un valor, muestra una alerta y termina la función.
            if (!valor) {
                alert('Por favor, selecciona una valoración antes de votar.');
                return;
            }

            // Envía una solicitud `POST` al servidor con los datos de la valoración usando `fetch`.
            fetch('../templates/valorar.php', {
                method: 'POST', // Método HTTP utilizado para la solicitud.
                headers: {
                    'Content-Type': 'application/json', // Define que los datos enviados son JSON.
                },
                // Convierte los datos de la valoración a formato JSON para enviarlos al servidor.
                body: JSON.stringify({
                    idProducto: idProducto, // ID del producto a valorar.
                    valor: valor, // Valor de la valoración seleccionada.
                }),
            })
                .then(response => response.json()) // Convierte la respuesta del servidor a formato JSON.
                .then(data => {
                    // Si el servidor responde con un error, muestra una alerta con el mensaje.
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Si la valoración es exitosa, actualiza la celda de valoración en la tabla.
                        const celdaValoracion = document.getElementById(`valoracion-${idProducto}`);
                        let estrellasHTML = ''; // Variable para almacenar el HTML de las estrellas.

                        // Añade estrellas completas según la media recibida del servidor.
                        for (let i = 0; i < Math.floor(data.media); i++) {
                            estrellasHTML += '<i class="fas fa-star"></i>';
                        }

                        // Si la media tiene un decimal mayor o igual a 0.5, añade media estrella.
                        if (data.media % 1 >= 0.5) {
                            estrellasHTML += '<i class="fas fa-star-half-alt"></i>';
                        }

                        // Completa las estrellas faltantes con estrellas vacías.
                        for (let i = Math.ceil(data.media); i < 5; i++) {
                            estrellasHTML += '<i class="far fa-star"></i>';
                        }

                        // Añade el texto con el número de valoraciones (singular o plural).
                        const textoValoraciones = data.votos === 1 ? '1 valoración' : `${data.votos} valoraciones`;
                        estrellasHTML += ` <span class='rating-text'>(${textoValoraciones})</span>`;

                        // Actualiza el contenido de la celda de valoración con el nuevo HTML.
                        celdaValoracion.innerHTML = estrellasHTML;

                        // Muestra una alerta de agradecimiento al usuario.
                        alert('¡Gracias por tu valoración!');
                    }
                })
                .catch(error => {
                    // Si ocurre un error durante la solicitud, lo registra en la consola y muestra una alerta.
                    console.error('Error al enviar la valoración:', error);
                    alert('Hubo un problema al enviar tu valoración. Intenta nuevamente.');
                });
        });
    });

    // Selecciona todas las imágenes dentro de la tabla.
    const images = document.querySelectorAll('table img');

    // Añade un evento de clic a cada imagen para mostrarla ampliada en el modal.
    images.forEach(image => {
        image.addEventListener('click', () => {
            modal.style.display = 'flex'; // Muestra el modal.
            modalImage.src = image.src; // Establece la fuente de la imagen ampliada.
        });
    });

    // Cierra el modal cuando se hace clic en el botón de cierre.
    closeModal.addEventListener('click', () => {
        modal.style.display = 'none'; // Oculta el modal.
    });

    // Cierra el modal si el usuario hace clic fuera de la imagen (en el fondo del modal).
    modal.addEventListener('click', (e) => {
        if (e.target === modal) { // Verifica que el clic fue en el modal, no en el contenido interno.
            modal.style.display = 'none'; // Oculta el modal.
        }
    });
});
