document.addEventListener('DOMContentLoaded', cargarPublicaciones);

async function cargarPublicaciones() {
    const container = document.getElementById('posts-container');
    if (!container) return;

    try {
        // 🌟 NOTA: Si este es el dashboard de Admin, recuerda apuntar a "dashboard_publicaciones.php"
        const response = await fetch('../../PHP/Perfil/obtener_publicaciones.php');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const posts = await response.json();
        container.innerHTML = '';

        if (!Array.isArray(posts)) {
            console.error("Lo que recibí no es un array:", posts);
            container.innerHTML = '<p style="color: gray; text-align: center;">Error en el formato de datos del servidor.</p>';
            return;
        }

        if (posts.length === 0) {
            container.innerHTML = '<p style="color: gray; grid-column: 1/-1; text-align: center;">No hay publicaciones para moderar.</p>';
            return;
        }

        posts.forEach(post => {
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
                    // Extraemos la extensión del archivo dinámicamente para el atributo type
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

            card.innerHTML = `
                <div style="padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #f1f5f9;">
                    <img src="../../assets/Fotos_perfil/default_avatar.png" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div class="post-autor" style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${post.autor}</div>
                        <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">${post.fecha}</div>
                    </div>
                </div>
                <div style="padding: 16px;">
                    <h3 style="font-size: 1rem; color: #1e293b; margin-bottom: 8px;">${post.titulo}</h3>
                    <div style="border-radius: 12px; overflow: hidden; height: 200px; background: #f8fafc; margin-top: 10px;">
                        ${componenteMultimedia}
                    </div>
                </div>
                <div style="padding: 12px 16px; background: #fafafa; text-align: right;">
                    <button class="btn-action-delete" onclick="eliminarPost(${post.id})" 
                            style="color: #e63946; background: #ffe5e5; padding: 6px 14px; border-radius: 8px; border: none; font-weight: 700; font-size: 0.75rem; cursor: pointer;">
                        Eliminar Publicación
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    } catch (error) {
        console.error("Error cargando posts:", error);
        container.innerHTML = `<p style="color: red;">Error al conectar con el servidor: ${error.message}</p>`;
    }
}

function eliminarPost(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta publicación se eliminará permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`../../PHP/Perfil/eliminar_post.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('Eliminado', 'La publicación ha sido borrada', 'success');
                    cargarPublicaciones();
                }
            });
        }
    });
}

// 🌟 Buscador adaptado
document.getElementById('searchPost').addEventListener('keyup', function(e) {
    const texto = e.target.value.toLowerCase();
    const publicaciones = document.querySelectorAll('.feed-card');
    
    publicaciones.forEach(card => {
        const titulo = card.querySelector('h3').textContent.toLowerCase();
        const autor = card.querySelector('.post-autor').textContent.toLowerCase();

        if (titulo.includes(texto) || autor.includes(texto)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });

    // Mensaje adaptativo de resultados vacíos
    const resultados = Array.from(publicaciones).filter(c => c.style.display !== "none");
    const container = document.getElementById('posts-container');
    
    let noRes = document.getElementById('no-results');
    if (resultados.length === 0) {
        if (!noRes) {
            noRes = document.createElement('p');
            noRes.id = 'no-results';
            noRes.style.textAlign = 'center';
            noRes.style.gridColumn = '1/-1';
            noRes.style.color = 'gray';
            noRes.textContent = 'No se encontraron publicaciones que coincidan.';
            container.appendChild(noRes);
        }
    } else if (noRes) {
        noRes.remove();
    }
});