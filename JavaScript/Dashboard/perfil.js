document.addEventListener('DOMContentLoaded', function() {
    // 1. Referencias
    const btnAbrir = document.getElementById('btnEditarPerfil');
    const modal = document.getElementById('modalEdit');
    const btnCerrarX = document.getElementById('closeModal');
    const btnCancelar = document.getElementById('btnCancel');
    const form = document.getElementById('formEditarPerfil');

    // 2. Abrir Modal
    if (btnAbrir) {
        btnAbrir.addEventListener('click', (e) => {
            e.preventDefault(); // Evita cualquier salto de página
            modal.style.display = 'flex';
        });
    }

    // 3. Funciones para cerrar
    const cerrarModal = () => {
        modal.style.display = 'none';
    };

    if (btnCerrarX) btnCerrarX.onclick = cerrarModal;
    if (btnCancelar) btnCancelar.onclick = cerrarModal;

    // Cerrar si hacen clic fuera del cuadro blanco
    window.onclick = (event) => {
        if (event.target == modal) cerrarModal();
    };

    // 4. Procesar el Formulario (AJAX)
    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(form);

            try {
                // Asegúrate de que la ruta sea correcta según donde esté este JS
                const response = await fetch('actualizar_perfil.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                // Dentro del .then o tras el await del fetch:
                if (result.status === 'success') {
                // 1. Cerramos el modal primero
                modal.style.display = 'none';

                // 2. Mostramos la alerta
                Swal.fire({
                    title: '¡Actualizado!',
                    text: 'Tu perfil se ha actualizado correctamente.',
                    icon: 'success',
                    confirmButtonColor: '#39cb39',
                    timer: 2000,
                    showConfirmButton: false,
                    // Esto asegura que la alerta esté por encima de cualquier cosa
                    didOpen: () => {
                        const container = document.querySelector('.swal2-container');
                        if (container) container.style.zIndex = '10001';
                    }
                }).then(() => {
                    location.reload();
                });
            }
            } catch (error) {
                console.error("Error:", error);
                alert("No se pudo conectar con el servidor.");
            }
        };
    }
});

document.getElementById('inputFoto').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    // Validar que sea imagen y no pese más de 2MB
    if (!archivo.type.startsWith('image/')) {
        Swal.fire('Error', 'Por favor selecciona una imagen válida', 'error');
        return;
    }
    if (archivo.size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'La imagen es demasiado pesada (Máx 2MB)', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('foto', archivo);

    // Mostrar carga
    Swal.fire({
        title: 'Subiendo...',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('subir_foto.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Foto actualizada!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload(); // Recarga para actualizar todas las fotos en el dashboard
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo subir la imagen', 'error');
    });
});

document.getElementById('inputBanner').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    const formData = new FormData();
    formData.append('banner', archivo);

    Swal.fire({
        title: 'Actualizando portada...',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('subir_banner.php', { // Crearemos este archivo
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); 
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
});