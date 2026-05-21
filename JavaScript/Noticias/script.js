/* ========================================================= */
/* =============== CARGAR DATOS DESDE BD ==================== */
/* ========================================================= */

const contenedor = document.getElementById("contenedorPublicaciones");
const inputBuscador = document.getElementById("inputBuscador");

// Variables globales de estado para el filtrado dinámico combinado
let subcategoriaActivaId = null; 
const categoriaId = "4"; // ID único asignado a Noticias en tu BD

// Array para almacenar las publicaciones originales (sin traducir)
let publicacionesOriginales = [];

// Función para renderizar las publicaciones (con o sin traducción)
function renderizarPublicaciones() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const publicaciones = publicacionesOriginales;
    
    if (publicaciones.length === 0) {
        const mensajeNoResultados = idiomaActual === 'en' 
            ? "No news or posts found matching your search."
            : "No se encontraron noticias o publicaciones que coincidan con la búsqueda.";
            
        contenedor.innerHTML = `
            <div class="sin-publicaciones" style="grid-column: 1 / -1; text-align: center; padding: 50px; color: white;">
                <i class="fas fa-search-minus" style="font-size: 3rem; margin-bottom: 15px; color: rgba(255,255,255,0.4);"></i>
                <p style="font-family: 'Outfit', sans-serif; font-size: 1.2rem;">${mensajeNoResultados}</p>
            </div>
        `;
        return;
    }

    contenedor.innerHTML = "";
    
    publicaciones.forEach((post, i) => {
        // Usar título traducido si está en inglés y existe
        let tituloMostrar = post.titulo_original;
        let descMostrar = post.descripcion_original;
        
        if (idiomaActual === 'en') {
            if (post.titulo_traducido) tituloMostrar = post.titulo_traducido;
            if (post.descripcion_traducida) descMostrar = post.descripcion_traducida;
        }
        
        // Generamos la etiqueta multimedia dinámicamente según el backend (video o imagen)
        let elementoMultimedia = "";
        if (post.tipo_media === 'video') {
            elementoMultimedia = `<video class="imagen-publicacion" src="${post.imagen}" autoplay muted loop playsinline style="object-fit: cover; width: 100%; height: 100%;"></video>`;
        } else {
            elementoMultimedia = `<img class="imagen-publicacion" src="${post.imagen}" alt="${escapeHTML(tituloMostrar)}">`;
        }

        // La primera publicación se renderiza como la tarjeta grande destacada
        if (i === 0) {
            const tarjetaGrande = document.createElement("div");
            tarjetaGrande.classList.add("tarjeta-grande");

            // Reutilizamos el elemento multimedia adaptando las clases para la vista destacada
            let elementoMultimediaGrande = elementoMultimedia
                .replace('class="imagen-publicacion"', 'class="imagen-grande"');

            const verMasTexto = (idiomaActual === 'en' && typeof traducciones !== 'undefined' && traducciones['Ver más']) 
                ? traducciones['Ver más'] 
                : 'Ver más';

            tarjetaGrande.innerHTML = `
                ${elementoMultimediaGrande}
                <div class="info-grande">
                    <h2 class="titulo-grande" style="cursor: pointer;" onclick="window.location.href='../../PHP/Publicacion/detalle_publicacion.php?id=${post.id}'">${escapeHTML(tituloMostrar)}</h2>
                    <p class="descripcion-grande">${escapeHTML(descMostrar)}</p>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 15px; width: 100%;">
                        <button class="boton-ver-mas" onclick="window.location.href='../../PHP/Publicacion/detalle_publicacion.php?id=${post.id}'">${verMasTexto}</button>
                        
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
        
        tarjeta.style.cursor = "pointer";
        tarjeta.setAttribute("onclick", `if(!event.target.closest('.likes-container')) window.location.href='../../PHP/Publicacion/detalle_publicacion.php?id=${post.id}'`);

        tarjeta.innerHTML = `
            ${elementoMultimedia}
            <h3 class="titulo-publicacion">${escapeHTML(tituloMostrar)}</h3>
            <p class="descripcion-publicacion">${escapeHTML(descMostrar)}</p>
            
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

// Función centralizada para cargar publicaciones
async function cargarPublicaciones() {
    // 1. Construimos la ruta base apuntando de forma fija a la categoría general de Noticias
    let url = `obtener_publicaciones.php?categoria_id=${categoriaId}`;
    
    // 2. Si el usuario interactuó con una subcategoría, mutamos el parámetro destino
    if (subcategoriaActivaId) {
        url = `obtener_publicaciones.php?subcategoria_id=${subcategoriaActivaId}`;
    }
    
    // 3. Si el input de búsqueda contiene texto, concatenamos el parámetro de limpieza SQL
    if (inputBuscador && inputBuscador.value.trim() !== "") {
        url += `&search=${encodeURIComponent(inputBuscador.value.trim())}`;
    }

    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        let publicaciones = await response.json();
        
        // Guardar publicaciones originales
        publicacionesOriginales = publicaciones.map(pub => ({
            ...pub,
            titulo_original: pub.titulo,
            descripcion_original: pub.descripcion,
            titulo_traducido: null,
            descripcion_traducida: null
        }));
        
        // Si el idioma es inglés y existe la función global de traducción, traducir en bloque
        const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
        if (idiomaActual === 'en' && typeof window.traducirPublicaciones === 'function') {
            console.log("[Noticias] Traduciendo publicaciones con función global...");
            publicacionesOriginales = await window.traducirPublicaciones(publicacionesOriginales);
        }
        
        renderizarPublicaciones();
        
    } catch (error) {
        console.error('Error al cargar:', error);
        contenedor.innerHTML = '<p style="text-align:center;color:white;padding:50px;">Error al cargar las publicaciones de Noticias.</p>';
    }
}

// Escuchar cambios de idioma para recargar publicaciones
window.addEventListener('idioma-cambiado', () => {
    console.log("[Noticias] Idioma cambiado, recargando publicaciones...");
    cargarPublicaciones();
});

// Inicialización de escuchas y flujos de renderizado en el DOM
document.addEventListener("DOMContentLoaded", () => {
    // Carga inicial por defecto para Noticias (ID 4)
    cargarPublicaciones();

    // Evento de escucha en tiempo real para capturar la escritura del usuario
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
        // Almacenamos el fondo inicial que viene cargado de forma por defecto en Noticias
        let fondoFijoActual = window.getComputedStyle(hero).backgroundImage;

        botonesFiltro.forEach(boton => {
            const nuevaImagen = boton.getAttribute("data-bg");
            const subcategoriaId = boton.getAttribute("data-subcategoria");

            if (nuevaImagen) {
                // 1. Efecto HOVER: Cambia momentáneamente al pasar el cursor
                boton.addEventListener("mouseenter", () => {
                    hero.style.backgroundImage = `url('${nuevaImagen}')`;
                });

                // Al quitar el cursor, regresa al fondo que esté FIJO actualmente
                boton.addEventListener("mouseleave", () => {
                    hero.style.backgroundImage = fondoFijoActual;
                });

                // 2. Efecto CLICK: Fija el fondo permanentemente y muta el estado global
                boton.addEventListener("click", () => {
                    fondoFijoActual = `url('${nuevaImagen}')`;
                    hero.style.backgroundImage = fondoFijoActual;

                    // Alternar estados estéticos CSS activos de los botones
                    botonesFiltro.forEach(b => b.classList.remove("activo"));
                    boton.classList.add("activo");

                    // Fijamos globalmente el estado de la subcategoría seleccionada para la grilla combinada
                    subcategoriaActivaId = subcategoriaId ? subcategoriaId : null;

                    // Lanzamos la actualización unificada
                    cargarPublicaciones();
                });
            }
        });
    }
});