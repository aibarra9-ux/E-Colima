document.addEventListener("DOMContentLoaded", () => {

    const toggles = document.querySelectorAll(".togglePassword");
    const jaguar = document.querySelector(".jaguar");

    const form = document.getElementById("registroForm");
    const password = document.getElementById("password");
    const confirmar = document.getElementById("confirmar");
    const correo = document.getElementById("correoRegistro");

    const errorJS = document.getElementById("errorJS");
    const errorPHP = document.getElementById("errorPHP");
    const strengthMessage = document.getElementById("strengthMessage");

    // NUEVOS ELEMENTOS PARA VERIFICACIÓN
    const btnAccion = document.getElementById("btnAccion");
    const seccionCodigo = document.getElementById("seccionCodigo");
    const btnSubmitReal = document.getElementById("btnSubmitReal");
    const inputCodigo = document.getElementById("codigoVerificacion");

    let jaguarBloqueado = false;
    let etapaCodigo = false; // Controla si ya se envió el correo

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
                jaguar.src = "../../assets/Login/Imagen Onza.png";
                password.dispatchEvent(new Event("input"));
            }
        });
    });

    // 📩 LÓGICA DEL BOTÓN DE ACCIÓN (ENVIAR CÓDIGO / REGISTRAR)
    btnAccion.addEventListener("click", function () {
        if (!etapaCodigo) {
            // VALIDACIONES PREVIAS AL ENVÍO
            if (correo.value === "" || !correo.value.includes("@")) {
                alert("Por favor, ingresa un correo válido.");
                return;
            }
            if (password.value === "" || password.value !== confirmar.value) {
                errorJS.classList.add("mostrar-error");
                if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
                return;
            }

            // Llamada a verificar.php usando Fetch
            const formData = new FormData();
            formData.append('correo', correo.value);

            btnAccion.textContent = "Enviando...";
            btnAccion.disabled = true;

            fetch('verificar.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnAccion.disabled = false;
                if (data.status === "success") {
                    // MOSTRAR EL CÓDIGO EN CONSOLA PARA PRUEBAS EN LOCALHOST
                    console.log("CÓDIGO ECOLIMA:", data.debug_code);
                    
                    seccionCodigo.style.display = "block";
                    btnAccion.textContent = "Verificar y Crear Cuenta";
                    etapaCodigo = true;
                    
                    if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza feliz.png";
                } else {
                    alert("Error: " + data.message);
                    btnAccion.textContent = "Enviar código de verificación";
                }
            })
            .catch(error => {
                btnAccion.disabled = false;
                btnAccion.textContent = "Enviar código de verificación";
                console.error("Error:", error);
            });

        } else {
            // ETAPA 2: VALIDAR QUE HAYA ESCRITO ALGO EN EL CÓDIGO
            if (inputCodigo.value.length < 6) {
                alert("Ingresa el código de 6 dígitos enviado a tu correo.");
                return;
            }
            // Disparar el submit real
            btnSubmitReal.click();
        }
    });

    // 🔐 VALIDACIÓN SUBMIT REAL
   form.addEventListener("submit", function(event) {
    // Si todavía no estamos en la etapa del código, bloqueamos el envío pase lo que pase
    if (!etapaCodigo) {
        event.preventDefault();
        // Disparamos la lógica del botón naranja manualmente por si el usuario es terco
        btnAccion.click(); 
        return false;
    }

    // Si ya estamos en la etapa del código pero el campo está vacío
    if (inputCodigo.value.length < 6) {
        event.preventDefault();
        alert("¡Espera! Primero debes ingresar el código de 6 dígitos.");
        return false;
    }

    // Validación final de contraseñas
    if (password.value !== confirmar.value) {
        event.preventDefault();
        errorJS.classList.add("mostrar-error");
        if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
    }
});

    // ✍️ OCULTAR ERRORES AL ESCRIBIR
    document.querySelectorAll("#registroForm input").forEach(input => {
        input.addEventListener("input", () => {
            errorJS.classList.remove("mostrar-error");
            if(errorPHP) errorPHP.classList.remove("mostrar-error");
        });
    });

    // 💪 FUERZA CONTRASEÑA (Mantengo tu lógica original intacta)
    password.addEventListener("input", function () {
        const value = this.value;
        const hasLetters = /[A-Za-z]/.test(value);
        const hasNumbers = /[0-9]/.test(value);
        const hasSymbols = /[\W]/.test(value);

        if (value.length === 0) {
            strengthMessage.textContent = "";
            if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Imagen Onza.png";
            return;
        }

        if (value.length < 8) {
            strengthMessage.textContent = "🔴 Muy corta";
            strengthMessage.style.color = "red";
            if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza decepcionada.png";
            return;
        }

        if (!hasNumbers || !hasLetters) {
            strengthMessage.textContent = "🔴 Débil";
            strengthMessage.style.color = "red";
            if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
            return;
        }

        if (hasLetters && hasNumbers && !hasSymbols) {
            strengthMessage.textContent = "🟡 Media";
            strengthMessage.style.color = "orange";
            if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza evaluadora.png";
            return;
        }

        if (hasLetters && hasNumbers && hasSymbols) {
            strengthMessage.textContent = "🟢 Fuerte";
            strengthMessage.style.color = "green";
            if (!jaguarBloqueado) jaguar.src = "../../assets/Login/Onza feliz.png";
        }
    });
});