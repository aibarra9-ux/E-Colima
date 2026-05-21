document.addEventListener('DOMContentLoaded', function() {
    // 1. Referencias (ACTUALIZADAS AL NUEVO HTML)
    const modal = document.getElementById('configDrawer'); // El panel lateral
    const overlay = document.getElementById('configOverlay'); // El fondo oscuro
    const form = document.getElementById('formEditarPerfil');

    // Funciones de cierre consistentes con tu HTML
    const cerrarConfig = () => {
        if(modal) modal.classList.remove('open');
        if(overlay) overlay.classList.remove('open');
    };

    // 2. Procesar el Formulario (AJAX)
    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(form);

            try {
                // CORRECCIÓN DE RUTA: Ajusta esta ruta a donde realmente esté tu PHP
                // Si el PHP está en la misma carpeta que el dashboard, déjalo así.
                // Si está en una carpeta superior usa: ../actualizar_perfil.php
                const response = await fetch('actualizar_perfil.php', {
                    method: 'POST',
                    body: formData
                });

                // Si el servidor responde 404 o 500, esto atrapará el error antes del .json()
                if (!response.ok) throw new Error('Archivo no encontrado en el servidor');

                const result = await response.json();
                
                if (result.status === 'success') {
                    cerrarConfig(); // Cerramos el panel lateral

                    Swal.fire({
                        title: '¡Actualizado!',
                        text: 'Tu perfil se ha actualizado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#39cb39',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', result.message || 'Error desconocido', 'error');
                }
            } catch (error) {
                console.error("Error detallado:", error);
                Swal.fire('Error de conexión', 'No se pudo contactar con el archivo actualizar_perfil.php. Revisa la ruta en el JS.', 'error');
            }
        };
    }
});

// --- SUBIR FOTO ---
document.getElementById('inputFoto').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    if (!archivo.type.startsWith('image/')) {
        Swal.fire('Error', 'Selecciona una imagen válida', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('foto', archivo);

    Swal.fire({ title: 'Subiendo...', didOpen: () => { Swal.showLoading(); } });

    fetch('subir_foto.php', { // <--- VERIFICA QUE ESTE ARCHIVO ESTÉ EN LA MISMA CARPETA
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
    })
    .catch(err => Swal.fire('Error', 'No se pudo conectar con subir_foto.php', 'error'));
});

// --- SUBIR BANNER ---
document.getElementById('inputBanner').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    if (!archivo) return;

    const formData = new FormData();
    formData.append('banner', archivo);

    Swal.fire({ title: 'Actualizando portada...', didOpen: () => { Swal.showLoading(); } });

    fetch('subir_banner.php', { // <--- VERIFICA QUE ESTE ARCHIVO ESTÉ EN LA MISMA CARPETA
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
    })
    .catch(err => Swal.fire('Error', 'No se pudo conectar con subir_banner.php', 'error'));
});