// Funciones de autenticación
document.addEventListener("DOMContentLoaded", function () {
    // Verificar autenticación JWT
    checkAuthentication();

    // Configurar evento de logout
    const logoutBtn = document.getElementById("logout-btn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function (e) {
            e.preventDefault();
            logout();
        });
    }
});

// Verificar si el usuario está autenticado
function checkAuthentication() {
    const token = localStorage.getItem("auth_token");
    const authUser = localStorage.getItem("auth_user");

    if (token && authUser) {
        try {
            const userData = JSON.parse(authUser);
            document
                .querySelectorAll(".auth-guest")
                .forEach((el) => el.classList.add("d-none"));
            document
                .querySelectorAll(".auth-user")
                .forEach((el) => el.classList.remove("d-none"));

            // Establecer nombre de usuario
            const usernameDisplay = document.getElementById("username-display");
            if (usernameDisplay) {
                usernameDisplay.textContent =
                    userData.username || userData.name || "Usuario";
            }

            // Generar identicon para usuario
            const userIdenticon = document.getElementById("user-identicon");
            if (userIdenticon && window.generateIdenticon) {
                const hash = userData.email || userData.username || "default";
                userIdenticon.src = generateIdenticon(md5(hash), 32);
            }

            return true;
        } catch (e) {
            console.error("Error parsing auth user data", e);
            return false;
        }
    } else {
        document
            .querySelectorAll(".auth-guest")
            .forEach((el) => el.classList.remove("d-none"));
        document
            .querySelectorAll(".auth-user")
            .forEach((el) => el.classList.add("d-none"));
        return false;
    }
}

// Función simple MD5 (para demo)
function md5(string) {
    return string
        .split("")
        .reduce((a, b) => {
            a = (a << 5) - a + b.charCodeAt(0);
            return a & a;
        }, 0)
        .toString(16);
}

// Logout
function logout() {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("auth_user");
    checkAuthentication();
    window.location.href = "/";
}
