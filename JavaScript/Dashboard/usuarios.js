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
            console.error("El servidor no envió JSON válido. Envió esto:", textoCargado);
            return;
        }

        if (usuarios.error) {
            alert("Error de Base de Datos: " + usuarios.error);
            return;
        }

        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = '';

        usuarios.forEach(user => {
            const nombreReal = user.username || "Sin nombre";
            const correoReal = user.email || "Sin correo";
            const fechaReal = user.fecha_registro || "No disponible";
            
            // Lógica para la foto: si no existe en la BD, usamos la default
            const fotoReal = user.foto_perfil ? user.foto_perfil : 'default_avatar.png';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="user-info-cell" style="display: flex; align-items: center; gap: 12px;">
                        <div class="user-img-wrapper" style="width: 40px; height: 40px; overflow: hidden; border-radius: 50%; border: 1px solid #eee;">
                            <img src="../../assets/Fotos_perfil/${fotoReal}" 
                                 alt="Avatar" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <span style="font-weight: 500;">${nombreReal}</span>
                    </div>
                </td>
                <td>${correoReal}</td>
                <td>${fechaReal}</td>
                <td>
                    <select class="role-selector" onchange="gestionarCambioRol(${user.id}, this)">
                        <option value="1" ${user.rol_id == 1 ? 'selected' : ''}>Administrador</option>
                        <option value="2" ${user.rol_id == 2 ? 'selected' : ''}>Editor</option>
                        <option value="3" ${user.rol_id == 3 ? 'selected' : ''}>Autor</option>
                        <option value="4" ${user.rol_id == 4 ? 'selected' : ''}>Usuario</option>
                    </select>
                </td>
                <td>
                    <button class="btn-action-delete" onclick="eliminarUsuario(${user.id}, '${nombreReal}')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
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
    const nuevoRol = selectElement.value;
    const nombreRol = selectElement.options[selectElement.selectedIndex].text;

    // Ventana de confirmación estilizada
    const result = await Swal.fire({
        title: '¿Cambiar rol?',
        text: `¿Estás seguro de asignar el rango de "${nombreRol}" a este usuario?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('id', userId);
        formData.append('rol', nuevoRol);

        try {
            const res = await fetch('../../PHP/Perfil/actualizar_rol.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                Swal.fire('¡Actualizado!', 'El rol ha sido modificado con éxito.', 'success');
            } else {
                Swal.fire('Error', 'No se pudo actualizar en la base de datos.', 'error');
                cargarUsuarios();
            }
        } catch (error) {
            Swal.fire('Error', 'No hay conexión con el servidor.', 'error');
            cargarUsuarios();
        }
    } else {
        cargarUsuarios(); // Revierte el select
    }
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
                Swal.fire('Error', 'Hubo un problema al eliminar.', 'error');
            }
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

document.addEventListener('DOMContentLoaded', () => {
    const btnEdit = document.querySelector('.btn-edit-main');
    const modal = document.getElementById('modalEdit');
    const closeModal = document.getElementById('closeModal');
    const btnCancel = document.getElementById('btnCancel');
    const form = document.getElementById('formEditarPerfil');

    // Abrir modal
    btnEdit.onclick = () => modal.style.display = 'flex';

    // Cerrar modal
    const cerrar = () => modal.style.display = 'none';
    closeModal.onclick = cerrar;
    btnCancel.onclick = cerrar;

    // Enviar datos
    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        const response = await fetch('actualizar_perfil.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (result.status === 'success') {
            alert("¡Cambios guardados con éxito!");
            location.reload(); // Recarga para ver los cambios en el Dashboard
        } else {
            alert("Hubo un error: " + result.message);
        }
    };
});