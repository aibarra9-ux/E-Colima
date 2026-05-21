/**
 * @fileoverview Sistema Core de Traducción Dinámica con Persistencia - Proyecto Ecolima
 * @version 3.4.0 - API + Diccionario Local (Con soporte para publicaciones dinámicas)
 */

// POOL DE DESARROLLADORES (Correos reales del equipo)
const POOL_DESARROLLADORES = [
    "cmartinez72@ucol.mx",
    "aibarra9@ucol.mx",
    "rbarba0@ucol.mx",
    "dnava2@ucol.mx",
    "elcumbioneslocos@gmail.com",
    "eibarra4@ucol.mx"
];

const DELIMITADOR_BLOQUE = "|||";

// Diccionario de traducciones para interfaz fija (español -> inglés)
const traducciones = {

"ECOSISTEMAS": "ECOSYSTEMS",
"Una categoría centrada en mostrar los diversos ecosistemas del estado de Colima": "A category focused on showing the diverse ecosystems of the state of Colima",
"Todos": "All",
"Colima": "Colima",
"Villa de Alvarez": "Villa de Alvarez",
"Manzanillo": "Manzanillo",
"Tecoman": "Tecoman",
"Armeria": "Armeria",
"Comala": "Comala",
"Coquimatlan": "Coquimatlan",
"Cuauhtemoc": "Cuauhtemoc",
"Ixtlahuacan": "Ixtlahuacan",
"Minatitlan": "Minatitlan",
"Ecosistemas - ECOLIMA": "Ecosystems - ECOLIMA",
"Buscar en ecosistemas...": "Search in ecosystems...",

"🔍 Buscar nombre o correo...": "🔍 Search by name or email...",

// Para solicitudes.js
"No hay solicitudes pendientes de revisión.": "No pending requests to review.",
"Aprobar": "Approve",
"Rechazar": "Reject",
"Error al cargar las solicitudes.": "Error loading requests.",
"¿Aprobar solicitud?": "Approve request?",
"¿Rechazar solicitud?": "Reject request?",
"El usuario recibirá los permisos del nuevo rol de inmediato.": "The user will receive the new role permissions immediately.",
"La solicitud será marcada como rechazada.": "The request will be marked as rejected.",
"Sí, continuar": "Yes, continue",
"Cancelar": "Cancel",
"¡Logrado!": "Success!",
"No se pudo conectar con el servidor": "Could not connect to the server",

// Traducciones de roles
"Administrador": "Administrator",
"Editor": "Editor", 
"Escritor": "Writer",
"Usuario": "User",
"Sin Rol": "No Role",

// Para perfil.js
"Por": "By",
"No content yet": "Sin contenido todavía",
"Sin contenido todavía": "No content yet",
"⚠️ Write something before publishing": "⚠️ Escribe algo antes de publicar",
"🌿 Post created locally": "🌿 Publicación creada localmente",
"⚠️ Please select a valid image file.": "⚠️ Por favor, selecciona un archivo de imagen válido.",
"⏳ Uploading image...": "⏳ Subiendo imagen...",
"✅ Profile picture updated!": "✅ ¡Foto de perfil actualizada!",
"🌿 Cover banner updated!": "🌿 ¡Banner de portada actualizado!",
"📝 Biography saved successfully": "📝 Biografía guardada con éxito",
"Are you sure you want to logout?": "¿Seguro que quieres salir?",
"You will have to log in again.": "Tendrás que iniciar sesión de nuevo.",
"Yes, logout": "Sí, salir",
"Cancel": "Cancelar",
"👋 Logging out...": "👋 Cerrando sesión...",
"Reason for rejection:": "Motivo de rechazo:",
"Created on": "Creado el",
"Published": "Publicada",
"Accepted": "Aceptada",
"Pending": "Pendiente",
"Rejected": "Rechazada",

"No se encontraron ecosistemas o publicaciones que coincidan con la búsqueda.": "No ecosystems or posts found matching your search.",

"No se encontraron animales o publicaciones que coincidan con la búsqueda.": "No animals or posts found matching your search.",

// Mensajes de "sin resultados" para cada categoría
"No se encontraron plantas o publicaciones que coincidan con la búsqueda.": "No plants or posts found matching your search.",
"No se encontraron animales o publicaciones que coincidan con la búsqueda.": "No animals or posts found matching your search.",
"No se encontraron ecosistemas o publicaciones que coincidan con la búsqueda.": "No ecosystems or posts found matching your search.",
"No se encontraron consejos que coincidan con la búsqueda.": "No tips found matching your search.",
"No se encontraron noticias que coincidan con la búsqueda.": "No news found matching your search.",

// Mensajes de "sin resultados"
"No se encontraron noticias o publicaciones que coincidan con la búsqueda.": "No news or posts found matching your search.",
"No se encontraron plantas o publicaciones que coincidan con la búsqueda.": "No plants or posts found matching your search.",
"No se encontraron animales o publicaciones que coincidan con la búsqueda.": "No animals or posts found matching your search.",
"No se encontraron ecosistemas o publicaciones que coincidan con la búsqueda.": "No ecosystems or posts found matching your search.",
"No se encontraron consejos que coincidan con la búsqueda.": "No tips found matching your search.",

// Mensajes de error
"Error al cargar las publicaciones de Consejos.": "Error loading tips posts.",
"Error al cargar las publicaciones de Ecosistemas.": "Error loading ecosystems posts.",
"Error al cargar las publicaciones de Fauna.": "Error loading fauna posts.",
"Error al cargar las publicaciones de Flora.": "Error loading flora posts.",
"Error al cargar las publicaciones de Noticias.": "Error loading news posts.",

// Botón "Ver más"
"Ver más": "Read more",

// Fauna página
"FAUNA": "FAUNA",
"Una categoría centrada en mostrar la diversa fauna del estado de Colima": "A category focused on showing the diverse fauna of the state of Colima",
"Mamiferos": "Mammals",
"Aves": "Birds",
"Reptiles": "Reptiles",
"Anfibios": "Amphibians",
"Invertebrados": "Invertebrates",
"Buscar en fauna...": "Search in fauna...",
"Fauna - ECOLIMA": "Fauna - ECOLIMA",

// ============================================
// FLORA
// ============================================
"FLORA": "FLORA",
"Una categoría centrada en mostrar la diversidad de plantas del estado de Colima": "A category focused on showing the diversity of plants in the state of Colima",
"Todos": "All",
"Arboles": "Trees",
"Arbustos": "Shrubs",
"Cactaceas": "Cactaceae",
"Plantas Endémicas": "Endemic Plants",
"Plantas en riesgo": "Plants at risk",
"Reino Fungi": "Fungi Kingdom",
"Buscar en flora...": "Search in flora...",
"Flora - ECOLIMA": "Flora - ECOLIMA",

// ============================================
// NOTICIAS
// ============================================
"Noticias": "News",
"Entérate de las más recientes noticias acerca de la vida terrestre en Colima": "Get the latest news about terrestrial life in Colima",
"Biodiversidad": "Biodiversity",
"Cambio climatico": "Climate change",
"Reforestación": "Reforestation",
"Educación ambiental": "Environmental education",
"Contaminación": "Pollution",
"Áreas naturales protegidas": "Protected natural areas",
"Buscar en noticias...": "Search in news...",
"Noticias - ECOLIMA": "News - ECOLIMA",

// Añade estas líneas al objeto traducciones
"ECOLIMA – Gestión de Usuarios": "ECOLIMA – User Management",
"Mi Perfil": "My Profile",
"Usuarios": "Users",
"Publicaciones": "Posts",
"Solicitudes": "Requests",
"Estadísticas": "Statistics",
"Configuración": "Settings",
"Cerrar sesión": "Log out",
"Gestión de Comunidad": "Community Management",
"Lista de Usuarios": "User List",
"Buscar nombre o correo...": "Search by name or email...",
"Usuario": "User",
"Correo Electrónico": "Email",
"Registro": "Registration",
"Rol": "Role",
"Acciones": "Actions",
"⚙️ Editar Perfil": "⚙️ Edit Profile",
"Nombre de Usuario": "Username",
"Correo Electrónico": "Email",
"Apariencia del Sistema": "System Appearance",
"Modo Claro": "Light Mode",
"Modo Oscuro": "Dark Mode",
"Contraseña Actual": "Current Password",
"Requerida para confirmar cambios": "Required to confirm changes",
"Contraseña Nueva": "New Password",
"Dejar vacío para no cambiar": "Leave empty to keep current",
"ID de Administrador:": "Admin ID:",
"Guardar Cambios": "Save Changes",

// Añade estas líneas al objeto traducciones
"Ver más": "Read more",
"No se encontraron consejos que coincidan con la búsqueda.": "No tips found matching your search.",
"Error al cargar las publicaciones de Consejos.": "Error loading tips posts.",

    // Navegación / Topbar
    "E-COLIMA": "E-COLIMA",
    "Cerrar sesión": "Log out",
    "Iniciar sesión": "Log in",
    "Solicitudes": "Requests",
    "Flora": "Flora",
    "Fauna": "Fauna",
    "Ecosistemas": "Ecosystems",
    "Consejos": "Tips",
    "Noticias": "News",
    
    // Textos del home
    "Colima es un museo vivo de biodiversidad, donde cada especie y cada paisaje cuentan una historia; explora, aprende y descubre cómo conservar estos tesoros naturales con información completa y confiable.": "Colima is a living museum of biodiversity, where every species and landscape tells a story; explore, learn and discover how to preserve these natural treasures with complete and reliable information.",
    "Adéntrate en la red de la vida.": "Dive into the web of life.",
    
    // Tarjetas
    "Descubre la increíble variedad de plantas y vegetación que hacen único a Colima": "Discover the incredible variety of plants and vegetation that make Colima unique",
    "Conoce las fascinantes especies animales que habitan en los diversos ecosistemas de Colima": "Discover the fascinating animal species that inhabit the diverse ecosystems of Colima",
    "Conoce los fascinantes ecosistemas terrestres del estado de Colima": "Discover the fascinating terrestrial ecosystems of the state of Colima",
    "Conoce educativos consejos para cuidar la vida terrestre": "Discover educational tips to take care of terrestrial life",
    "Conoce las mas recientes noticias acerca de la vida terrestre": "Discover the latest news about terrestrial life",
    "Leer más": "Read more",
    "PROXIMAMENTE...": "COMING SOON...",
    
    // Footer
    "View with GitHub": "View with GitHub",
    "2026 FACULTAD DE INGENIERÍA ELECTROMECÁNICA": "2026 FACULTY OF ELECTROMECHANICAL ENGINEERING",
    "LICENCIA": "LICENSE",
    "Este proyecto está bajo la Licencia MIT. Eres libre de usar, copiar y modificar el software para fines personales, educativos o comerciales, manteniendo siempre el aviso de autoría original.": "This project is under the MIT License. You are free to use, copy and modify the software for personal, educational or commercial purposes, always keeping the original authorship notice.",
    "RECURSOS": "RESOURCES",
    "EQUIPO": "TEAM",
    
    // Modales
    "¿CERRAR SESIÓN?": "LOG OUT?",
    "Si cierras sesión, tendrás que volver a ingresar para ver tu perfil e interactuar en las publicaciones.": "If you log out, you will have to log back in to view your profile and interact with posts.",
    "Cancelar": "Cancel",
    "Aceptar": "Accept",
    "INICIO REQUERIDO": "LOGIN REQUIRED",
    "Para explorar tu perfil, personalizar tu interfaz e interactuar en la comunidad de E-COLIMA, necesitas iniciar sesión.": "To explore your profile, customize your interface and interact with the E-COLIMA community, you need to log in.",
    "Quedarme aquí": "Stay here",
    "Iniciar Sesión": "Log in",

    // Placeholders
    "Buscar publicaciones...": "Search posts...",
    
    // Menú FAB
    "Crear publicación": "Create post",

    // Añade esto al objeto traducciones (para login.php)
"← Volver": "← Back",
"Bienvenido de nuevo 👋": "Welcome back 👋",
"Nombre de usuario": "Username",
"Ingresar contraseña": "Enter password",
"Recordar usuario": "Remember me",
"¿Olvidaste tu contraseña?": "Forgot your password?",
"Entrar": "Login",
"No tienes una cuenta aún?": "Don't have an account yet?",
"Regístrate": "Sign up",
"Iniciar sesión": "Log in",
"Credenciales incorrectas.": "Incorrect credentials.",
"No tienes permisos de administrador.": "You don't have administrator permissions.",
"Inicia sesión para acceder a esta área.": "Log in to access this area.",
"Ha ocurrido un error inesperado.": "An unexpected error occurred.",

// Añade estas líneas al objeto traducciones
"Crea tu cuenta": "Create your account",
"Las contraseñas no coinciden": "Passwords do not match",
"Correo electrónico": "Email",
"Crear contraseña": "Create password",
"Confirmar contraseña": "Confirm password",
"Te hemos enviado un código a tu correo:": "We have sent a code to your email:",
"Código de 6 dígitos": "6-digit code",
"📧 ¿No recibiste el código? Reenviar": "📧 Didn't receive the code? Resend",
"Reenviando...": "Resending...",
"Puedes reenviar en": "You can resend in",
"segundos": "seconds",
"Enviar código de verificación": "Send verification code",
"¿Ya tienes una cuenta?": "Already have an account?",
"Logeate": "Log in",
"¡Código reenviado! Revisa tu correo.": "Code resent! Check your email.",
"El usuario o correo ya existe": "Username or email already exists",
"El código de verificación es inválido": "The verification code is invalid",
"Error al registrar, intenta de nuevo": "Registration error, please try again",
"Registro - ECOLIMA": "Sign up - ECOLIMA",

"Nuevos Hoy": "New Today",
"Multimedia": "Multimedia",
"Interacciones": "Interactions",
"Registros de Usuarios (6 meses)": "User Registrations (6 months)",
"Distribución de Comunidad": "Community Distribution",
"BASE": "BASIC",
"El": "",
"de tus usuarios son nivel Base.": "of your users are Basic level.",

"Super Administrador Ecolima": "Ecolima Super Admin",
"Acceso de Nivel": "Access Level",
"Correo registrado:": "Registered email:",
"Miembro desde:": "Member since:",
"Editar Perfil": "Edit Profile",
"Estado del Sistema": "System Status",
"Security": "Security",
"Verificación de Identidad": "Identity Verification",
"Tu cuenta tiene privilegios de administrador para la gestión de ecosistemas en Colima. Asegúrate de mantener tus credenciales seguras.": "Your account has administrator privileges for ecosystem management in Colima. Make sure to keep your credentials secure.",

"Control de Contenido": "Content Management",
"Explorar Publicaciones": "Browse Posts",
"Buscar título o autor...": "Search by title or author...",
"ECOLIMA – Gestión de Publicaciones": "ECOLIMA – Posts Management",

"Moderador de Credenciales": "Credentials Moderator",
"Solicitudes Pendientes": "Pending Requests",
"Buscar usuario...": "Search user...",
"ECOLIMA – Solicitudes de Rol": "ECOLIMA – Role Requests",

"ECOLIMA – Perfil": "ECOLIMA – Profile",
"Panel de perfil": "Profile Panel",
"Solicitar cambio de Permisos": "Request Permission Change",
"Me gusta": "Likes",
"Mis Publicaciones": "My Posts",
"Solicitud de rango avanzado": "Advanced Role Request",
"Envía una propuesta detallando por qué deseas cambiar de rol (ej. Escritor o Editor). Tu caso será evaluado por los administradores.": "Submit a detailed proposal explaining why you want to change your role (e.g., Writer or Editor). Your case will be reviewed by administrators.",
"Rango solicitado:": "Requested Role:",
"Editor (Modificar y validar contenido)": "Editor (Modify and validate content)",
"Autor / Escritor (Subir flora, fauna, etc.)": "Author / Writer (Upload flora, fauna, etc.)",
"Motivo detallado:": "Detailed Reason:",
"Escribe aquí tu experiencia o razones para el cambio...": "Write your experience or reasons for the change here...",
"Enviar Solicitud": "Submit Request",
"Guardar cambios": "Save changes",

"Panel de Solicitudes - ECOLIMA": "Requests Panel - ECOLIMA",
"Volver al Home": "Back to Home",
"Panel de Control de Solicitudes": "Requests Control Panel",
"Revisa, previsualiza y autoriza las contribuciones de la comunidad escritora.": "Review, preview and authorize contributions from the writer community.",
"Pendientes": "Pending",
"La solicitud ha sido procesada correctamente.": "The request has been processed successfully.",
"Escritor": "Writer",
"Categoría": "Category",
"Título de la Publicación": "Post Title",
"Fecha de Envío": "Submission Date",
"No hay publicaciones pendientes de revisión por ahora.": "There are no pending posts to review at this time.",
"Previsualizar": "Preview",
"Aprobar y Publicar": "Approve and Publish",
"Rechazar Solicitud": "Reject Request",
"Cerrar": "Close",
"Especifica el motivo del rechazo. La publicación cambiará a estado rechazado.": "Specify the reason for rejection. The post will change to rejected status.",
"Escribe aquí el motivo detallado del rechazo...": "Write the detailed reason for rejection here...",
"Enviar": "Submit",

"Volver": "Back",
"Por:": "By:",
"Comentarios": "Comments",
"Escribe un comentario respetuoso...": "Write a respectful comment...",
"Publicar comentario": "Post comment",
"Debes": "You must",
"iniciar sesión": "log in",
"para dejar un comentario.": "to leave a comment.",
"Sé el primero en comentar esta publicación.": "Be the first to comment on this post.",
"El comentario no puede estar vacío.": "Comment cannot be empty.",

"Publicar en": "Publish in",
"Crear publicación": "Create post",
"¡Publicación creada! Pendiente de revisión.": "Post created! Pending review.",
"Error al publicar. Intenta de nuevo.": "Error publishing. Please try again.",
"Tipo de portada": "Cover type",
"Imagen": "Image",
"Video": "Video",
"Imagen de portada": "Cover image",
"Subir imagen": "Upload image",
"Video de portada": "Cover video",
"Seleccionar archivo de Video (.mp4, .webm)": "Select video file (.mp4, .webm)",
"Título": "Title",
"Escribe un título claro y atractivo...": "Write a clear and engaging title...",
"Subcategoría": "Subcategory",
"Cargando subcategorías...": "Loading subcategories...",
"Selecciona una subcategoría": "Select a subcategory",
"General (Sin subcategorías)": "General (No subcategories)",
"Error al cargar opciones": "Error loading options",
"Contenido": "Content",
"Comparte información valiosa sobre": "Share valuable information about",
"palabras": "words",
"Publicar": "Publish",
"Vista previa": "Preview",
"Sin portada": "No cover",
"Título de la publicación": "Post title",
"El contenido aparecerá aquí...": "Content will appear here...",
"Recortar": "Crop",
"Video seleccionado:": "Selected video:",
"Sin imagen": "No image",
"Sin video": "No video",

"Consejos": "Tips",
"Una categoría centrada en mostrar consejos para cuidar la vida terrestre": "A category focused on showing tips to take care of terrestrial life",
"Acciones individuales": "Individual actions",
"Acciones escolares": "School actions",
"Acciones comunitarias": "Community actions",
"Consumo responsable": "Responsible consumption",
"Buscar en consejos...": "Search in tips...",
"Consejos - ECOLIMA": "Tips - ECOLIMA"
};

let idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

// ============================================================
// TRADUCCIÓN DE INTERFAZ (RÁPIDA, SIN API)
// ============================================================
function ejecutarTraduccionLocal() {
    // Traducir textos con data-translate
    const elementosTexto = document.querySelectorAll("[data-translate]");
    elementosTexto.forEach(el => {
        const textoOriginal = el.textContent.trim();
        if (traducciones[textoOriginal]) {
            el.textContent = traducciones[textoOriginal];
        }
    });
    
    // Traducir placeholders
    const elementosPlaceholder = document.querySelectorAll("[data-translate-placeholder]");
    elementosPlaceholder.forEach(input => {
        const placeholderOriginal = input.getAttribute("placeholder");
        if (placeholderOriginal && traducciones[placeholderOriginal]) {
            input.setAttribute("placeholder", traducciones[placeholderOriginal]);
        }
    });
    
    console.log("[Ecolima Sync] Interfaz traducida");
}

// ============================================================
// TRADUCCIÓN DE TEXTO CON API (PARA PUBLICACIONES DINÁMICAS)
// ============================================================
async function traducirTextoAPI(texto) {
    if (!texto || texto.trim() === "" || idiomaActual !== 'en') return texto;
    
    try {
        const indiceAleatorio = Math.floor(Math.random() * POOL_DESARROLLADORES.length);
        const identificadorActivo = POOL_DESARROLLADORES[indiceAleatorio];
        const urlPeticion = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(texto)}&langpair=es|en&de=${identificadorActivo}`;
        
        const response = await fetch(urlPeticion);
        
        if (response.status === 429) {
            console.warn("[Ecolima Sync] Límite de API alcanzado, usando texto original");
            return texto;
        }
        
        const jsonResponse = await response.json();
        return jsonResponse.responseData.translatedText || texto;
    } catch (error) {
        console.error("Error traduciendo:", error);
        return texto;
    }
}

// ============================================================
// TRADUCCIÓN DE MÚLTIPLES TEXTOS (MÁS EFICIENTE)
// ============================================================
async function traducirBloque(textos) {
    if (textos.length === 0 || idiomaActual !== 'en') return textos;
    
    try {
        const bloque = textos.join(DELIMITADOR_BLOQUE);
        const indiceAleatorio = Math.floor(Math.random() * POOL_DESARROLLADORES.length);
        const identificadorActivo = POOL_DESARROLLADORES[indiceAleatorio];
        const urlPeticion = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(bloque)}&langpair=es|en&de=${identificadorActivo}`;
        
        const response = await fetch(urlPeticion);
        
        if (response.status === 429) {
            console.warn("[Ecolima Sync] Límite de API alcanzado");
            return textos;
        }
        
        const jsonResponse = await response.json();
        const traducidos = jsonResponse.responseData.translatedText.split(DELIMITADOR_BLOQUE);
        return traducidos.map(t => t.trim());
    } catch (error) {
        console.error("Error traduciendo bloque:", error);
        return textos;
    }
}

