/* ========================================================= */
/* =============== CARGAR DATOS DESDE BD ==================== */
/* ========================================================= */

const contenedor = document.getElementById("contenedorPublicaciones");
const inputBuscador = document.getElementById("inputBuscador");

// Variables globales de estado para el filtrado dinámico combinado
let subcategoriaActivaId = null; 
const categoriaId = "3"; // ID único asignado a ecosisteams en tu BD

// Función centralizada para estructurar la URL e inyectar las publicaciones
function cargarPublicaciones() {
    // 1. Construimos la ruta base apuntando a la categoría general de Fauna
    let url = `obtener_publicaciones.php?categoria_id=${categoriaId}`;
    
    // 2. Si el usuario activó una subcategoría mediante los botones, mutamos el parámetro
    if (subcategoriaActivaId) {
        url = `obtener_publicaciones.php?subcategoria_id=${subcategoriaActivaId}`;
    }
    
    // 3. Si el input contiene caracteres, concatenamos el filtro de búsqueda textual
    if (inputBuscador && inputBuscador.value.trim() !== "") {
        url += `&search=${encodeURIComponent(inputBuscador.value.trim())}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(publicaciones => {
            // Limpiamos la grilla por completo antes de inyectar las nuevas tarjetas
            contenedor.innerHTML = "";

            if (publicaciones.length === 0) {
                contenedor.innerHTML = `
                    <div class="sin-publicaciones" style="grid-column: 1 / -1; text-align: center; padding: 50px; color: white;">
                        <i class="fas fa-search-minus" style="font-size: 3rem; margin-bottom: 15px; color: rgba(255,255,255,0.4);"></i>
                        <p style="font-family: 'Outfit', sans-serif; font-size: 1.2rem;">No se encontraron ecosistemas que coincidan con la búsqueda.</p>
                    </div>
                `;
                return;
            }

            publicaciones.forEach((post, i) => {
                // La primera publicación se renderiza como la tarjeta grande destacada (Ej. El jaguar)
                if (i === 0) {
                    const tarjetaGrande = document.createElement("div");
                    tarjetaGrande.classList.add("tarjeta-grande");

                    tarjetaGrande.innerHTML = `
                        <img class="imagen-grande" src="${post.imagen}" alt="${post.titulo}">
                        <div class="info-grande">
                            <h2 class="titulo-grande">${post.titulo}</h2>
                            <p class="descripcion-grande">${post.descripcion}</p>
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 15px; width: 100%;">
                                <button class="boton-ver-mas">Ver más</button>
                                
                                <div class="likes-container" style="display: flex; align-items: center; gap: 8px;">
                                    <button onclick="interactuarLike(${post.id}, this)" 
                                            style="background: none; border: none; cursor: pointer; font-size: 1.4rem; padding: 0; transition: transform 0.1s;">
                                        <i class="${post.le_gusta ? 'fa-solid fa-heart' : 'fa-regular fa-heart'}" 
                                           style="color: ${post.le_gusta ? '#e63946' : 'rgba(255,255,255,0.6)'};"></i>
                                    </button>
                                    <span class="likes-count" style="font-weight: 700; color: white; font-size: 1rem;">${post.likes}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    contenedor.appendChild(tarjetaGrande);

                    setTimeout(() => {
                        tarjetaGrande.classList.add("mostrar");
                    }, 100);

                    return;
                }

                // Las siguientes publicaciones se renderizan en las tarjetas comunes de la grilla
                const tarjeta = document.createElement("div");
                tarjeta.classList.add("tarjeta-publicacion");

                tarjeta.innerHTML = `
                    <img class="imagen-publicacion" src="${post.imagen}">
                    <h3 class="titulo-publicacion">${post.titulo}</h3>
                    <p class="descripcion-publicacion">${post.descripcion}</p>
                    
                    <div class="likes-container" style="display: flex; align-items: center; gap: 8px; margin-top: auto; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <button onclick="interactuarLike(${post.id}, this)" 
                                style="background: none; border: none; cursor: pointer; font-size: 1.2rem; padding: 0; transition: transform 0.1s;">
                            <i class="${post.le_gusta ? 'fa-solid fa-heart' : 'fa-regular fa-heart'}" 
                               style="color: ${post.le_gusta ? '#e63946' : 'rgba(255,255,255,0.5)'};"></i>
                        </button>
                        <span class="likes-count" style="font-weight: 700; color: rgba(255,255,255,0.8); font-size: 0.9rem;">${post.likes}</span>
                    </div>
                `;

                contenedor.appendChild(tarjeta);

                setTimeout(() => {
                    tarjeta.classList.add("mostrar");
                }, 150 * i);
            });
        })
        .catch(error => {
            console.error('Error al cargar:', error);
            contenedor.innerHTML = '<p style="text-align:center;color:white;padding:50px;">Error al cargar las publicaciones de Fauna.</p>';
        });
}

