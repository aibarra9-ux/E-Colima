document.addEventListener("DOMContentLoaded", () => {

    const toggles = document.querySelectorAll(".togglePassword");
    const jaguar = document.querySelector(".jaguar");

    const form = document.getElementById("registroForm");
    const password = document.getElementById("password");
    const confirmar = document.getElementById("confirmar");

    const errorJS = document.getElementById("errorJS");
    const errorPHP = document.getElementById("errorPHP");
    const strengthMessage = document.getElementById("strengthMessage");

    let jaguarBloqueado = false;

     if (document.body.dataset.error === "true") {
        jaguar.src = "../../assets/Login/Onza dientes.png";
    }

    // 👁️ TOGGLE OJO
    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {

            const input = this.closest(".input-group").querySelector("input");
            const isHidden = input.type === "password";

            if (isHidden) {
                input.type = "text";
                this.src = "../../assets/Login/eye-fill.png";

                jaguar.src = "../../assets/Login/Onza ojos cerrados.png";
                jaguarBloqueado = true;

            } else {
                input.type = "password";
                this.src = "../../assets/Login/eye-off-fill.png";

                jaguarBloqueado = false;

                //  FORZAR imagen normal
                jaguar.src = "../../assets/Login/Imagen Onza.png";
                password.dispatchEvent(new Event("input"));

            }
        });
    });

    // 🔐 VALIDACIÓN SUBMIT (UNIFICADA)
    form.addEventListener("submit", function(event){

        if(password.value !== confirmar.value){
            event.preventDefault();

            errorJS.classList.add("mostrar-error");

            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Onza dientes.png";
            }

        } else {
            errorJS.classList.remove("mostrar-error");
        }
    });

    // ✍️ OCULTAR ERRORES AL ESCRIBIR
    document.querySelectorAll("#registroForm input").forEach(input => {
        input.addEventListener("input", () => {
            errorJS.classList.remove("mostrar-error");
            if(errorPHP) errorPHP.classList.remove("mostrar-error");
        });
    });

    // 💪 FUERZA CONTRASEÑA
    password.addEventListener("input", function () {

        const value = this.value;

        const hasLetters = /[A-Za-z]/.test(value);
        const hasNumbers = /[0-9]/.test(value);
        const hasSymbols = /[\W]/.test(value);

        if (value.length === 0) {
            strengthMessage.textContent = "";
            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Imagen Onza.png";
            }
            return;
        }

        if (value.length < 8) {
            strengthMessage.textContent = "🔴 Muy corta";
            strengthMessage.style.color = "red";

            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Onza decepcionada.png";
            }
            return;
        }

        if (!hasNumbers || !hasLetters) {
            strengthMessage.textContent = "🔴 Débil";
            strengthMessage.style.color = "red";

            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Onza dientes.png";
            }
            return;
        }

        if (hasLetters && hasNumbers && !hasSymbols) {
            strengthMessage.textContent = "🟡 Media";
            strengthMessage.style.color = "orange";

            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Onza evaluadora.png";
            }
            return;
        }

        if (hasLetters && hasNumbers && hasSymbols) {
            strengthMessage.textContent = "🟢 Fuerte";
            strengthMessage.style.color = "green";

            if (!jaguarBloqueado) {
                jaguar.src = "../../assets/Login/Onza feliz.png";
            }
        }
    });

});
