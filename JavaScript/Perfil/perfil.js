/* ========================================================= */
/* ─── DATA DE HISTORIALES INTERNOS (LOCALES) ────────────── */
/* ========================================================= */
const tabData = {
    megusta: [
        { title: "🌊 Costas de Colima – monitoreo de playas", body: "Revisamos las condiciones actuales de los arrecifes costeros y documentamos la biodiversidad marina presente en la temporada.", tag: "Ecosistemas" },
        { title: "🦋 Polinizadores urbanos en Colima capital", body: "Un proyecto colaborativo con escuelas locales para identificar especies de mariposas y abejas en parques y jardines.", tag: "Biodiversidad" },
        { title: "🌳 Reforestación en la sierra – avance", body: "Plantamos 3,200 árboles nativos de encino y copal en la zona alta. Los primeros datos de supervivencia superan el 85%.", tag: "Reforestación" },
    ],
    mispublicaciones: [], 
    validaciones: [
        { title: "✅ Validación: Especie endémica confirmada", body: "El avistamiento reportado en el volcán de Colima fue validado como Dendrophthora colimaensis, especie endémica del estado.", tag: "Validado" },
    ],
};

// Variables globales para rastrear el estado del feed
let activeTab = 'megusta';
let pestañaActiva = 'megusta'; 

// Función para traducir textos
function traducirTexto(texto) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return texto;
    if (typeof traducciones !== 'undefined' && traducciones[texto]) {
        return traducciones[texto];
    }
    return texto;
}

// Función para traducir tags
function traducirTag(tag) {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    if (idiomaActual !== 'en') return tag;
    
    const tagsTraducidos = {
        'Ecosistemas': 'Ecosystems',
        'Biodiversidad': 'Biodiversity',
        'Reforestación': 'Reforestation',
        'Validado': 'Validated',
        'Nueva': 'New'
    };
    
    return tagsTraducidos[tag] || tag;
}

/* ========================================================= */
/* ─── RENDERIZADO LOCAL DEL FEED ────────────────────────── */
/* ========================================================= */
function renderFeed(tab) {
    const list = document.getElementById('feedList');
    if (!list) return;
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const posts = tabData[tab];
    list.innerHTML = '';

    if (!posts || posts.length === 0) {
        const mensajeVacio = idiomaActual === 'en' 
            ? "No content yet"
            : "Sin contenido todavía";
            
        const card = document.createElement('div');
        card.className = 'feed-card';
        card.style.minHeight = '180px';
        card.innerHTML = `<div class="feed-empty" style="display:flex;flex-direction:column;align-items:center;gap:10px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>${mensajeVacio}</span>
        </div>`;
        list.appendChild(card);
        return;
    }

    posts.forEach((p, i) => {
        const card = document.createElement('div');
        card.className = 'feed-card';
        card.style.animationDelay = (i * 0.07) + 's';
        const tagTraducido = traducirTag(p.tag);
        card.innerHTML = `<div class="post-inner">
            <span class="post-tag">${tagTraducido}</span>
            <div class="post-title">${p.title}</div>
            <div class="post-body">${p.body}</div>
        </div>`;
        list.appendChild(card);
    });
}

/* ========================================================= */
/* ─── INTERCAMBIO ENTRE PESTAÑAS ────────────────────────── */
/* ========================================================= */
function switchTab(tabName, boton) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    if (boton) boton.classList.add('active');
    
    pestañaActiva = tabName;
    activeTab = tabName; 

    if (tabName === 'megusta') {
        cargarLikesUsuario();
    } else if (tabName === 'mispublicaciones') {
        cargarMisPublicaciones(); 
    } else {
        renderFeed(tabName);
    }
}

