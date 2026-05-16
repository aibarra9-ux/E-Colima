/* === CONFIGURACIÓN DE TRADUCCIÓN DE EMERGENCIA === */
const btnTranslate = document.getElementById('btn-translate');
const spanEs = document.getElementById('lang-es');
const spanEn = document.getElementById('lang-en');

// Guardar idioma en localStorage
let currentLang = localStorage.getItem('globalLang') || 'es';

// Diccionario de textos integrado directamente aquí para evitar usar FETCH y carpetas
const traducciones = {
    "es": {
        "navbar": { "buscar": "Buscar...", "login": "Iniciar sesión", "logout": "Cerrar sesión" },
        "inicio": {
            "bienvenida": "Bienvenido, ",
            "eslogan": "Colima es un museo vivo de biodiversidad...",
            "categorias_titulo": "Adéntrate en la red de la vida.",
            "flora_titulo": "Flora",
            "flora_desc": "Descubre la increíble variedad de plantas y vegetación que hacen único a Colima.",
            "fauna_titulo": "Fauna",
            "fauna_desc": "Conoce las fascinantes especies animales que habitan en los diversos ecosistemas de Colima.",
            "leer_mas": "Leer más",
            "noticias_titulo": "Noticias de los ecosistemas de Colima"
        },
        "login": {
            "volver": "← Volver",
            "titulo": "Hola de nuevo, ¿Qué tal?",
            "placeholder_user": "Nombre de usuario",
            "placeholder_pass": "Ingresar contraseña",
            "recordar": "Recordar usuario",
            "olvidaste": "¿Olvidaste tu contraseña?",
            "btn_entrar": "Entrar",
            "btn_google": "O únete con Google",
            "no_cuenta": "No tienes una cuenta aún?",
            "registrate": "Regístrate"
        },
        "registro": {
            "titulo": "Crea tu cuenta",
            "placeholder_nombre": "Nombre completo",
            "placeholder_correo": "Correo electrónico",
            "placeholder_crear": "Crear contraseña",
            "placeholder_confirmar": "Confirmar contraseña",
            "btn_crear": "Crear cuenta",
            "ya_cuenta": "¿Ya tienes una cuenta?",
            "logearse": "Logeate"
        }
    },
    "en": {
        "navbar": { "buscar": "Search...", "login": "Sign In", "logout": "Log Out" },
        "inicio": {
            "bienvenida": "Welcome, ",
            "eslogan": "Colima is a living museum of biodiversity...",
            "categorias_titulo": "Step into the web of life.",
            "flora_titulo": "Flora",
            "flora_desc": "Discover the incredible variety of plants...",
            "fauna_titulo": "Fauna",
            "fauna_desc": "Meet the fascinating animal species...",
            "leer_mas": "Read more",
            "noticias_titulo": "News from Colima's ecosystems"
        },
        "login": {
            "volver": "← Back",
            "titulo": "Welcome back, how are you?",
            "placeholder_user": "Username",
            "placeholder_pass": "Enter password",
            "recordar": "Remember me",
            "olvidaste": "Forgot your password?",
            "btn_entrar": "Sign In",
            "btn_google": "Or join with Google",
            "no_cuenta": "Don't have an account yet?",
            "registrate": "Register"
        },
        "registro": {
            "titulo": "Create your account",
            "placeholder_nombre": "Full name",
            "placeholder_correo": "Email address",
            "placeholder_crear": "Create password",
            "placeholder_confirmar": "Confirm password",
            "btn_crear": "Create account",
            "ya_cuenta": "Already have an account?",
            "logearse": "Log In"
        }
    }
};

function traducirPagina() {
    // Cambiar clases del resplandor
    if (spanEs && spanEn) {
        if (currentLang === 'en') {
            spanEs.classList.remove('active');
            spanEn.classList.add('active');
        } else {
            spanEn.classList.remove('active');
            spanEs.classList.add('active');
        }
    }

    // Buscar todos los elementos con data-section y traducirlos
    document.querySelectorAll("[data-section]").forEach((el) => {
        const section = el.dataset.section;
        const value = el.dataset.value;

        if (traducciones[currentLang][section] && traducciones[currentLang][section][value]) {
            if (el.tagName === 'INPUT') {
                el.placeholder = traducciones[currentLang][section][value];
            } else {
                el.innerText = traducciones[currentLang][section][value];
            }
        }
    });
}

// Evento directo al botón
if (btnTranslate) {
    btnTranslate.onclick = function() {
        currentLang = (currentLang === 'es') ? 'en' : 'es';
        localStorage.setItem('globalLang', currentLang);
        traducirPagina();
    };
}

// Traducir de inmediato al cargar
window.onload = traducirPagina;


/* === LÓGICA DE POSTS (Tus posts intactos) === */
const posts = [
    { title: "Bosque tropical", desc: "Ejemplo de publicación.", image: "https://picsum.photos/600/900?1" },
    { title: "Plantas silvestres", desc: "Descripción breve.", image: "https://picsum.photos/600/900?2" }
];

const container = document.getElementById("postsContainer");
if (container) {
    posts.forEach((post) => {
        const card = document.createElement("div");
        card.className = "post-card";
        card.innerHTML = `
            <img src="${post.image}" alt="${post.title}">
            <h3>${post.title}</h3>
            <p>${post.desc}</p>
        `;
        container.appendChild(card);
    });
}