// Inicialización de cargas e hilos de eventos en el DOM
document.addEventListener("DOMContentLoaded", () => {
    // Ejecución inicial por defecto para Fauna (ID 2)
    cargarPublicaciones();

    // Evento asíncrono para capturar la escritura del usuario en la barra superior
    if (inputBuscador) {
        inputBuscador.addEventListener("input", () => {
            cargarPublicaciones();
        });
    }
});

/* ========================================================= */
/* ================ FUNCIÓN INTERACTIVA LIKES ============= */
/* ========================================================= */

async function interactuarLike(postId, boton) {
    try {
        const response = await fetch(`../../PHP/Perfil/dar_like.php?publicacion_id=${postId}`);
        const data = await response.json();

        if (data.status === 'success') {
            const icono = boton.querySelector('i');
            const contador = boton.nextElementSibling;
            
            if (data.accion === 'liked') {
                icono.className = 'fa-solid fa-heart';
                icono.style.color = '#e63946';
            } else {
                icono.className = 'fa-regular fa-heart';
                const esTarjetaGrande = boton.closest('.info-grande') !== null;
                icono.style.color = esTarjetaGrande ? 'rgba(255,255,255,0.6)' : 'rgba(255,255,255,0.5)';
            }
            contador.textContent = data.total_likes;
        } else if (data.message === 'No autorizado') {
            alert('¡Hola! Necesitas iniciar sesión en tu cuenta para interactuar y dar me gusta.');
        }
    } catch (error) {
        console.error("Error al procesar la interacción de me gusta:", error);
    }
}

/* ========================================================= */
/* ================= BOTON SCROLL TOP ====================== */
/* ========================================================= */

const btnScrollTop = document.getElementById("btnScrollTop");
const seccionHero = document.querySelector(".seccion-hero");

if (btnScrollTop && seccionHero) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                btnScrollTop.classList.remove("visible");
            } else {
                btnScrollTop.classList.add("visible");
            }
        });
    }, { threshold: 0.1 });

    observer.observe(seccionHero);

    btnScrollTop.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

/* ========================================================= */
/* ====== CAMBIO DE FONDO DINÁMICO Y FILTRADO (CLICK) ======= */
/* ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const hero = document.querySelector(".seccion-hero");
    const botonesFiltro = document.querySelectorAll(".boton-filtro");

    if (hero && botonesFiltro.length > 0) {
        // Guardamos el fondo original por defecto (el del Jaguar/Fauna inicial)
        let fondoFijoActual = window.getComputedStyle(hero).backgroundImage;

        botonesFiltro.forEach(boton => {
            const nuevaImagen = boton.getAttribute("data-bg");
            const subcategoriaId = boton.getAttribute("data-subcategoria");

            if (nuevaImagen) {
                // 1. Efecto HOVER: Cambia temporalmente al pasar el cursor
                boton.addEventListener("mouseenter", () => {
                    hero.style.backgroundImage = `url('${nuevaImagen}')`;
                });

                // Al sacar el cursor, regresa al fondo que esté bloqueado o fijo actualmente
                boton.addEventListener("mouseleave", () => {
                    hero.style.backgroundImage = fondoFijoActual;
                });

                // 2. Efecto CLICK: Fija el fondo permanentemente y cambia el estado global
                boton.addEventListener("click", () => {
                    fondoFijoActual = `url('${nuevaImagen}')`;
                    hero.style.backgroundImage = fondoFijoActual;

                    // Manejo visual de la clase activa en los botones
                    botonesFiltro.forEach(b => b.classList.remove("activo"));
                    boton.classList.add("activo");

                    // Fijamos la subcategoría seleccionada para la petición global
                    subcategoriaActivaId = subcategoriaId ? subcategoriaId : null;

                    // Actualizamos las publicaciones llamando a la lógica combinada
                    cargarPublicaciones();
                });
            }
        });
    }
});