/* ========================================================= */
/* ─── INICIALIZADOR ÚNICO (DOMContentLoaded) ────────────── */
/* ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    
    // Escuchar cambios de idioma para recargar
    window.addEventListener('idioma-cambiado', () => {
        console.log("[Perfil] Idioma cambiado, recargando pestaña actual...");
        if (activeTab === 'megusta') {
            cargarLikesUsuario();
        } else if (activeTab === 'mispublicaciones') {
            cargarMisPublicaciones();
        } else {
            renderFeed(activeTab);
        }
    });
    
    // 1. Cargar pestaña por defecto al iniciar
    if (pestañaActiva === 'megusta') {
        cargarLikesUsuario();
    } else if (pestañaActiva === 'mispublicaciones') {
        cargarMisPublicaciones();
    } else {
        renderFeed(pestañaActiva);
    }

    // 2. 🌟 Sincronizar el interruptor de Modo Oscuro directo a la Base de Datos
    const switchModoOscuro = document.getElementById("switchModoOscuro");
    if (switchModoOscuro) {
        switchModoOscuro.addEventListener("change", async () => {
            const esOscuro = switchModoOscuro.checked ? 1 : 0;

            try {
                const response = await fetch("../../PHP/Perfil/actualizar_tema.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `modo_oscuro=${esOscuro}`
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    location.reload(); 
                } else {
                    console.error("El servidor remoto no pudo actualizar la preferencia.");
                }
            } catch (error) {
                console.error("Error de comunicación asíncrona con el servidor:", error);
            }
        });
    }

    // 3. Escucha del Formulario de Solicitud de Roles
    const formSolicitud = document.getElementById('formSolicitudRol');
    if (formSolicitud) {
        formSolicitud.addEventListener('submit', function(e) {
            e.preventDefault();
            const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
            const formData = new FormData(formSolicitud);

            const tituloCarga = idiomaActual === 'en' ? 'Sending request...' : 'Enviando solicitud...';
            const textoCarga = idiomaActual === 'en' ? 'Please wait a moment.' : 'Por favor, espera un momento.';

            Swal.fire({
                title: tituloCarga,
                text: textoCarga,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('procesar_solicitud_rol.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const tituloExito = idiomaActual === 'en' ? 'Sent!' : '¡Enviada!';
                    const textoExito = idiomaActual === 'en' 
                        ? 'Your request has been registered and is pending review.'
                        : 'Tu solicitud ha sido registrada correctamente y está en espera de revisión.';
                    Swal.fire({
                        title: tituloExito,
                        text: textoExito,
                        icon: 'success',
                        confirmButtonColor: '#2d6a4f'
                    }).then(() => {
                        formSolicitud.reset();
                        switchSection('panel-perfil');
                    });
                } else {
                    Swal.fire('Error', data.message || 'No se pudo registrar la solicitud.', 'error');
                }
            })
            .catch(err => {
                console.error("Error al enviar solicitud:", err);
                Swal.fire('Error', 'Hubo un problema de conexión con el servidor.', 'error');
            });
        });
    }
});

/* ========================================================= */
/* ─── VISTAS Y SECCIONES DE LA BARRA LATERAL (SIDEBAR) ──── */
/* ========================================================= */
function switchSection(sectionId) {
    const secPerfil = document.getElementById('section-perfil');
    const secPermisos = document.getElementById('section-permisos');
    const panelFeed = document.getElementById('panel-feed-izquierdo');

    if (secPerfil) secPerfil.style.display = 'none';
    if (secPermisos) secPermisos.style.display = 'none';
    
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => item.classList.remove('active'));
    
    if (sectionId === 'panel-perfil' || sectionId === 'section-perfil') {
        if (secPerfil) secPerfil.style.display = 'contents';
        if (panelFeed) panelFeed.style.display = 'block';
        const btn = document.querySelector('[onclick*="panel-perfil"]');
        if (btn) btn.classList.add('active');
    } else if (sectionId === 'panel-permisos' || sectionId === 'section-permisos') {
        if (secPermisos) secPermisos.style.display = 'block';
        if (panelFeed) panelFeed.style.display = 'none'; 
        const btn = document.querySelector('[onclick*="panel-permisos"]');
        if (btn) btn.classList.add('active');
    }
}

