<!-- ECOLIMA -->
<div align="center">
  <img src="assets/Home/Colima,%20volcan.jpg" width="800" alt="Volcán de Colima">
  <h1>🌋 ECOLIMA</h1>
  <p><em>Explorando la biodiversidad del estado de Colima</em></p>
</div>

---

## 📗 Descripción General

**ECOLIMA** es una plataforma web alineada con el **ODS 15: Vida de Ecosistemas Terrestres**, diseñada para difundir información confiable sobre la biodiversidad del estado de Colima.

El sitio permite explorar especies, ecosistemas y datos ambientales de forma clara y accesible, promoviendo el conocimiento del patrimonio natural del estado.

---

## 🎯 Objetivo

Facilitar el acceso a información ambiental sobre la biodiversidad de Colima, fomentando:

- 🌱 Educación ambiental
- 🌿 Conciencia ecológica
- 🦋 Conservación de los ecosistemas regionales

---

## 🎥 Video Comercial / Commercial Video

- [▶️ Ver video en español](https://youtu.be/GbpTECWwnts)
- [▶️ Watch video in English](https://youtu.be/HhfkJiZ9Z0w)

---

## ⚙️ Funcionalidades

---

### 🌍 Cinco categorías temáticas

*![Categorías](ruta-imagen)*

La biodiversidad de Colima organizada en cinco mundos por descubrir: **Flora**, **Fauna**, **Ecosistemas**, **Noticias** y **Consejos**. Cada categoría es una puerta a información curada y visual.

---

### 🌐 Cambio de idioma

*![Idioma](ruta-imagen)*

Una plataforma sin fronteras. Alterna entre **español e inglés** con un solo clic desde cualquier sección del sitio.

---

### 🌗 Modo oscuro / claro

*![Modo oscuro](ruta-imagen)*

Dos caras, una misma esencia. El usuario elige entre **modo oscuro y modo claro** según su preferencia, mejorando la experiencia visual y la accesibilidad.

---

### 🔐 Registro e inicio de sesión

*![Registro](ruta-imagen)*

Un acceso seguro y sin complicaciones. Registro con **validación de datos**, **encriptación de contraseña** y **detección de duplicados**. El sistema asigna roles automáticamente.

---

### ✍️ Publicaciones (Escritores)

*![Publicar](ruta-imagen)*

Dar voz a la naturaleza. Los escritores crean contenido mediante un **botón flotante** que despliega las categorías. La herramienta incluye **carga de imagen con recorte interactivo**, **vista previa en tiempo real** y **contador de palabras**.

---

### ✅ Validación (Editores)

*![Validar](ruta-imagen)*

Calidad antes que cantidad. Los editores **revisan, aprueban o rechazan** cada publicación antes de que vea la luz, asegurando contenido confiable.

---

### 👤 Perfil de usuario

*![Perfil](ruta-imagen)*

Un espacio personal. Cada usuario accede a su perfil con **información de cuenta**, **rol asignado** y **registro de actividad** en la plataforma.

---

### 📊 Panel de administración

*![Estadísticas](ruta-imagen)*

El centro de mando. El administrador visualiza **métricas en tiempo real**: publicaciones por categoría, usuarios registrados y actividad del sitio.

---

### 🔍 Búsqueda inteligente

*![Búsqueda](ruta-imagen)*

Encuentra en segundos. La barra de búsqueda rastrea **títulos y contenido** en todas las categorías para ofrecer resultados inmediatos.

---

## 📁 Estructura del Proyecto

Ecolima/
├── index.php # Punto de entrada
├── README.md # Documentación
├── LICENSE # Licencia MIT
├── Database/
│ └── ecolima.sql # Respaldo de la base de datos
│
├── PHP/
│ ├── buscar.php # Buscador global
│ │
│ ├── Home/
│ │ ├── home.php # Página principal
│ │ └── buscar.php # Backend de búsqueda
│ │
│ ├── Login/
│ │ ├── login.php # Inicio de sesión
│ │ ├── registro.php # Registro
│ │ ├── logout.php # Cerrar sesión
│ │ ├── procesar_login.php
│ │ └── procesar_registro.php
│ │
│ ├── Perfil/
│ │ ├── perfil.php # Perfil de usuario
│ │ ├── dashboard_perfil.php # Panel administrador
│ │ ├── dashboard_estadisticas.php
│ │ └── conexion.php # Conexión a la BD
│ │
│ ├── Fauna/
│ ├── Flora/
│ ├── Ecosistemas/
│ ├── Noticias/
│ ├── Consejos/ # Una carpeta por categoría
│ │ ├── categoria.php
│ │ └── obtener_publicaciones.php
│ │
│ ├── Publicar/
│ │ ├── publicar.php # Interfaz de publicación
│ │ └── procesar_publicacion.php
│ │
│ └── Editor/
│ ├── administrar_solicitudes.php
│ └── procesar_revision.php
│
├── CSS/
│ ├── Home/
│ ├── Fauna/
│ ├── Flora/
│ ├── Ecosistemas/
│ ├── Noticias/
│ ├── Consejos/
│ ├── Perfil/
│ ├── Login/
│ └── Publicar/ # Una carpeta por sección
│ ├── styles.css # Modo claro
│ └── styles_oscuro.css # Modo oscuro
│
├── JavaScript/
│ ├── Fauna/
│ ├── Flora/
│ ├── Ecosistemas/
│ ├── Noticias/
│ ├── Consejos/
│ ├── Perfil/
│ └── Dashboard/ # Una carpeta por sección
│ └── script.js
│
└── assets/
├── Home/ # Imágenes de la página principal
├── Fauna/ # Imágenes de la categoría
├── Flora/
├── Ecosistemas/
├── Noticias/
├── Consejos/
├── Login/ # Imágenes del login
├── Perfil/ # Fotos de perfil
├── Publicaciones/ # Imágenes subidas por usuarios
├── Fotos_perfil/
├── Fotos_banner/
└── Fotos_post/

---
## 🌄 Inspiración

Colima posee una riqueza natural única: volcanes, selvas, costas y una gran diversidad de especies. ECOLIMA busca ser una herramienta digital que permita explorar, aprender y valorar estos ecosistemas.

---

## 🛠️ Tecnologías

| Área | Herramientas |
|------|-------------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Base de datos | MySQL |
| Control de versiones | Git & GitHub |
| Servidor local | XAMPP |

---

## 👥 Equipo

| Integrante | Rol |
|-----------|-----|
| Alan Ibarra | Desarrollo |
| Carolina Zúñiga | Desarrollo |
| Dana Nava | Desarrollo |
| Miranda Montiel | Desarrollo |
| Ricardo Barba | Desarrollo |

**Facultad de Ingeniería Electromecánica — Universidad de Colima**

---

## 📄 Licencia

Este proyecto está bajo la [Licencia MIT](LICENSE). Eres libre de usar, modificar y distribuir el software, manteniendo el aviso de autoría original.
