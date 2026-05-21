 /* ─── TAB DATA ─── */
  const tabData = {
    megusta: [
      { title: "🌊 Costas de Colima – monitoreo de playas", body: "Revisamos las condiciones actuales de los arrecifes costeros y documentamos la biodiversidad marina presente en la temporada.", tag: "Ecosistemas" },
      { title: "🦋 Polinizadores urbanos en Colima capital", body: "Un proyecto colaborativo con escuelas locales para identificar especies de mariposas y abejas en parques y jardines.", tag: "Biodiversidad" },
      { title: "🌳 Reforestación en la sierra – avance", body: "Plantamos 3,200 árboles nativos de encino y copal en la zona alta. Los primeros datos de supervivencia superan el 85%.", tag: "Reforestación" },
    ],
    publicaciones: [],
    validaciones: [
      { title: "✅ Validación: Especie endémica confirmada", body: "El avistamiento reportado en el volcán de Colima fue validado como Dendrophthora colimaensis, especie endémica del estado.", tag: "Validado" },
    ],
  };

  /* ─── RENDER FEED ─── */
  let activeTab = 'megusta';

  function renderFeed(tab) {
    const list = document.getElementById('feedList');
    const posts = tabData[tab];
    list.innerHTML = '';

    if (posts.length === 0) {
      const card = document.createElement('div');
      card.className = 'feed-card';
      card.style.minHeight = '180px';
      card.innerHTML = `<div class="feed-empty" style="display:flex;flex-direction:column;align-items:center;gap:10px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Sin contenido todavía</span>
      </div>`;
      list.appendChild(card);
      return;
    }

    posts.forEach((p, i) => {
      const card = document.createElement('div');
      card.className = 'feed-card';
      card.style.animationDelay = (i * 0.07) + 's';
      card.innerHTML = `<div class="post-inner">
        <span class="post-tag">${p.tag}</span>
        <div class="post-title">${p.title}</div>
        <div class="post-body">${p.body}</div>
      </div>`;
      list.appendChild(card);
    });
  }

  function switchTab(tab, btn) {
    activeTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderFeed(tab);
  }

  /* ─── CONFIG DRAWER ─── */
  function openConfig() {
    document.getElementById('cfgName').value = document.querySelector('.profile-name').textContent.trim();
    document.getElementById('configOverlay').classList.add('open');
    document.getElementById('configDrawer').classList.add('open');
  }
  function closeConfig() {
    document.getElementById('configOverlay').classList.remove('open');
    document.getElementById('configDrawer').classList.remove('open');
  }
  function toggleSwitch(id) {
    document.getElementById(id).classList.toggle('on');
  }
  function guardarConfig() {
    const name = document.getElementById('cfgName').value.trim();
    if (name) document.querySelector('.profile-name').textContent = name;
    closeConfig();
    showToast('✅ Configuración guardada');
  }

  /* ─── NEW POST MODAL ─── */
  function openModal() {
    document.getElementById('modalOverlay').classList.add('open');
    setTimeout(() => document.getElementById('postTitle').focus(), 200);
  }
  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.getElementById('postTitle').value = '';
    document.getElementById('postBody').value = '';
  }
  function publicar() {
    const title = document.getElementById('postTitle').value.trim();
    const body  = document.getElementById('postBody').value.trim();
    if (!title && !body) { showToast('⚠️ Escribe algo antes de publicar'); return; }

    // Base de conexión: aquí se llamaría al API/backend
    const newPost = { title: title || '(Sin título)', body: body || '', tag: 'Nueva' };

    tabData.publicaciones.unshift(newPost);
    closeModal();
    showToast('🌿 Publicación creada');

    // Si ya estás en la pestaña publicaciones, refresca
    if (activeTab === 'publicaciones') renderFeed('publicaciones');
  }

  /* ─── CERRAR SESIÓN ─── */
  function cerrarSesion() {
    if (confirm('¿Seguro que quieres cerrar sesión?')) {
      showToast('👋 Cerrando sesión...');
      setTimeout(() => window.location.href = 'index.html', 1200);
    }
  }

  /* ─── TOAST ─── */
  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
  }

  /* ─── KEYBOARD ─── */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeConfig(); closeModal(); }
  });

  /* ─── INIT ─── */
  renderFeed('megusta');