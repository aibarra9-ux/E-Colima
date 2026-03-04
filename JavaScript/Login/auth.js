document.addEventListener("DOMContentLoaded", function() {

    const showRegister = document.getElementById("showRegister");
    const showLogin = document.getElementById("showLogin");

    const loginForm = document.querySelector(".login-form");
    const registerForm = document.querySelector(".register-form");

    if (showRegister) {
        showRegister.addEventListener("click", () => {
            loginForm.classList.remove("active");
            registerForm.classList.add("active");
        });
    }

    if (showLogin) {
        showLogin.addEventListener("click", () => {
            registerForm.classList.remove("active");
            loginForm.classList.add("active");
        });
    }

});