/* ========================================================= */
/* ─── CONFIG DRAWER (DATOS DE USUARIO) ──────────────────── */
/* ========================================================= */
function openConfig() {
    const pName = document.querySelector('.profile-name');
    if (pName) document.getElementById('cfgName').value = pName.textContent.trim();
    document.getElementById('configOverlay').classList.add('open');
    document.getElementById('configDrawer').classList.add('open');
}

function closeConfig() {
    document.getElementById('configOverlay').classList.remove('open');
    document.getElementById('configDrawer').classList.remove('open');
}

function toggleSwitch(id) {
    const sw = document.getElementById(id);
    if (sw) sw.classList.toggle('on');
}

async function guardarConfig() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const name = document.getElementById('cfgName').value.trim();
    const email = document.getElementById('cfgEmail').value.trim();
    const passActual = document.getElementById('cfgPassActual').value;
    const passNueva = document.getElementById('cfgPass').value;

    const camposTitulo = idiomaActual === 'en' ? 'Required fields' : 'Campos obligatorios';
    const camposMsg = idiomaActual === 'en' ? 'Name and email cannot be empty.' : 'El nombre y correo no pueden estar vacíos.';
    
    if (!name || !email) {
        Swal.fire(camposTitulo, camposMsg, 'warning');
        return;
    }

    if (passNueva.length > 0) {
        const seguridadTitulo = idiomaActual === 'en' ? 'Security' : 'Seguridad';
        const seguridadMsg = idiomaActual === 'en' 
            ? 'You must enter your current password to set a new one.'
            : 'Debes escribir tu contraseña actual para poder asignar una nueva.';
        const invalidaMsg = idiomaActual === 'en' 
            ? 'The new password must be between 8 and 32 characters.'
            : 'La nueva contraseña debe tener entre 8 y 32 caracteres.';
            
        if (passActual.length === 0) {
            Swal.fire(seguridadTitulo, seguridadMsg, 'warning');
            return;
        }
        if (passNueva.length < 8 || passNueva.length > 32) {
            Swal.fire('Contraseña inválida', invalidaMsg, 'warning');
            return;
        }
    }

    const formData = new FormData();
    formData.append('username', name);
    formData.append('email', email);
    formData.append('password_actual', passActual);
    formData.append('password_nueva', passNueva);

    try {
        const procesandoTitulo = idiomaActual === 'en' ? 'Processing...' : 'Procesando...';
        const procesandoTexto = idiomaActual === 'en' ? 'Please wait a moment.' : 'Espere un momento, por favor.';
        
        Swal.fire({
            title: procesandoTitulo,
            text: procesandoTexto,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let response = await fetch('../../PHP/Perfil/actualizar_configuracion.php', {
            method: 'POST',
            body: formData
        });
        let data = await response.json();

        if (data.status === 'need_verification') {
            const { value: codigoIngresado } = await Swal.fire({
                title: 'Confirma tu nuevo correo',
                text: data.message,
                input: 'text',
                inputPlaceholder: '000000',
                allowOutsideClick: false,
                confirmButtonColor: '#2d6a4f',
                confirmButtonText: 'Verificar y Guardar',
                cancelButtonText: 'Cancelar',
                showCancelButton: true,
                inputAttributes: { 
                    maxlength: 6, 
                    style: 'text-align: center; letter-spacing: 5px; font-weight: bold; font-size: 24px;' 
                },
                inputValidator: (value) => {
                    if (!value || value.length !== 6 || isNaN(value)) {
                        return 'Debes introducir el código numérico de 6 dígitos.';
                    }
                }
            });

            if (!codigoIngresado) {
                Swal.close();
                return;
            }

            formData.append('codigo_verificacion', codigoIngresado);

            Swal.fire({
                title: 'Verificando código...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            response = await fetch('../../PHP/Perfil/actualizar_configuracion.php', {
                method: 'POST',
                body: formData
            });
            data = await response.json();
        }

        if (data.status === 'success') {
            const exitoTitulo = idiomaActual === 'en' ? 'Updated!' : '¡Actualizado!';
            Swal.fire(exitoTitulo, data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }

    } catch (error) {
        console.error('Error al actualizar la configuración:', error);
        Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
    }
}

/* ========================================================= */
/* ─── CONSUMO BASE DE DATOS REAL (PHP / AJAX) ───────────── */
/* ========================================================= */
async function cargarLikesUsuario() {
    const feedList = document.getElementById('feedList');
    if (!feedList) return;
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

    try {
        const response = await fetch('../../PHP/Perfil/obtener_likes_usuario.php');
        if (!response.ok) throw new Error('Error al conectar con el servidor');

        let posts = await response.json();
        feedList.innerHTML = '';

        if (!Array.isArray(posts) || posts.length === 0) {
            renderFeed('megusta');
            return;
        }
        
        // Traducir títulos y descripciones si es necesario
        if (idiomaActual === 'en' && typeof window.traducirPublicaciones === 'function') {
            const postsParaTraducir = posts.map(post => ({
                titulo: post.titulo,
                descripcion: post.descripcion
            }));
            const traducidas = await window.traducirPublicaciones(postsParaTraducir);
            traducidas.forEach((pub, idx) => {
                if (pub.titulo_traducido) posts[idx].titulo_traducido = pub.titulo_traducido;
                if (pub.descripcion_traducida) posts[idx].descripcion_traducida = pub.descripcion_traducida;
            });
        }

        posts.forEach(post => {
            let tituloMostrar = post.titulo;
            let descMostrar = post.descripcion;
            
            if (idiomaActual === 'en') {
                if (post.titulo_traducido) tituloMostrar = post.titulo_traducido;
                if (post.descripcion_traducida) descMostrar = post.descripcion_traducida;
            }
            
            const item = document.createElement('div');
            item.className = 'feed-item-card';
            item.style.display = 'flex';
            item.style.gap = '16px';
            item.style.background = 'var(--tarjetas-bg, #ffffff)'; 
            item.style.padding = '16px';
            item.style.borderRadius = '14px';
            item.style.marginBottom = '16px';
            item.style.boxShadow = '0 2px 12px rgba(0,0,0,0.04)';
            item.style.alignItems = 'center';

            item.innerHTML = `
                <img src="${post.imagen}" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0; background: #f1f5f9;">
                <div style="flex-grow: 1; min-width: 0;">
                    <h4 style="margin: 0 0 4px 0; font-size: 1rem; color: var(--texto-sitio, #1a3a2a); font-family: 'Nunito', sans-serif; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${escapeHTML(tituloMostrar)}
                    </h4>
                    <p style="margin: 0 0 6px 0; font-size: 0.85rem; color: #64748b; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        ${escapeHTML(descMostrar)}
                    </p>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700;">
                        <span>${traducirTexto('Por')} @${post.autor}</span> • <span>${post.fecha}</span>
                    </div>
                </div>
                <div style="flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" fill="#e63946" stroke="#e63946" stroke-width="2" style="width: 20px; height: 20px;">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
            `;
            feedList.appendChild(item);
        });
    } catch (error) {
        console.error('Error cargando el feed de likes remotos, usando locales:', error);
        renderFeed('megusta');
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

async function cargarMisPublicaciones() {
    const feedList = document.getElementById('feedList');
    if (!feedList) return;
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

    try {
        const response = await fetch('../../PHP/Perfil/obtener_mis_publicaciones.php');
        if (!response.ok) throw new Error('Error al conectar con el servidor');

        let posts = await response.json();
        feedList.innerHTML = ''; 

        if (!Array.isArray(posts) || posts.length === 0) {
            renderFeed('mispublicaciones');
            return;
        }
        
        // Traducir títulos si es necesario
        if (idiomaActual === 'en' && typeof window.traducirPublicaciones === 'function') {
            const postsParaTraducir = posts.map(post => ({
                titulo: post.titulo,
                descripcion: post.descripcion
            }));
            const traducidas = await window.traducirPublicaciones(postsParaTraducir);
            traducidas.forEach((pub, idx) => {
                if (pub.titulo_traducido) posts[idx].titulo_traducido = pub.titulo_traducido;
                if (pub.descripcion_traducida) posts[idx].descripcion_traducida = pub.descripcion_traducida;
            });
        }

        const switchOscuro = document.getElementById('switchModoOscuro');
        const esModoOscuro = switchOscuro && switchOscuro.checked;

        // Traducciones de estados
        const estadosTraducidos = {
            'publicado': idiomaActual === 'en' ? 'Published' : 'Aceptada',
            'aceptado': idiomaActual === 'en' ? 'Accepted' : 'Aceptada',
            'pendiente': idiomaActual === 'en' ? 'Pending' : 'Pendiente',
            'rechazado': idiomaActual === 'en' ? 'Rejected' : 'Rechazada'
        };
        
        const motivoLabel = idiomaActual === 'en' ? 'Reason for rejection:' : 'Motivo de rechazo:';
        const creadoLabel = idiomaActual === 'en' ? 'Created on' : 'Creado el';

        posts.forEach(post => {
            let tituloMostrar = post.titulo;
            let descMostrar = post.descripcion;
            
            if (idiomaActual === 'en') {
                if (post.titulo_traducido) tituloMostrar = post.titulo_traducido;
                if (post.descripcion_traducida) descMostrar = post.descripcion_traducida;
            }
            
            const item = document.createElement('div');
            item.className = 'feed-item-card';
            item.style.display = 'flex';
            item.style.gap = '16px';
            
            item.style.background = esModoOscuro ? '#1e293b' : 'var(--tarjetas-bg, #ffffff)';
            item.style.padding = '16px';
            item.style.borderRadius = '14px';
            item.style.marginBottom = '16px';
            item.style.boxShadow = esModoOscuro ? '0 4px 14px rgba(0,0,0,0.3)' : '0 2px 12px rgba(0,0,0,0.04)';
            item.style.alignItems = 'flex-start';
            item.style.border = esModoOscuro ? '1px solid #334155' : 'none';

            let badgeColor = esModoOscuro ? '#334155' : '#e2e8f0'; 
            let badgeTextColor = esModoOscuro ? '#cbd5e1' : '#475569';
            let estadoTexto = estadosTraducidos[post.estado] || post.estado;
            let mostrarMotivoHtml = '';

            if (post.estado === 'publicado' || post.estado === 'aceptado') {
                badgeColor = esModoOscuro ? '#064e3b' : '#d1fae5';
                badgeTextColor = esModoOscuro ? '#6ee7b7' : '#065f46';
            } else if (post.estado === 'pendiente') {
                badgeColor = esModoOscuro ? '#78350f' : '#fef3c7';
                badgeTextColor = esModoOscuro ? '#fde68a' : '#92400e';
            } else if (post.estado === 'rechazado') {
                badgeColor = esModoOscuro ? '#7f1d1d' : '#fee2e2';
                badgeTextColor = esModoOscuro ? '#fca5a5' : '#991b1b';
                
                if (post.motivo_rechazo) {
                    const fondoMotivo = esModoOscuro ? '#2d3748' : '#fff5f5';
                    const textoMotivo = esModoOscuro ? '#fca5a5' : '#991b1b';
                    const bordeMotivo = '#ef4444';

                    mostrarMotivoHtml = `
                        <div style="margin-top: 8px; padding: 10px; background: ${fondoMotivo}; border-left: 3px solid ${bordeMotivo}; border-radius: 4px; font-size: 0.8rem; color: ${textoMotivo}; width: 100%; box-sizing: border-box;">
                            <strong>${motivoLabel}</strong> ${escapeHTML(post.motivo_rechazo)}
                        </div>
                    `;
                }
            }

            const colorTitulo = esModoOscuro ? '#ffffff' : 'var(--texto-sitio, #1a3a2a)';
            const colorDescripcion = esModoOscuro ? '#cbd5e1' : '#64748b';
            const colorFecha = esModoOscuro ? '#94a3b8' : '#94a3b8';

            item.innerHTML = `
                <img src="${post.imagen}" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0; background: #f1f5f9; margin-top: 4px;">
                <div style="flex-grow: 1; min-width: 0; width: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; gap: 8px;">
                        <h4 style="margin: 0; font-size: 1rem; color: ${colorTitulo}; font-family: 'Nunito', sans-serif; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${escapeHTML(tituloMostrar)}
                        </h4>
                        <span style="background: ${badgeColor}; color: ${badgeTextColor}; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; flex-shrink: 0;">
                            ${estadoTexto}
                        </span>
                    </div>
                    <p style="margin: 0 0 6px 0; font-size: 0.85rem; color: ${colorDescripcion}; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        ${escapeHTML(descMostrar)}
                    </p>
                    <div style="font-size: 0.75rem; color: ${colorFecha}; font-weight: 700;">
                        <span>${creadoLabel} ${post.fecha}</span>
                    </div>
                    ${mostrarMotivoHtml}
                </div>
            `;
            feedList.appendChild(item);
        });
    } catch (error) {
        console.error('Error cargando publicaciones del servidor. Cargando temporales locales:', error);
        renderFeed('mispublicaciones');
    }
}

/* ========================================================= */
/* ─── MODAL DE NUEVA PUBLICACIÓN LOCAL ──────────────────── */
/* ========================================================= */
function openModal() {
    document.getElementById('modalOverlay').classList.add('open');
    setTimeout(() => {
        const pTitle = document.getElementById('postTitle');
        if (pTitle) pTitle.focus();
    }, 200);
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.getElementById('postTitle').value = '';
    document.getElementById('postBody').value = '';
}

function publicar() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const title = document.getElementById('postTitle').value.trim();
    const body  = document.getElementById('postBody').value.trim();
    const msgVacio = idiomaActual === 'en' ? '⚠️ Write something before publishing' : '⚠️ Escribe algo antes de publicar';
    if (!title && !body) { showToast(msgVacio); return; }

    const newPost = { title: title || (idiomaActual === 'en' ? '(No title)' : '(Sin título)'), body: body || '', tag: idiomaActual === 'en' ? 'New' : 'Nueva' };
    
    tabData.mispublicaciones.unshift(newPost);
    closeModal();
    showToast(idiomaActual === 'en' ? '🌿 Post created locally' : '🌿 Publicación creada localmente');

    if (activeTab === 'mispublicaciones') {
        renderFeed('mispublicaciones');
    }
}

/* ========================================================= */
/* ─── UPLOAD DE MULTIMEDIA E INFORMACIÓN ADICIONAL ──────── */
/* ========================================================= */
function subirImagenAutomatica(tipo) {
    const input = tipo === 'avatar' ? document.getElementById('input-avatar') : document.getElementById('input-banner');
    if (!input) return;
    const archivo = input.files[0];
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const msgImagen = idiomaActual === 'en' ? '⚠️ Please select a valid image file.' : '⚠️ Por favor, selecciona un archivo de imagen válido.';
    
    if (!archivo) return;
    if (!archivo.type.startsWith('image/')) {
        showToast(msgImagen);
        return;
    }

    const usuarioId = input.getAttribute('data-usuario-id');
    const formData = new FormData();
    let urlDestino = '';
    
    formData.append('usuario_id_alterno', usuarioId); 

    if (tipo === 'avatar') {
        formData.append('foto', archivo);
        urlDestino = 'subir_foto.php'; 
    } else {
        formData.append('banner_perfil', archivo);
        urlDestino = 'subir_banner.php';
    }

    const msgSubiendo = idiomaActual === 'en' ? '⏳ Uploading image...' : '⏳ Subiendo imagen...';
    showToast(msgSubiendo);

    fetch(urlDestino, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) 
    .then(textoOriginal => {
        try {
            const data = JSON.parse(textoOriginal);
            if (data.status === 'success') {
                const timestamp = new Date().getTime();
                const msgExito = idiomaActual === 'en' ? '✅ Profile picture updated!' : '✅ ¡Foto de perfil actualizada!';
                const msgBannerExito = idiomaActual === 'en' ? '🌿 Cover banner updated!' : '🌿 ¡Banner de portada actualizado!';
                
                if (tipo === 'avatar') {
                    const nuevaRuta = '../../assets/Fotos_perfil/' + data.archivo;
                    document.querySelector('.profile-avatar').src = nuevaRuta + '?t=' + timestamp;
                    showToast(msgExito);
                } else {
                    const nombreArchivoBanner = data.archivo || data.banner || data.ruta_nueva;
                    const nuevaRutaBanner = nombreArchivoBanner.includes('/') ? nombreArchivoBanner : '../../assets/Fotos_banner/' + nombreArchivoBanner;
                    document.querySelector('.profile-hero .cover').src = nuevaRutaBanner + '?t=' + timestamp;
                    showToast(msgBannerExito);
                }
            } else {
                showToast('❌ Error: ' + (data.message || 'No se pudo subir la imagen.'));
            }
        } catch (err) {
            console.error("Error de respuesta del servidor:", textoOriginal);
            showToast('⚠️ Hubo un detalle en la respuesta del servidor.');
        }
    })
    .catch(error => {
        console.error('Error al subir:', error);
        showToast('❌ Error de comunicación con el servidor.');
    });
}

function guardarBiografia(nuevoTexto) {
    const bioElement = document.getElementById('bioElement');
    if (!bioElement) return;
    
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

    const textoLimpio = nuevoTexto.replace(/^"|"$/g, '').trim();
    const inputAvatar = document.getElementById('input-avatar');
    const usuarioId = inputAvatar ? inputAvatar.getAttribute('data-usuario-id') : null;

    const formData = new FormData();
    formData.append('biografia', textoLimpio);
    formData.append('usuario_id_alterno', usuarioId);

    fetch('guardar_biografia.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const msgExito = idiomaActual === 'en' ? '📝 Biography saved successfully' : '📝 Biografía guardada con éxito';
            showToast(msgExito);
        } else {
            showToast('❌ ' + data.message);
        }
    })
    .catch(err => {
        console.error("Error al guardar biografía:", err);
    });
}

/* ========================================================= */
/* ─── UTILIDADES (TOASTS, CERRAR SESIÓN, TECLADO) ───────── */
/* ========================================================= */
function cerrarSesion() {
    const idiomaActual = localStorage.getItem("ecolima_lang") || 'es';
    const titulo = idiomaActual === 'en' ? 'Are you sure you want to logout?' : '¿Seguro que quieres salir?';
    const texto = idiomaActual === 'en' ? 'You will have to log in again.' : 'Tendrás que iniciar sesión de nuevo.';
    const confirmarTexto = idiomaActual === 'en' ? 'Yes, logout' : 'Sí, salir';
    const cancelarTexto = idiomaActual === 'en' ? 'Cancel' : 'Cancelar';
    
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5B9760',
        cancelButtonColor: '#b0b0b0',
        confirmButtonText: confirmarTexto,
        cancelButtonText: cancelarTexto,
        background: 'var(--white)',
        color: 'var(--text-dark)'
    }).then((result) => {
        if (result.isConfirmed) {
            const msgCerrando = idiomaActual === 'en' ? '👋 Logging out...' : '👋 Cerrando sesión...';
            showToast(msgCerrando);
            setTimeout(() => window.location.href = 'index.html', 1200);
        }
    });
}

function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeConfig(); closeModal(); }
});