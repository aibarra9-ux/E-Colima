/* ========================================================= */
/* =============== CARGAR DATOS DESDE BD ==================== */
/* ========================================================= */

const contenedor = document.getElementById("contenedorPublicaciones");

fetch('obtener_publicaciones.php?categoria_id=1')
    .then(response => response.json())
    .then(publicaciones => {
        if (publicaciones.length === 0) {
            contenedor.innerHTML = '<p style="text-align:center;color:white;padding:50px;">No hay publicaciones aún en Flora.</p>';
            return;
        }

        publicaciones.forEach((post, i) => {
            if (i === 0) {
                const tarjetaGrande = document.createElement("div");
                tarjetaGrande.classList.add("tarjeta-grande");
                tarjetaGrande.innerHTML = `
                    <img class="imagen-grande" src="${post.imagen}" alt="${post.titulo}">
                    <div class="info-grande">
                        <h2 class="titulo-grande">${post.titulo}</h2>
                        <p class="descripcion-grande">${post.descripcion}</p>
                        <button class="boton-ver-mas">Ver más</button>
                    </div>
                `;
                contenedor.appendChild(tarjetaGrande);
                setTimeout(() => { tarjetaGrande.classList.add("mostrar"); }, 100);
                return;
            }

            const tarjeta = document.createElement("div");
            tarjeta.classList.add("tarjeta-publicacion");
            tarjeta.innerHTML = `
                <img class="imagen-publicacion" src="${post.imagen}">
                <h3 class="titulo-publicacion">${post.titulo}</h3>
                <p class="descripcion-publicacion">${post.descripcion}</p>
            `;
            contenedor.appendChild(tarjeta);
            setTimeout(() => { tarjeta.classList.add("mostrar"); }, 150 * i);
        });
    })
    .catch(error => {
        console.error('Error al cargar:', error);
        contenedor.innerHTML = '<p style="text-align:center;color:white;padding:50px;">Error al cargar las publicaciones.</p>';
    });

/* ================= BOTON SCROLL TOP ====================== */
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

/* ================= CAMBIO DE FONDO (HOVER) ====================== */
const hero = document.querySelector(".seccion-hero");
const botones = document.querySelectorAll(".boton-filtro");
const rutaBase = "../../assets/flora/subcategorias_bg/";
const fondoOriginal = "../../assets/flora/fondoTitulo.jpg";

const fondosPorCategoria = {
    "Todos": "flora_todos.jpg",
    "Arboles": "flora_arboles.jpg",
    "Arbustos": "flora_arbustos.jpg",
    "Cactaceas": "flora_cactacea.jpg",
    "Plantas Endémicas": "flora_endemica.jpg",
    "Plantas en riesgo": "flora_extintas.jpg",
    "Reino Fungi": "flora_fungi.jpg"
};

botones.forEach(boton => {
    boton.addEventListener("mouseenter", () => {
        const textoBoton = boton.textContent.trim();
        const archivoImagen = fondosPorCategoria[textoBoton];
        if (archivoImagen) {
            hero.style.backgroundImage = `url('${rutaBase}${archivoImagen}')`;
        }
    });

    boton.addEventListener("mouseleave", () => {
        hero.style.backgroundImage = `url('${fondoOriginal}')`;
    });
});