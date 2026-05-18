document.addEventListener("DOMContentLoaded", () => {

    const toggles = document.querySelectorAll(".togglePassword");
    const jaguar = document.querySelector(".jaguar");

    // ELEMENTOS DE REGISTRO (Pueden ser null en login.php)
    const formRegistro = document.getElementById("registroForm");
    const confirmar = document.getElementById("confirmar");
    const correo = document.getElementById("correoRegistro");
    const errorJS = document.getElementById("errorJS");
    const errorPHP = document.getElementById("errorPHP");
    const strengthMessage = document.getElementById("strengthMessage");
    const btnAccion = document.getElementById("btnAccion");
    const seccionCodigo = document.getElementById("seccionCodigo");
    const btnSubmitReal = document.getElementById("btnSubmitReal");
    const inputCodigo = document.getElementById("codigoVerificacion");

    // ELEMENTO COMÚN / PERFIL
    const password = document.getElementById("password");

    let jaguarBloqueado = false;
    let etapaCodigo = false; 

    // Cambiar aspecto del jaguar si el servidor PHP reporta errores por GET
    if (document.body.dataset.error === "true" && jaguar) {
        jaguar.src = "../../assets/Login/Onza dientes.png";
    }

    // 👁️ CONTROLADOR DEL OJO (FUNCIONA EN AMBOS FORMULARIOS)
    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const input = this.closest(".input-group").querySelector("input");
            const isHidden = input.type === "password";

            if (isHidden) {
                input.type = "text";
                this.src = "../../assets/Login/eye-fill.png";
                if (jaguar) {
                    jaguar.src = "../../assets/Login/Onza ojos cerrados.png";
                    jaguarBloqueado = true;
                }
            } else {
                input.type = "password";
                this.src = "../../assets/Login/eye-off-fill.png";
                if (jaguar) {
                    jaguarBloqueado = false;
                    jaguar.src = "../../assets/Login/Imagen Onza.png";
                }
                if (password) password.dispatchEvent(new Event("input"));
            }
        });
    });

    // 📩 LÓGICA DE VERIFICACIÓN DE REGISTRO (Solo si existe en la página)
    if (btnAccion && correo && password && confirmar) {
        btnAccion.addEventListener("click", function () {
            if (!etapaCodigo) {
                if (correo.value === "" || !correo.value.includes("@")) {
                    alert("Por favor, ingresa un correo válido.");
                    return;
                }
                if (password.value === "" || password.value !== confirmar.value) {
                    if (errorJS) errorJS.classList.add("mostrar-error");
                    if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
                    return;
                }

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
                        console.log("CÓDIGO ECOLIMA:", data.debug_code);
                        if (seccionCodigo) seccionCodigo.style.display = "block";
                        btnAccion.textContent = "Verificar y Crear Cuenta";
                        etapaCodigo = true;
                        if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza feliz.png";
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
                if (inputCodigo && inputCodigo.value.length < 6) {
                    alert("Ingresa el código de 6 dígitos enviado a tu correo.");
                    return;
                }
                if (btnSubmitReal) btnSubmitReal.click();
            }
        });
    }

    // 🔐 VALIDACIÓN DEL SUBMIT DE REGISTRO (Solo si existe en la página)
    if (formRegistro && password && confirmar) {
        formRegistro.addEventListener("submit", function(event) {
            if (!etapaCodigo) {
                event.preventDefault();
                if (btnAccion) btnAccion.click(); 
                return false;
            }

            if (inputCodigo && inputCodigo.value.length < 6) {
                event.preventDefault();
                alert("¡Espera! Primero debes ingresar el código de 6 dígitos.");
                return false;
            }

            if (password.value !== confirmar.value) {
                event.preventDefault();
                if (errorJS) errorJS.classList.add("mostrar-error");
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
            }
        });
    }

    // ✍️ OCULTAR ERRORES AL ESCRIBIR (Registro)
    const inputsRegistro = document.querySelectorAll("#registroForm input");
    if (inputsRegistro.length > 0) {
        inputsRegistro.forEach(input => {
            input.addEventListener("input", () => {
                if (errorJS) errorJS.classList.remove("mostrar-error");
                if (errorPHP) errorPHP.classList.remove("mostrar-error");
            });
        });
    }

    // 💪 MEDIDOR DE FUERZA DE CONTRASEÑA (Solo si strengthMessage existe)
    if (password && strengthMessage) {
        password.addEventListener("input", function () {
            const value = this.value;
            const hasLetters = /[A-Za-z]/.test(value);
            const hasNumbers = /[0-9]/.test(value);
            const hasSymbols = /[\W]/.test(value);

            if (value.length === 0) {
                strengthMessage.textContent = "";
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Imagen Onza.png";
                return;
            }

            if (value.length < 8) {
                strengthMessage.textContent = "🔴 Muy corta";
                strengthMessage.style.color = "red";
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza decepcionada.png";
                return;
            }

            if (!hasNumbers || !hasLetters) {
                strengthMessage.textContent = "🔴 Débil";
                strengthMessage.style.color = "red";
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza dientes.png";
                return;
            }

            if (hasLetters && hasNumbers && !hasSymbols) {
                strengthMessage.textContent = "🟡 Media";
                strengthMessage.style.color = "orange";
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza evaluadora.png";
                return;
            }

            if (hasLetters && hasNumbers && hasSymbols) {
                strengthMessage.textContent = "🟢 Fuerte";
                strengthMessage.style.color = "green";
                if (jaguar && !jaguarBloqueado) jaguar.src = "../../assets/Login/Onza feliz.png";
            }
        });
    }

    // 🌟 FLUJO INTERACTIVO INTERNO: RECUPERACIÓN DE CONTRASEÑA DE 3 PASOS
    const btnForgot = document.getElementById('forgotPassword');
    if (btnForgot) {
        btnForgot.addEventListener('click', async function() {
            try {
                // --- PASO 1: Solicitar Correo Electrónico ---
                const { value: email } = await Swal.fire({
                    title: 'Recuperar Contraseña',
                    text: 'Introduce el correo electrónico asociado a tu cuenta:',
                    input: 'email',
                    inputPlaceholder: 'tu_correo@example.com',
                    showCancelButton: true,
                    confirmButtonColor: '#2d6a4f',
                    confirmButtonText: 'Enviar Código',
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => {
                        if (!value) return 'Por favor, escribe un correo electrónico válido.';
                    }
                });

                if (!email) return;

                Swal.fire({
                    title: 'Enviando código...',
                    text: 'Estamos generando tu token de seguridad.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                let response = await fetch('procesar_recuperacion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `accion=solicitar_codigo&email=${encodeURIComponent(email)}`
                });
                let data = await response.json();

                if (data.status !== 'success') {
                    Swal.fire('Error', data.message, 'error');
                    return;
                }

                // --- PASO 2: Solicitar Código de 6 Dígitos ---
                const { value: token } = await Swal.fire({
                    title: 'Introduce el código',
                    text: `Hemos enviado un pin de seguridad de 6 dígitos a: ${email}`,
                    input: 'text',
                    inputPlaceholder: '000000',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#2d6a4f',
                    confirmButtonText: 'Validar Código',
                    cancelButtonText: 'Cancelar',
                    inputAttributes: {
                        maxlength: 6,
                        style: 'text-align: center; letter-spacing: 5px; font-weight: bold; font-size: 24px;'
                    },
                    inputValidator: (value) => {
                        if (!value || value.length !== 6 || isNaN(value)) {
                            return 'Introduce los 6 números del token.';
                        }
                    }
                });

                if (!token) return;

                Swal.fire({ title: 'Validando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                response = await fetch('procesar_recuperacion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `accion=validar_codigo&email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}`
                });
                data = await response.json();

                if (data.status !== 'success') {
                    Swal.fire('Error', data.message, 'error');
                    return;
                }

                // --- PASO 3: Solicitar Nueva Contraseña ---
                const { value: nuevaPassword } = await Swal.fire({
                    title: 'Establece tu nueva contraseña',
                    text: 'La contraseña debe tener entre 8 y 32 caracteres.',
                    input: 'password',
                    inputPlaceholder: 'Escribe tu nueva contraseña',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#2d6a4f',
                    confirmButtonText: 'Actualizar Contraseña',
                    cancelButtonText: 'Cancelar',
                    inputAttributes: { minlength: 8, maxlength: 32 },
                    inputValidator: (value) => {
                        if (!value || value.length < 8) {
                            return 'La contraseña debe tener al menos 8 caracteres.';
                        }
                    }
                });

                if (!nuevaPassword) return;

                Swal.fire({ title: 'Actualizando base de datos...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                response = await fetch('procesar_recuperacion.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `accion=cambiar_password&email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}&password=${encodeURIComponent(nuevaPassword)}`
                });
                data = await response.json();

                if (data.status === 'success') {
                    Swal.fire('¡Contraseña Cambiada!', data.message, 'success');
                } else {
                    Swal.fire('Error', data.message, 'error');
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
            }
        });
    }
});
