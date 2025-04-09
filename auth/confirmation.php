<?php
/**
 * Página de confirmación de registro
 * 
 * Esta página se muestra después de que un usuario se registra
 * y necesita confirmar su correo electrónico.
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">¡Registro exitoso!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-check" style="font-size: 4rem; color: #28a745;"></i>
                    </div>
                    
                    <h5 class="card-title text-center">Confirma tu dirección de correo electrónico</h5>
                    
                    <div class="alert alert-info">
                        <p>Hemos enviado un correo electrónico de confirmación a la dirección que proporcionaste.</p>
                        <p>Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace de confirmación para activar tu cuenta.</p>
                    </div>
                    
                    <p class="text-center">Una vez que hayas confirmado tu correo electrónico, podrás iniciar sesión en tu cuenta.</p>
                    
                    <div class="text-center mt-4">
                        <a href="/auth/login.php" class="btn btn-primary">Ir a la página de inicio de sesión</a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">¿No recibiste el correo de confirmación?</h5>
                </div>
                <div class="card-body">
                    <p>Si no has recibido el correo electrónico de confirmación después de unos minutos:</p>
                    <ul>
                        <li>Revisa tu carpeta de spam o correo no deseado</li>
                        <li>Verifica que hayas introducido correctamente tu dirección de correo electrónico</li>
                        <li>Si aún tienes problemas, puedes solicitar un nuevo correo de confirmación</li>
                    </ul>
                    
                    <form id="resendForm" class="mt-3">
                        <div class="mb-3">
                            <label for="email" class="form-label">Tu dirección de correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="alert alert-danger d-none" id="errorMessage"></div>
                        <div class="alert alert-success d-none" id="successMessage"></div>
                        <button type="submit" class="btn btn-outline-primary">Reenviar correo de confirmación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script para manejar el reenvío del correo de confirmación
document.getElementById('resendForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
    
    // Ocultar mensajes previos
    errorMessage.classList.add('d-none');
    successMessage.classList.add('d-none');
    
    try {
        // Realizar solicitud al endpoint de reenvío
        const response = await fetch('/auth/resend_confirmation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        });
        
        const data = await response.json();
        
        if (data.error) {
            // Mostrar error
            errorMessage.textContent = data.error;
            errorMessage.classList.remove('d-none');
        } else {
            // Mostrar éxito
            successMessage.textContent = 'Se ha enviado un nuevo correo de confirmación. Por favor, revisa tu bandeja de entrada.';
            successMessage.classList.remove('d-none');
            
            // Limpiar el formulario
            document.getElementById('email').value = '';
        }
    } catch (error) {
        // Mostrar error
        errorMessage.textContent = 'Error al enviar la solicitud. Inténtalo de nuevo.';
        errorMessage.classList.remove('d-none');
    }
});
</script>

<?php
// Incluir el pie de página
include_once __DIR__ . '/../include/footer.php';
?>
