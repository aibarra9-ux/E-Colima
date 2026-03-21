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

document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("registroForm");
    const password = document.getElementById("password");
    const confirmar = document.getElementById("confirmar");
    const errorJS = document.getElementById("errorJS");
    const errorPHP = document.getElementById("errorPHP"); // puede ser null si no hay error

    form.addEventListener("submit", function(event){
        if(password.value !== confirmar.value){
            event.preventDefault();
            errorJS.classList.add("mostrar-error");
        } else {
            errorJS.classList.remove("mostrar-error");
        }
    });

    // Ocultar errores al escribir
    document.querySelectorAll("#registroForm input").forEach(input => {
        input.addEventListener("input", () => {
            errorJS.classList.remove("mostrar-error");
            if(errorPHP) errorPHP.classList.remove("mostrar-error"); // ← esta línea es la nueva
        });
    });

});