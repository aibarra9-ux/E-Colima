document.addEventListener('DOMContentLoaded', cargarSolicitudes);

async function cargarSolicitudes() {
    const container = document.getElementById('lista-solicitudes');
    
    try {
        const response = await fetch('../../PHP/Perfil/obtener_solicitudes.php');
        const solicitudes = await response.json();

        container.innerHTML = '';

        if (solicitudes.length === 0) {
            container.innerHTML = '<tr><td colspan="5" style="text-align:center; color:gray; padding:40px;">No hay solicitudes pendientes de revisión.</td></tr>';
            return;
        }

        solicitudes.forEach(sol => {
    const tr = document.createElement('tr');
    // Aseguramos que la clase del badge dependa del ID del rol (ajusta si Admin no es 1)
    const claseRol = sol.rol_solicitado == 1 ? 'badge-admin' : 'badge-user';
    
    // Extraemos solo la fecha (YYYY-MM-DD) del campo fecha_creacion
    const fechaLimpia = sol.fecha_creacion ? sol.fecha_creacion.split(' ')[0] : '---';

    tr.innerHTML = `
        <td><strong>${sol.username}</strong></td>
        <td><span class="badge-rol ${claseRol}">${sol.rol_nombre || 'Sin Rol'}</span></td>
        <td style="max-width: 400px; color: #4a5568;">${sol.motivo}</td>
        <td style="color: #94a3b8; font-weight: 600;">${fechaLimpia}</td>
        <td>
            <div class="action-buttons">
                <button title="Aprobar" class="btn-approve" onclick="gestionarSolicitud(${sol.id}, 'aprobar')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>
                <button title="Rechazar" class="btn-reject" onclick="gestionarSolicitud(${sol.id}, 'rechazar')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </td>
    `;
    container.appendChild(tr);
});
    } catch (error) {
        console.error("Error cargando solicitudes:", error);
    }
}

// Buscador en tiempo real
document.getElementById('searchSolicitud').addEventListener('keyup', (e) => {
    const text = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#lista-solicitudes tr');

    rows.forEach(row => {
        const username = row.querySelector('td:first-child').textContent.toLowerCase();
        row.style.display = username.includes(text) ? '' : 'none';
    });
});

function gestionarSolicitud(id, accion) {
    const titulo = accion === 'aprobar' ? '¿Aprobar solicitud?' : '¿Rechazar solicitud?';
    const texto = accion === 'aprobar' 
        ? "El usuario recibirá los permisos del nuevo rol de inmediato." 
        : "La solicitud será marcada como rechazada.";
    const colorBoton = accion === 'aprobar' ? '#2ecc71' : '#e74c3c';

    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: colorBoton,
        cancelButtonColor: '#95a5a6',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada al PHP
            fetch(`../../PHP/Perfil/procesar_solicitudes.php?id=${id}&accion=${accion}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: '¡Logrado!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#2d5a27'
                        });
                        cargarSolicitudes(); // Recargamos la tabla para que desaparezca la fila
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                });
        }
    });
}
