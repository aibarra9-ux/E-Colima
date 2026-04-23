/* ========================================================= */
/* ======================= DATOS ============================ */
/* ========================================================= */

const publicaciones = [

{
titulo:"Águila Real de Colima",
descripcion:"Ejemplo de publicación.",
imagen:"https://picsum.photos/600/900?1"
},

{
titulo:"Iguana Negra de Colima",
descripcion:"Descripción breve.",
imagen:"https://picsum.photos/600/900?2"
},

{
titulo:"Zorro de Colima",
descripcion:"Otra publicación.",
imagen:"https://picsum.photos/600/900?3"
},

{
titulo:"Ocelote de Colima",
descripcion:"Ejemplo de publicación.",
imagen:"https://picsum.photos/600/900?4"
},

{
titulo:"Tigrillo de Colima",
descripcion:"Descripción breve.",
imagen:"https://picsum.photos/600/900?5"
},

{
titulo:"Venado Cola Blanca de Colima",
descripcion:"Otra publicación.",
imagen:"https://picsum.photos/600/900?6"
},

{
titulo:"Jabali de Collar de Colima",
descripcion:"Ejemplo de publicación.",
imagen:"https://picsum.photos/600/900?7"
},

{
titulo:"Ardilla de Colima",
descripcion:"Descripción breve.",
imagen:"https://picsum.photos/600/900?8"
},

{
titulo:"Topacaminos de Colima",
descripcion:"Otra publicación.",
imagen:"https://picsum.photos/600/900?9"
},

{
titulo:"TZanate de Colima",
descripcion:"Otra publicación.",
imagen:"https://picsum.photos/600/900?10"
},

];


/* ========================================================= */
/* ================= CREAR TARJETAS ========================= */
/* ========================================================= */

const contenedor = document.getElementById("contenedorPublicaciones");

publicaciones.forEach((post,i)=>{

// Tarjeta destacada para el primer post
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

        setTimeout(() => {
            tarjetaGrande.classList.add("mostrar");
        }, 100);

        return; // salta al siguiente, no crea tarjeta normal
    }

// Tarjetas normales
const tarjeta = document.createElement("div");
    tarjeta.classList.add("tarjeta-publicacion");

    tarjeta.innerHTML = `
        <img class="imagen-publicacion" src="${post.imagen}">
        <h3 class="titulo-publicacion">${post.titulo}</h3>
        <p class="descripcion-publicacion">${post.descripcion}</p>
    `;

    contenedor.appendChild(tarjeta);

    setTimeout(() => {
        tarjeta.classList.add("mostrar");
    }, 200 * i);


/* ========================================================= */
/* ================= BOTON SCROLL TOP ====================== */
/* ========================================================= */

const btnScrollTop = document.getElementById("btnScrollTop");
const seccionHero = document.querySelector(".seccion-hero");

const observer = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
        if(entry.isIntersecting){
            btnScrollTop.classList.remove("visible");
        } else {
            btnScrollTop.classList.add("visible");
        }
    });
});

observer.observe(seccionHero);

btnScrollTop.addEventListener("click",()=>{
    window.scrollTo({top:0,behavior:"smooth"});
});
});