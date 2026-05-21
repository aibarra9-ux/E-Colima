document.addEventListener('DOMContentLoaded', () => {
    cargarSolicitudes();
    
    // Escuchar cambios de idioma para recargar solicitudes
    window.addEventListener('idioma-cambiado', () => {
        console.log("[Solicitudes] Idioma cambiado, recargando solicitudes...");
        cargarSolicitudes();
    });
});

// Array para almacenar las solicitudes originales (sin traducir)
let solicitudesOriginales = [];

// Función para traducir textos
function traducirTexto(texto) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return texto;
    if (typeof traducciones !== 'undefined' && traducciones[texto]) {
        return traducciones[texto];
    }
    return texto;
}

// Función para obtener el nombre del rol traducido
function traducirRol(rolNombre, rolId) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return rolNombre;
    
    const rolesTraducidos = {
        'Administrador': 'Administrator',
        'Editor': 'Editor',
        'Escritor': 'Writer',
        'Usuario': 'User',
        'Sin Rol': 'No Role'
    };
    
    return rolesTraducidos[rolNombre] || rolNombre;
}

// Función para renderizar las solicitudes
function renderizarSolicitudes() {
    const container = document.getElementById('lista-solicitudes');
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const solicitudes = solicitudesOriginales;
    
    container.innerHTML = '';

    if (solicitudes.length === 0) {
        const mensajeVacio = idiomaActual === 'en' 
            ? "No pending requests to review."
            : "No hay solicitudes pendientes de revisión.";
        container.innerHTML = `<tr><td colspan="5" style="text-align:center; color:gray; padding:40px;">${mensajeVacio}</td></tr>`;
        return;
    }

    solicitudes.forEach(sol => {
        let claseRol = 'badge-user';
        if (sol.rol_solicitado == 2) claseRol = 'badge-escritor';
        if (sol.rol_solicitado == 3) claseRol = 'badge-editor';    
        
        const fechaLimpia = sol.fecha_creacion ? sol.fecha_creacion.split(' ')[0] : '---';
        const rolNombreTraducido = traducirRol(sol.rol_nombre || 'Sin Rol', sol.rol_solicitado);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${escapeHTML(sol.username)}</strong></td>
            <td><span class="badge-rol ${claseRol}">${rolNombreTraducido}</span></td>
            <td style="max-width: 400px; color: #4a5568;">${escapeHTML(sol.motivo)}</td>
            <td style="color: #94a3b8; font-weight: 600;">${fechaLimpia}</td>
            <td>
                <div class="action-buttons">
                    <button title="${traducirTexto('Aprobar')}" class="btn-approve" onclick="gestionarSolicitud(${sol.id}, 'aprobar')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </button>
                    <button title="${traducirTexto('Rechazar')}" class="btn-reject" onclick="gestionarSolicitud(${sol.id}, 'rechazar')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </td>
        `;
        container.appendChild(tr);
    });
    
    // Restaurar el filtro de búsqueda después de renderizar
    const searchInput = document.getElementById('searchSolicitud');
    if (searchInput && searchInput.value.trim() !== "") {
        aplicarFiltroBusqueda(searchInput.value.trim());
    }
}

// Función auxiliar para escapar HTML
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Función para aplicar filtro de búsqueda
function aplicarFiltroBusqueda(texto) {
    const textoLower = texto.toLowerCase();
    const rows = document.querySelectorAll('#lista-solicitudes tr');
    
    // Excluir la fila de "no hay solicitudes" si existe
    rows.forEach(row => {
        const firstCell = row.querySelector('td:first-child');
        if (firstCell && firstCell.textContent) {
            const username = firstCell.textContent.toLowerCase();
            row.style.display = username.includes(textoLower) ? '' : 'none';
        }
    });
}

async function cargarSolicitudes() {
    const container = document.getElementById('lista-solicitudes');
    
    try {
        const response = await fetch('../../PHP/Perfil/obtener_solicitudes.php');
        let solicitudes = await response.json();

        // Guardar solicitudes originales
        solicitudesOriginales = solicitudes.map(sol => ({
            ...sol,
            username_original: sol.username,
            motivo_original: sol.motivo,
            rol_nombre_original: sol.rol_nombre
        }));
        
        renderizarSolicitudes();
        
    } catch (error) {
        console.error("Error cargando solicitudes:", error);
        const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
        const mensajeError = idiomaActual === 'en' 
            ? "Error loading requests."
            : "Error al cargar las solicitudes.";
        container.innerHTML = `<tr><td colspan="5" style="text-align:center; color:red; padding:40px;">${mensajeError}</td></tr>`;
    }
}

// Buscador en tiempo real
const searchInput = document.getElementById('searchSolicitud');
if (searchInput) {
    searchInput.addEventListener('keyup', (e) => {
        aplicarFiltroBusqueda(e.target.value.trim());
    });
}

function gestionarSolicitud(id, accion) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    
    const titulo = accion === 'aprobar' 
        ? (idiomaActual === 'en' ? 'Approve request?' : '¿Aprobar solicitud?')
        : (idiomaActual === 'en' ? 'Reject request?' : '¿Rechazar solicitud?');
    
    const texto = accion === 'aprobar' 
        ? (idiomaActual === 'en' ? 'The user will receive the new role permissions immediately.' : 'El usuario recibirá los permisos del nuevo rol de inmediato.')
        : (idiomaActual === 'en' ? 'The request will be marked as rejected.' : 'La solicitud será marcada como rechazada.');
    
    const colorBoton = accion === 'aprobar' ? '#2ecc71' : '#e74c3c';
    const confirmarTexto = idiomaActual === 'en' ? 'Yes, continue' : 'Sí, continuar';
    const cancelarTexto = idiomaActual === 'en' ? 'Cancel' : 'Cancelar';

    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: colorBoton,
        cancelButtonColor: '#95a5a6',
        confirmButtonText: confirmarTexto,
        cancelButtonText: cancelarTexto
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamada al PHP
            fetch(`../../PHP/Perfil/procesar_solicitudes.php?id=${id}&accion=${accion}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const exitoTitulo = idiomaActual === 'en' ? 'Success!' : '¡Logrado!';
                        Swal.fire({
                            title: exitoTitulo,
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#2d5a27'
                        });
                        cargarSolicitudes(); // Recargamos la tabla
                    } else {
                        const errorTitulo = idiomaActual === 'en' ? 'Error' : 'Error';
                        Swal.fire(errorTitulo, data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorTitulo = idiomaActual === 'en' ? 'Error' : 'Error';
                    const errorMsg = idiomaActual === 'en' 
                        ? 'Could not connect to the server' 
                        : 'No se pudo conectar con el servidor';
                    Swal.fire(errorTitulo, errorMsg, 'error');
                });
        }
    });
}