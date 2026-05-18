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
                const response = await fetch('actualizar_perfil.php', {
                    method: 'POST',
                    body: formData
                });

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
                Swal.fire('Error de conexión', 'No se pudo contactar con el archivo actualizar_perfil.php.', 'error');
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

    // 🌟 ARREGLO 1: Capturamos el ID del atributo HTML para que el PHP no lo reciba vacío
    const usuarioId = e.target.getAttribute('data-usuario-id');

    const formData = new FormData();
    formData.append('foto', archivo);
    formData.append('usuario_id_alterno', usuarioId); // <- Pasamos el ID al servidor

    Swal.fire({ title: 'Subiendo...', didOpen: () => { Swal.showLoading(); } });

    fetch('subir_foto.php', { 
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reestablecido tu refresco original
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

    // 🌟 ARREGLO 2: Capturamos el ID del atributo HTML igual que en la foto
    const usuarioId = e.target.getAttribute('data-usuario-id');

    const formData = new FormData();
    // 🌟 ARREGLO 3: Cambiado de 'banner' a 'banner_perfil' para que tu PHP lo reconozca
    formData.append('banner_perfil', archivo); 
    formData.append('usuario_id_alterno', usuarioId); // <- Pasamos el ID al servidor

    Swal.fire({ title: 'Actualizando portada...', didOpen: () => { Swal.showLoading(); } });

    fetch('subir_banner.php', { 
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload(); // Reestablecido tu refresco original
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => Swal.fire('Error', 'No se pudo conectar con subir_banner.php', 'error'));
});