// ============================================================
// FUNCIÓN GLOBAL PARA QUE script.js PUEDA TRADUCIR PUBLICACIONES
// ============================================================
window.traducirPublicaciones = async function(publicaciones) {
    if (idiomaActual !== 'en') return publicaciones;
    
    // Extraer todos los títulos y descripciones para traducir en bloque
    const textosATraducir = [];
    publicaciones.forEach(pub => {
        textosATraducir.push(pub.titulo);
        textosATraducir.push(pub.descripcion);
    });
    
    const textosTraducidos = await traducirBloque(textosATraducir);
    
    // Reemplazar los textos traducidos
    let idx = 0;
    publicaciones.forEach(pub => {
        if (textosTraducidos[idx]) pub.titulo_traducido = textosTraducidos[idx];
        idx++;
        if (textosTraducidos[idx]) pub.descripcion_traducida = textosTraducidos[idx];
        idx++;
    });
    
    return publicaciones;
};

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener("DOMContentLoaded", async () => {
    const langBox = document.querySelector(".lang-box");
    
    if (langBox) {
        langBox.style.cursor = "pointer";
        langBox.textContent = (idiomaActual === 'es') ? "ES / EN" : "EN / ES";
        
        langBox.addEventListener("click", () => {
            idiomaActual = (idiomaActual === 'es') ? 'en' : 'es';
            localStorage.setItem("ecolima_lang", idiomaActual);
            
            if (idiomaActual === 'es') {
                location.reload();
            } else {
                langBox.textContent = "EN / ES";
                ejecutarTraduccionLocal();
                // Disparar evento para que script.js recargue y traduzca publicaciones
                window.dispatchEvent(new Event('idioma-cambiado'));
            }
        });
    }

    if (idiomaActual === 'en') {
        ejecutarTraduccionLocal();
    }
});

// Función para traducir bloque de publicación (para detalle_publicacion.php)
window.traducirBloquePublicacion = async function(textos) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return textos;
    
    try {
        const bloque = textos.join(DELIMITADOR_BLOQUE);
        const indiceAleatorio = Math.floor(Math.random() * POOL_DESARROLLADORES.length);
        const identificadorActivo = POOL_DESARROLLADORES[indiceAleatorio];
        const urlPeticion = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(bloque)}&langpair=es|en&de=${identificadorActivo}`;
        
        const response = await fetch(urlPeticion);
        
        if (response.status === 429) {
            console.warn("[Ecolima Sync] Límite de API alcanzado");
            return textos;
        }
        
        const jsonResponse = await response.json();
        const traducidos = jsonResponse.responseData.translatedText.split(DELIMITADOR_BLOQUE);
        return traducidos.map(t => t.trim());
    } catch (error) {
        console.error("Error traduciendo bloque de publicación:", error);
        return textos;
    }
};