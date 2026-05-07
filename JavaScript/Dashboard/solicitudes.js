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
            const claseRol = sol.rol_solicitado == 1 ? 'badge-admin' : 'badge-user';
            
            tr.innerHTML = `
                <td><strong>${sol.username}</strong></td>
                <td><span class="badge-rol ${claseRol}">${sol.rol_nombre}</span></td>
                <td style="max-width: 400px; color: #4a5568;">${sol.motivo}</td>
                <td>
                    <div class="action-buttons">
                        <button title="Aprobar" class="btn-approve" onclick="gestionarSolicitud(${sol.id}, 'aprobar')">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button title="Rechazar" class="btn-reject" onclick="gestionarSolicitud(${sol.id}, 'rechazar')">
                            <i class="fa-solid fa-xmark"></i>
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