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



const form = document.getElementById("registroForm");
const password = document.getElementById("password");
const confirmar = document.getElementById("confirmar");
const error = document.getElementById("errorPassword");

form.addEventListener("submit", function(event){

    if(password.value !== confirmar.value){

        event.preventDefault();
        error.textContent = "Las contraseñas no coinciden";
        error.style.display = "flex";

    } else {
        error.style.display = "none";
    }

});