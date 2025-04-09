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
                        <div class="alert alert-danger d-none" id="errorMessage"></div>
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
// Script para manejar el inicio de sesión con Supabase
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
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
            errorMessage.textContent = data.error;
            errorMessage.classList.remove('d-none');
        } else {
            // Redirigir al dashboard
            window.location.href = '/dashboard/index.php';
        }
    } catch (error) {
        // Mostrar error
        errorMessage.textContent = 'Error al iniciar sesión. Inténtalo de nuevo.';
        errorMessage.classList.remove('d-none');
    }
});
</script>

<?php
// Incluir el pie de página si lo tienes
// include_once __DIR__ . '/../include/footer.php';
?>
