"use strict";

function showMessage(elementId, message, isSuccess) {
    const element = document.getElementById(elementId);
    if (!element) {
        return;
    }
    element.innerHTML = `<div class="alert ${isSuccess ? "alert-success" : "alert-danger"}">${message}</div>`;
}

async function submitFormWithFetch(form, url, messageElementId, reloadOnSuccess = false) {
    if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
    }

    try {
        const formData = new FormData(form);
        const response = await fetch(url, {
            method: "POST",
            body: formData
        });
        const data = await response.json();
        showMessage(messageElementId, data.message, data.success);
        if (data.success && reloadOnSuccess) {
            setTimeout(() => window.location.reload(), 800);
        }
        if (data.success && url.includes("login")) {
            setTimeout(() => window.location.href = "index.php", 800);
        }
        if (data.success && url.includes("register")) {
            form.reset();
            form.classList.remove("was-validated");
        }
    } catch (error) {
        showMessage(messageElementId, "Došlo je do greške. Pokušaj ponovo.", false);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".needs-validation").forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add("was-validated");
            }
        });
    });

    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", (event) => {
            event.preventDefault();
            submitFormWithFetch(registerForm, "ajax/register.php", "registerMessage");
        });
    }

    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", (event) => {
            event.preventDefault();
            submitFormWithFetch(loginForm, "ajax/login.php", "loginMessage");
        });
    }

    const ratingForm = document.getElementById("ratingForm");
    if (ratingForm) {
        ratingForm.addEventListener("submit", (event) => {
            event.preventDefault();
            submitFormWithFetch(ratingForm, "ajax/rate.php", "ratingMessage", true);
        });
    }
});
