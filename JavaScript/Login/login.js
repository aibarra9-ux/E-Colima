/* === LOGICA DE MOSTRAR OOCULTAR CONTRASEÑA (Tu código original) === */
const toggles = document.querySelectorAll(".togglePassword");

toggles.forEach(toggle => {
    toggle.addEventListener("click", function () {
        const input = this.parentElement.previousElementSibling;

        if (input.type === "password") {
            input.type = "text";
            this.src = "../../assets/Login/eye-fill.png";
        } else {
            input.type = "password";
            this.src = "../../assets/Login/eye-off-fill.png";
        }
    });
});

/* === VALIDACIÓN DE REGISTRO COMPATIBLE CON IDIOMAS === */
const form = document.getElementById("registroForm");
const password = document.getElementById("password");
const confirmar = document.getElementById("confirmar");
const error = document.getElementById("errorPassword");

if (form) {
    form.addEventListener("submit", async function(event){
        if(password.value !== confirmar.value){
            event.preventDefault();
            
            // Detectamos cuál es el idioma activo guardado en el navegador
            let currentLang = localStorage.getItem('globalLang') || 'es';
            
            try {
                // Buscamos la traducción exacta del error en el JSON
                const response = await fetch(`../../lenguaje/${currentLang}.json`);
                const texts = await response.json();
                
                error.textContent = texts.registro.err_match || "Las contraseñas no coinciden";
            } catch (err) {
                error.textContent = "Las contraseñas no coinciden";
            }
            
            error.style.display = "flex";
        } else {
            error.style.display = "none";
        }
    });
}