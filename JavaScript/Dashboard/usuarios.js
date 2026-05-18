// usuarios.js

document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
});

// 1. CARGAR USUARIOS DESDE LA DB
async function cargarUsuarios() {
    try {
        const response = await fetch('../../PHP/Perfil/obtener_usuarios.php');
        const textoCargado = await response.text();

        let usuarios;
        try {
            usuarios = JSON.parse(textoCargado);
        } catch (e) {
            console.error("El servidor no envió JSON válido:", textoCargado);
            return;
        }

        if (usuarios.error) {
            alert("Error: " + usuarios.error);
            return;
        }

        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = '';

        usuarios.forEach(user => {
            // --- DEFINICIÓN DE VARIABLES (El paso que faltaba) ---
            const nombreReal = user.username || "Sin nombre";
            const correoReal = user.email || "Sin correo";
            const fechaReal = user.fecha_registro || "No disponible";
            const fotoReal = user.foto_perfil ? user.foto_perfil : 'default_avatar.png';
            
            // Lógica de seguridad
            const esAdmin = (user.rol_id == 1);
            const soyYo = (typeof miIdDeSesion !== 'undefined' && user.id == miIdDeSesion);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="user-info-cell" style="display: flex; align-items: center; gap: 12px;">
                        <div class="user-img-wrapper" style="width: 40px; height: 40px; min-width: 40px; overflow: hidden; border-radius: 50%; border: 2px solid var(--green-pale);">
                            <img src="../../assets/Fotos_perfil/${fotoReal}" 
                                 alt="Avatar" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>
                        <span style="font-weight: 700; color: var(--text-dark);">
                            ${nombreReal} ${soyYo ? '<small style="color:var(--green-mid)">(Tú)</small>' : ''}
                        </span>
                    </div>
                </td>
                <td style="color: var(--text-mid); font-weight: 600;">${correoReal}</td>
                <td style="color: var(--gray-400); font-size: 0.85rem;">${fechaReal}</td>
                <td>
                    <select class="role-select" 
                            ${esAdmin ? 'disabled' : ''} 
                            onchange="gestionarCambioRol(${user.id}, this)"
                            style="${esAdmin ? 'opacity: 0.6; cursor: not-allowed;' : ''}">
                        <option value="1" ${user.rol_id == 1 ? 'selected' : ''}>Administrador</option>
                        <option value="2" ${user.rol_id == 2 ? 'selected' : ''}>Editor</option>
                        <option value="3" ${user.rol_id == 3 ? 'selected' : ''}>Autor</option>
                        <option value="4" ${user.rol_id == 4 ? 'selected' : ''}>Usuario</option>
                    </select>
                </td>
                <td style="text-align: center;">
                    ${(esAdmin || soyYo) ? 
                        `<span style="color: #cbd5e1;" title="Protegido">🔒</span>` : 
                        `<button class="btn-action-delete" onclick="eliminarUsuario(${user.id}, '${nombreReal}')" title="Eliminar usuario">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>`
                    }
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error("Error crítico:", error);
    }
}

// 2. CAMBIAR ROL (CON SWEETALERT2)
async function gestionarCambioRol(userId, selectElement) {
    // 1. Guardamos el valor que tenía ANTES del cambio
    const valorOriginal = selectElement.getAttribute('data-old-value') || selectElement.defaultValue;
    const nuevoRol = selectElement.value;
    const nombreRol = selectElement.options[selectElement.selectedIndex].text;

    // 2. IMPORTANTE: Revertimos visualmente el select de inmediato 
    // para que no cambie hasta que el usuario confirme en el Swal.
    selectElement.value = valorOriginal;

    const result = await Swal.fire({
        title: '¿Cambiar rol?',
        text: `¿Estás seguro de asignar el rango de "${nombreRol}" a este usuario?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2d6a4f',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
    const formData = new FormData();
    formData.append('id', userId);
    formData.append('rol_id', nuevoRol);

    try {
        const res = await fetch('../../PHP/Perfil/actualizar_rol.php', {
            method: 'POST',
            body: formData
        });

        if (!res.ok) throw new Error('Error en el servidor');

        const data = await res.json();
        
        if (data.status === 'success' || data.success) {
            // 1. IMPORTANTE: Recargamos la tabla desde el servidor
            // Esto actualizará el 'rol_id' en el JS y pondrá los candados automáticamente
            await cargarUsuarios(); 
            
            Swal.fire({
                title: '¡Rango Actualizado!',
                text: 'El usuario ahora tiene nuevos permisos y restricciones.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            throw new Error(data.message || 'Error en BD');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudo actualizar el rol.', 'error');
        // Revertimos el select si hubo error (opcional, ya que cargarUsuarios lo haría)
        selectElement.value = valorOriginal;
    }
}
    // Si cancela, el select ya regresó a valorOriginal en el paso 2.
}

// 3. ELIMINAR USUARIO (CON SWEETALERT2)
async function eliminarUsuario(userId, nombre) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a eliminar a "${nombre}". ¡Esta acción no se puede deshacer!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'No, mantener'
    });

    if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('id', userId);

        try {
            const res = await fetch('../../PHP/Perfil/eliminar_usuario.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire('Eliminado', 'El usuario ha sido borrado.', 'success');
                cargarUsuarios();
            } else {
                    Swal.fire('No se pudo eliminar', data.error || 'Hubo un problema al eliminar.', 'error');            }
        } catch (error) {
            Swal.fire('Error', 'Error de red o servidor.', 'error');
        }
    }
}

// 4. FILTRAR USUARIOS (Versión Definitiva)
document.addEventListener('input', e => {
    if (e.target.matches('#user-search')) {
        const filtro = e.target.value.toLowerCase().trim();
        const filas = document.querySelectorAll('#users-table-body tr');

        filas.forEach(fila => {
            // 1. Extraemos solo el nombre de usuario (el span que está después del cuadro verde)
            const nombreElenent = fila.querySelector('.user-info-cell span');
            const nombre = nombreElenent ? nombreElenent.textContent.toLowerCase() : "";

            // 2. Extraemos el correo pero le quitamos el "@gmail.com" para que no interfiera
            const correoCompleto = fila.cells[1] ? fila.cells[1].innerText.toLowerCase() : "";
            const correoSinDominio = correoCompleto.split('@')[0]; 

            // 3. Lógica de coincidencia:
            // ¿El filtro está incluido en el nombre? O ¿Está incluido en la primera parte del correo?
            if (nombre.includes(filtro) || correoSinDominio.startsWith(filtro)) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    }
});

// Asegúrate de que las últimas líneas de tu usuarios.js queden únicamente así:

document.addEventListener('DOMContentLoaded', () => {
    // 1. Referencias con IDs actualizados del nuevo Dashboard
    const btnEdit = document.querySelector('.btn-edit-main');
    const modal = document.getElementById('configDrawer'); 
    const overlay = document.getElementById('configOverlay'); 

    // Funciones seguras para cerrar
    const cerrar = () => {
        if(modal) modal.classList.remove('open');
        if(overlay) overlay.classList.remove('open');
    };

    // 2. Abrir (Solo si el botón existe)
    if (btnEdit && modal && overlay) {
        btnEdit.onclick = () => {
            modal.classList.add('open');
            overlay.classList.add('open');
        };
    }

    // 3. Cerrar (Solo si los elementos existen)
    const btnCloseX = document.getElementById('closeModal');
    const btnCancel = document.getElementById('btnCancel');

    if (btnCloseX) btnCloseX.onclick = cerrar;
    if (btnCancel) btnCancel.onclick = cerrar;
    if (overlay) overlay.onclick = cerrar;

    // 🌟 ¡EL BLOQUE "form.onsubmit" QUE ESTABA AQUÍ ABAJO HA SIDO ELIMINADO!
});