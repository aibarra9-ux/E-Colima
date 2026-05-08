document.addEventListener('DOMContentLoaded', cargarPublicaciones);

async function cargarPublicaciones() {
    const container = document.getElementById('posts-container');
    if (!container) return;

    try {
        const response = await fetch('../../PHP/Perfil/obtener_publicaciones.php');
        
        // Verificamos si la respuesta es correcta antes de parsear
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const posts = await response.json();
        container.innerHTML = '';

        // VALIDACIÓN CRÍTICA:
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
            card.className = 'post-card';
            card.innerHTML = `
                <div class="post-image">
                    <img src="../../assets/Fotos_post/${post.imagen || 'default_post.jpg'}" alt="Post">
                </div>
                <div class="post-info">
                    <h3>${post.titulo}</h3>
                    <p>Por: <strong>${post.autor}</strong></p>
                    <span class="post-date"><i class="fa-regular fa-calendar"></i> ${post.fecha}</span>
                    <button class="btn-delete-post" onclick="eliminarPost(${post.id})">
                        <i class="fa-solid fa-trash"></i> Eliminar Publicación
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

document.getElementById('searchPost').addEventListener('keyup', function(e) {
    const texto = e.target.value.toLowerCase();
    const publicaciones = document.querySelectorAll('.post-card');

    publicaciones.forEach(card => {
        // Obtenemos el título y el autor de la tarjeta
        const titulo = card.querySelector('h3').textContent.toLowerCase();
        const autor = card.querySelector('.post-info strong').textContent.toLowerCase();

        // Si el texto está en el título o en el autor, mostramos la tarjeta
        if (titulo.includes(texto) || autor.includes(texto)) {
            card.style.display = "block";
            // Opcional: podrías usar card.classList.remove('hidden') si prefieres CSS
        } else {
            card.style.display = "none";
        }
    });

    // Opcional: Mensaje si no hay resultados
    const resultados = Array.from(publicaciones).filter(c => c.style.display !== "none");
    const container = document.getElementById('posts-container');
    
    // Si no hay resultados y no existe ya el mensaje, lo creamos
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