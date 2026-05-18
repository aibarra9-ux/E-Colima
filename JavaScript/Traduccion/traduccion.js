/**
 * @fileoverview Sistema Core de Traducción Dinámica con Persistencia - Proyecto Ecolima
 * @version 3.0.0
 */

const POOL_DESARROLLADORES = [
    "carolina.ecolima@gmail.com",
    "alan.ecolima@gmail.com",
    "diego.ecolima@gmail.com",
    "miranda.ecolima@gmail.com"
];

const DELIMITADOR_BLOQUE = "|||";

// Sistema de persistencia: Recupera el idioma del almacenamiento del navegador o por defecto usa español 'es'
let idiomaActual = localStorage.getItem("ecolima_lang") || 'es';

document.addEventListener("DOMContentLoaded", async () => {
    const langBox = document.querySelector(".lang-box");
    
    // Configuración inicial del botón en base al almacenamiento persistente
    if (langBox) {
        langBox.style.cursor = "pointer";
        langBox.textContent = (idiomaActual === 'es') ? "ES / EN" : "EN / ES";
        
        langBox.addEventListener("click", async () => {
            idiomaActual = (idiomaActual === 'es') ? 'en' : 'es';
            localStorage.setItem("ecolima_lang", idiomaActual); // Guardamos la elección del usuario
            
            if (idiomaActual === 'es') {
                location.reload();
            } else {
                langBox.textContent = "EN / ES";
                await ejecutarTraduccionMasiva();
            }
        });
    }

    // FLUJO PERSISTENTE AUTOMÁTICO: Si la página carga y el almacenamiento dice 'en', se traduce sola inmediatamente
    if (idiomaActual === 'en') {
        await ejecutarTraduccionMasiva();
    }
});

async function ejecutarTraduccionMasiva() {
    const elementosTexto = Array.from(document.querySelectorAll("[data-translate]"));
    const elementosPlaceholder = Array.from(document.querySelectorAll("[data-translate-placeholder]"));

    // --- TEXTOS DE INTERFAZ ---
    if (elementosTexto.length > 0) {
        let arrayTextos = elementosTexto.map(el => el.textContent.trim());
        let paqueteTextos = arrayTextos.join(DELIMITADOR_BLOQUE);

        let respuestaServidor = await consumirApiTraduccion(paqueteTextos);
        
        if (respuestaServidor) {
            let textosTraducidos = respuestaServidor.split(DELIMITADOR_BLOQUE);
            elementosTexto.forEach((el, index) => {
                if (textosTraducidos[index]) {
                    el.textContent = textosTraducidos[index].trim();
                }
            });
        }
    }

    // --- PLACEHOLDERS ---
    if (elementosPlaceholder.length > 0) {
        let arrayPlaceholders = elementosPlaceholder.map(input => input.getAttribute("placeholder") || "");
        let paquetePlaceholders = arrayPlaceholders.join(DELIMITADOR_BLOQUE);

        let respuestaServidorPl = await consumirApiTraduccion(paquetePlaceholders);
        
        if (respuestaServidorPl) {
            let placeholdersTraducidos = respuestaServidorPl.split(DELIMITADOR_BLOQUE);
            elementosPlaceholder.forEach((input, index) => {
                if (placeholdersTraducidos[index]) {
                    input.setAttribute("placeholder", placeholdersTraducidos[index].trim());
                }
            });
        }
    }
}

async function consumirApiTraduccion(bloqueTexto) {
    try {
        const indiceAleatorio = Math.floor(Math.random() * POOL_DESARROLLADORES.length);
        const identificadorActivo = POOL_DESARROLLADORES[indiceAleatorio];
        const urlPeticion = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(bloqueTexto)}&langpair=es|en&de=${identificadorActivo}`;

        console.log(`[Ecolima Sync] Traduciendo bloque mediante: ${identificadorActivo}`);

        const response = await fetch(urlPeticion);
        if (!response.ok) throw new Error(`HTTP Status ${response.status}`);

        const jsonResponse = await response.json();
        return jsonResponse.responseData.translatedText;
    } catch (error) {
        console.error("Error en comunicación externa de idiomas:", error);
        return null;
    }
}