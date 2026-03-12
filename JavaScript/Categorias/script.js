/* EJEMPLO POSTS */

const posts=[

{
title:"Bosque tropical",
desc:"Ejemplo de publicación.",
image:"https://picsum.photos/600/900?1"
},

{
title:"Plantas silvestres",
desc:"Descripción breve.",
image:"https://picsum.photos/600/900?2"
},

{
title:"Vegetación",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?3"
},

{
title:"Naturaleza",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?4"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?5"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?7"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?8"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?9"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?10"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?11"
},

{
title:"Selva",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?12"
},

{
title:"Flora tropical",
desc:"Otra publicación.",
image:"https://picsum.photos/600/900?6"
}

];


/* CREAR TARJETAS */

const container=document.getElementById("postsContainer");

posts.forEach((post,i)=>{

const card=document.createElement("div");

card.classList.add("post-card");

card.innerHTML=`

<img class="post-image" src="${post.image}">
<h3 class="post-title">${post.title}</h3>
<p class="post-desc">${post.desc}</p>

`;

container.appendChild(card);

setTimeout(()=>{
card.classList.add("show");
},200*i);

});


/* SIDEBAR SCROLL */

const sidebar=document.getElementById("sidebar");
const main=document.getElementById("mainContent");

window.addEventListener("scroll",()=>{

if(window.scrollY>200){

sidebar.classList.add("show");
main.classList.add("shift");

}else{

sidebar.classList.remove("show");
main.classList.remove("shift");

}

});