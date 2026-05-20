/**
 * @fileoverview Sistema Core de Traducción Dinámica e Individual - Proyecto Ecolima
 * @version 3.1.0
 */

const POOL_DESARROLLADORES = [
    "carolina.ecolima@gmail.com",
    "alan.ecolima@gmail.com",
    "diego.ecolima@gmail.com",
    "miranda.ecolima@gmail.com"
];

let idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

document.addEventListener("DOMContentLoaded", async () => {
    const langBox = document.querySelector(".lang-box");
    
    if (langBox) {
        langBox.style.cursor = "pointer";
        langBox.textContent = (idiomaActual === 'es') ? "ES / EN" : "EN / ES";
        
        langBox.addEventListener("click", async () => {
            idiomaActual = (idiomaActual === 'es') ? 'en' : 'es';
            localStorage.setItem("ecolima_lang", idiomaActual);
            
            if (idiomaActual === 'es') {
                location.reload(); // Vuelve al estado nativo en Español de PHP
            } else {
                langBox.textContent = "EN / ES";
                await ejecutarTraduccionMasiva();
            }
        });
    }

    // Si al cargar el sitio la persistencia está en inglés, ejecutamos la traducción
    if (idiomaActual === 'en') {
        await ejecutarTraduccionMasiva();
    }
});

async function ejecutarTraduccionMasiva() {
    const elementosTexto = document.querySelectorAll("[data-translate]");
    const elementosPlaceholder = document.querySelectorAll("[data-translate-placeholder]");

    // --- Traducir Textos de Interfaz de forma segura (Individual e Inmediata) ---
    const promesasTexto = Array.from(elementosTexto).map(async (el) => {
        // Evitamos traducir nodos vacíos o que ya se hayan guardado en un estado intermedio
        const textoOriginal = el.textContent.trim();
        if (textoOriginal.length > 0) {
            let traduccion = await consumirApiTraduccion(textoOriginal);
            if (traduccion) {
                el.textContent = traduccion;
            }
        }
    });

    // --- Traducir Placeholders de forma segura ---
    const promesasPlaceholder = Array.from(elementosPlaceholder).map(async (input) => {
        const placeholderOriginal = input.getAttribute("placeholder") || "";
        if (placeholderOriginal.trim().length > 0) {
            let traduccionPl = await consumirApiTraduccion(placeholderOriginal);
            if (traduccionPl) {
                input.setAttribute("placeholder", traduccionPl);
            }
        }
    });

    // Ejecutamos todos los hilos de traducción en paralelo para máxima velocidad
    await Promise.all([...promesasTexto, ...promesasPlaceholder]);
}

async function consumirApiTraduccion(texto) {
    try {
        const indiceAleatorio = Math.floor(Math.random() * POOL_DESARROLLADORES.length);
        const identificadorActivo = POOL_DESARROLLADORES[indiceAleatorio];
        const urlPeticion = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(texto)}&langpair=es|en&de=${identificadorActivo}`;

        const response = await fetch(urlPeticion);
        if (!response.ok) throw new Error(`HTTP Status ${response.status}`);

        const jsonResponse = await response.json();
        return jsonResponse.responseData.translatedText;
    } catch (error) {
        console.error("Error al traducir el fragmento:", texto, error);
        return null;
    }
}