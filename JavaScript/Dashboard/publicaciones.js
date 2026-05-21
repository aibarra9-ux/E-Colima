document.addEventListener('DOMContentLoaded', () => {
    cargarPublicaciones();
    
    // Escuchar cambios de idioma para recargar publicaciones
    window.addEventListener('idioma-cambiado', () => {
        console.log("[Dashboard Publicaciones] Idioma cambiado, recargando publicaciones...");
        cargarPublicaciones();
    });
});

// Array para almacenar las publicaciones originales (sin traducir)
let publicacionesOriginales = [];

// Función para renderizar las publicaciones (con o sin traducción)
function renderizarPublicaciones() {
    const container = document.getElementById('posts-container');
    if (!container) return;
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const posts = publicacionesOriginales;

    container.innerHTML = '';

    if (!Array.isArray(posts)) {
        console.error("Lo que recibí no es un array:", posts);
        container.innerHTML = '<p style="color: gray; text-align: center;">Error en el formato de datos del servidor.</p>';
        return;
    }

    if (posts.length === 0) {
        const mensajeVacio = idiomaActual === 'en' 
            ? "No posts to moderate."
            : "No hay publicaciones para moderar.";
        container.innerHTML = `<p style="color: gray; grid-column: 1/-1; text-align: center;">${mensajeVacio}</p>`;
        return;
    }

    posts.forEach(post => {
        // Usar título traducido si está en inglés y existe
        let tituloMostrar = post.titulo_original;
        
        if (idiomaActual === 'en') {
            if (post.titulo_traducido) tituloMostrar = post.titulo_traducido;
        }
        
        const card = document.createElement('div');
        card.className = 'feed-card'; 
        card.style.marginBottom = '24px';
        card.style.background = 'white';
        card.style.borderRadius = '20px';
        card.style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
        card.style.overflow = 'hidden';

        // 🌟 LÓGICA DE DETECCIÓN MULTIMEDIA (IMAGEN VS VIDEO)
        let componenteMultimedia = '';

        if (post.imagen) {
            if (post.tipo_archivo === 'video') {
                const extension = post.imagen.split('.').pop().toLowerCase();
                componenteMultimedia = `
                    <video style="width: 100%; height: 100%; object-fit: cover;" controls preload="metadata">
                        <source src="../../assets/Publicaciones/${post.imagen}" type="video/${extension}">
                        Tu navegador no soporta la reproducción de este video.
                    </video>`;
            } else {
                componenteMultimedia = `<img src="../../assets/Publicaciones/${post.imagen}" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
        } else {
            componenteMultimedia = `<img src="../../assets/Publicaciones/default_post.jpg" style="width: 100%; height: 100%; object-fit: cover;">`;
        }

        const textoEliminar = idiomaActual === 'en' ? "Delete Post" : "Eliminar Publicación";

        card.innerHTML = `
            <div style="padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f1f5f9;">
                <img src="../../assets/Fotos_perfil/default_avatar.png" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                <div>
                    <div class="post-autor" style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${escapeHTML(post.autor)}</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">${post.fecha}</div>
                </div>
            </div>
            <div style="padding: 16px;">
                <h3 style="font-size: 1rem; color: #1e293b; margin-bottom: 8px;">${escapeHTML(tituloMostrar)}</h3>
                <div style="border-radius: 12px; overflow: hidden; height: 200px; background: #f8fafc; margin-top: 10px;">
                    ${componenteMultimedia}
                </div>
            </div>
            <div style="padding: 12px 16px; background: #fafafa; text-align: right;">
                <button class="btn-action-delete" onclick="eliminarPost(${post.id})" 
                        style="color: #e63946; background: #ffe5e5; padding: 6px 14px; border-radius: 8px; border: none; font-weight: 700; font-size: 0.75rem; cursor: pointer;">
                    ${textoEliminar}
                </button>
            </div>
        `;
        container.appendChild(card);
    });
    
    // Restaurar el filtro de búsqueda después de renderizar
    const searchInput = document.getElementById('searchPost');
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

// Función para aplicar filtro de búsqueda después de renderizar
function aplicarFiltroBusqueda(texto) {
    const textoLower = texto.toLowerCase();
    const publicaciones = document.querySelectorAll('.feed-card');
    
    publicaciones.forEach(card => {
        const titulo = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const autor = card.querySelector('.post-autor')?.textContent.toLowerCase() || '';

        if (titulo.includes(textoLower) || autor.includes(textoLower)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });

    // Mensaje adaptativo de resultados vacíos
    const resultados = Array.from(publicaciones).filter(c => c.style.display !== "none");
    const container = document.getElementById('posts-container');
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const mensajeNoResultados = idiomaActual === 'en' 
        ? "No posts match your search."
        : "No se encontraron publicaciones que coincidan.";
    
    let noRes = document.getElementById('no-results');
    if (resultados.length === 0) {
        if (!noRes) {
            noRes = document.createElement('p');
            noRes.id = 'no-results';
            noRes.style.textAlign = 'center';
            noRes.style.gridColumn = '1/-1';
            noRes.style.color = 'gray';
            noRes.textContent = mensajeNoResultados;
            container.appendChild(noRes);
        }
    } else if (noRes) {
        noRes.remove();
    }
}

async function cargarPublicaciones() {
    const container = document.getElementById('posts-container');
    if (!container) return;

    try {
        const response = await fetch('../../PHP/Perfil/obtener_publicaciones.php');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        let posts = await response.json();

        if (!Array.isArray(posts)) {
            console.error("Lo que recibí no es un array:", posts);
            container.innerHTML = '<p style="color: gray; text-align: center;">Error en el formato de datos del servidor.</p>';
            return;
        }

        // Guardar publicaciones originales
        publicacionesOriginales = posts.map(post => ({
            ...post,
            titulo_original: post.titulo,
            titulo_traducido: null
        }));
        
        // Si el idioma es inglés y existe la función global de traducción, traducir en bloque
        const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
        if (idiomaActual === 'en' && typeof window.traducirPublicaciones === 'function') {
            console.log("[Dashboard Publicaciones] Traduciendo publicaciones con función global...");
            // Necesitamos adaptar el formato para la función de traducción
            const publicacionesParaTraducir = publicacionesOriginales.map(pub => ({
                titulo: pub.titulo_original,
                descripcion: pub.descripcion || ''
            }));
            const traducidas = await window.traducirPublicaciones(publicacionesParaTraducir);
            // Aplicar traducciones
            traducidas.forEach((pub, idx) => {
                if (pub.titulo_traducido) {
                    publicacionesOriginales[idx].titulo_traducido = pub.titulo_traducido;
                }
            });
        }
        
        renderizarPublicaciones();
        
    } catch (error) {
        console.error("Error cargando posts:", error);
        container.innerHTML = `<p style="color: red;">Error al conectar con el servidor: ${error.message}</p>`;
    }
}

function eliminarPost(id) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const tituloConfirmacion = idiomaActual === 'en' ? "Are you sure?" : "¿Estás seguro?";
    const textoConfirmacion = idiomaActual === 'en' 
        ? "This post will be permanently deleted" 
        : "Esta publicación se eliminará permanentemente";
    const confirmarTexto = idiomaActual === 'en' ? "Yes, delete" : "Sí, eliminar";
    const cancelarTexto = idiomaActual === 'en' ? "Cancel" : "Cancelar";
    
    Swal.fire({
        title: tituloConfirmacion,
        text: textoConfirmacion,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmarTexto,
        cancelButtonText: cancelarTexto
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`../../PHP/Perfil/eliminar_post.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const exitoTexto = idiomaActual === 'en' ? "Deleted" : "Eliminado";
                    const mensajeExito = idiomaActual === 'en' 
                        ? "The post has been deleted" 
                        : "La publicación ha sido borrada";
                    Swal.fire(exitoTexto, mensajeExito, 'success');
                    cargarPublicaciones();
                }
            });
        }
    });
}

// 🌟 Buscador adaptado
const searchInput = document.getElementById('searchPost');
if (searchInput) {
    searchInput.addEventListener('keyup', function(e) {
        aplicarFiltroBusqueda(e.target.value.trim());
    });
}