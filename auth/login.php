<?php
/**
 * Página de inicio de sesión
 */

// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Si el usuario ya está autenticado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

// Incluir el encabezado
include_once __DIR__ . '/../include/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Iniciar sesión
                </div>
                <div class="card-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="alert alert-danger d-none" id="errorMessage" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span class="error-text"></span>
                        </div>
                        <div class="alert alert-success d-none" id="successMessage" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span class="success-text"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    </form>
                    <div class="mt-3">
                        <p>¿No tienes una cuenta? <a href="/auth/register.php">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para obtener parámetros de la URL
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Mostrar mensajes de error o éxito basados en los parámetros de la URL
document.addEventListener('DOMContentLoaded', function() {
    const errorParam = getUrlParameter('error');
    const successParam = getUrlParameter('success');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');

    if (errorParam) {
        errorMessage.querySelector('.error-text').textContent = decodeURIComponent(errorParam);
        errorMessage.classList.remove('d-none');
    }

    if (successParam) {
        successMessage.querySelector('.success-text').textContent = decodeURIComponent(successParam);
        successMessage.classList.remove('d-none');
    }
});

// Script para manejar el inicio de sesión con Supabase
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    const submitButton = this.querySelector('button[type="submit"]');

    // Ocultar mensaje de error previo
    errorMessage.classList.add('d-none');

    // Deshabilitar botón y mostrar indicador de carga
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Iniciando sesión...';

    try {
        // Realizar solicitud al endpoint de login
        const response = await fetch('/auth/login_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.error) {
            // Mostrar error
            errorMessage.querySelector('.error-text').textContent = data.error;
            errorMessage.classList.remove('d-none');

            // Restaurar botón
            submitButton.disabled = false;
            submitButton.textContent = 'Iniciar sesión';
        } else {
            // Redirigir al dashboard
            window.location.href = '/dashboard/index.php';
        }
    } catch (error) {
        // Mostrar error genérico
        errorMessage.querySelector('.error-text').textContent = 'Error al conectar con el servidor. Por favor, verifica tu conexión a internet e inténtalo de nuevo.';
        errorMessage.classList.remove('d-none');

        // Restaurar botón
        submitButton.disabled = false;
        submitButton.textContent = 'Iniciar sesión';

        // Registrar error en consola para depuración
        console.error('Error de inicio de sesión:', error);
    }
});
</script>

<?php
// Incluir el pie de página si lo tienes
// include_once __DIR__ . '/../include/footer.php';
?>
