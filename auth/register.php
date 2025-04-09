<?php
/**
 * Página de registro
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
                    Crear una cuenta
                </div>
                <div class="card-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="nickname" class="form-label">Nickname</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" id="nickname" name="nickname" required pattern="[a-zA-Z0-9_]+" maxlength="30" onkeypress="return event.key !== ' '" onpaste="return handlePaste(event, 'nickname');">
                            </div>
                            <div class="form-text">Solo letras, números y guiones bajos. Sin espacios ni caracteres especiales.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required onkeypress="return event.key !== ' '" onpaste="return handlePaste(event, 'email');">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div class="alert alert-danger d-none" id="errorMessage"></div>
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </form>
                    <div class="mt-3">
                        <p>¿Ya tienes una cuenta? <a href="/auth/login.php">Iniciar sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para manejar el pegado de texto y eliminar espacios
function handlePaste(event, fieldType) {
    // Prevenir el comportamiento predeterminado de pegado
    event.preventDefault();

    // Obtener el texto del portapapeles
    const clipboardData = event.clipboardData || window.clipboardData;
    let pastedText = clipboardData.getData('text');

    // Eliminar espacios
    pastedText = pastedText.replace(/\s/g, '');

    // Validaciones específicas según el tipo de campo
    if (fieldType === 'email' && !pastedText.includes('@')) {
        // Validar formato básico de email
        return false;
    } else if (fieldType === 'nickname') {
        // Validar que solo contenga caracteres permitidos para nickname
        if (!/^[a-zA-Z0-9_]+$/.test(pastedText)) {
            return false;
        }
    }

    // Insertar el texto limpio en el campo
    document.execCommand('insertText', false, pastedText);
    return true;
}

// Script para manejar el registro con Supabase
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const nickname = document.getElementById('nickname').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorMessage = document.getElementById('errorMessage');

    // Validar el formato del nickname
    const nicknameRegex = /^[a-zA-Z0-9_]+$/;
    if (!nicknameRegex.test(nickname)) {
        errorMessage.textContent = 'El nickname solo puede contener letras, números y guiones bajos';
        errorMessage.classList.remove('d-none');
        return;
    }

    // Validar que las contraseñas coincidan
    if (password !== confirmPassword) {
        errorMessage.textContent = 'Las contraseñas no coinciden';
        errorMessage.classList.remove('d-none');
        return;
    }

    // Validar longitud de contraseña
    if (password.length < 6) {
        errorMessage.textContent = 'La contraseña debe tener al menos 6 caracteres';
        errorMessage.classList.remove('d-none');
        return;
    }

    try {
        // Realizar solicitud al endpoint de registro
        const response = await fetch('/auth/register_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password, nickname })
        });

        const data = await response.json();

        if (data.error) {
            // Mostrar error
            errorMessage.textContent = data.error;
            errorMessage.classList.remove('d-none');
        } else {
            // Redirigir al dashboard o a la página de confirmación
            window.location.href = data.redirectTo || '/dashboard/index.php';
        }
    } catch (error) {
        // Mostrar error
        errorMessage.textContent = 'Error al registrarse. Inténtalo de nuevo.';
        errorMessage.classList.remove('d-none');
    }
});
</script>

<?php
// Incluir el pie de página
include_once __DIR__ . '/../include/footer.php';
?>
