<?php
/**
 * Página de perfil de usuario
 *
 * Esta página permite al usuario ver y editar su perfil.
 * Solo es accesible para usuarios autenticados.
 */

// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Proteger esta página - muestra error 403 si no está autenticado
$user = $auth->protectPage('/errors/403.php');

// Incluir el header
include_once __DIR__ . '/../include/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Mi Perfil</h1>
                <a href="/dashboard/index.php" class="btn btn-outline-primary">Volver al Dashboard</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información Personal</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (isset($user->user_metadata->avatar_url)): ?>
                        <img src="<?php echo htmlspecialchars($user->user_metadata->avatar_url); ?>" alt="Avatar" class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
                        <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 4rem;">
                            <?php echo strtoupper(substr($user->email, 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <h5><?php echo htmlspecialchars($user->email); ?></h5>
                    <p class="text-muted">
                        Usuario desde: <?php echo date('d/m/Y', strtotime($user->created_at)); ?>
                    </p>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            Cambiar Contraseña
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Editar Perfil</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="alert alert-success d-none" id="successMessage"></div>
                        <div class="alert alert-danger d-none" id="errorMessage"></div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user->email); ?>" readonly>
                            <div class="form-text">El correo electrónico no se puede cambiar.</div>
                        </div>

                        <div class="mb-3">
                            <label for="display_name" class="form-label">Nombre de Usuario (Display Name)</label>
                            <input type="text" class="form-control" id="display_name" value="<?php echo htmlspecialchars($user->user_metadata->display_name ?? ''); ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Zona de Peligro</h5>
                </div>
                <div class="card-body">
                    <p>Las siguientes acciones son irreversibles. Por favor, ten cuidado.</p>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            Eliminar Mi Cuenta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="alert alert-success d-none" id="passwordSuccessMessage"></div>
                    <div class="alert alert-danger d-none" id="passwordErrorMessage"></div>

                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control" id="currentPassword" required>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="newPassword" required>
                        <div class="form-text">La contraseña debe tener al menos 8 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="changePasswordButton">Cambiar Contraseña</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar cuenta -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Eliminar Mi Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>¡Advertencia!</strong> Esta acción es irreversible. Todos tus datos serán eliminados permanentemente.
                </div>

                <p>Para confirmar, por favor escribe "ELIMINAR" en el campo a continuación:</p>

                <div class="mb-3">
                    <input type="text" class="form-control" id="deleteConfirmation" placeholder="ELIMINAR">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="deleteAccountButton" disabled>Eliminar Mi Cuenta</button>
            </div>
        </div>
    </div>
</div>

<script>
// Script para el formulario de perfil
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const display_name = document.getElementById('display_name').value;

    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    // Ocultar mensajes previos
    successMessage.classList.add('d-none');
    errorMessage.classList.add('d-none');

    try {
        // Aquí iría la lógica para actualizar el perfil con Supabase
        // Por ahora, solo mostraremos un mensaje de éxito

        successMessage.textContent = 'Perfil actualizado correctamente';
        successMessage.classList.remove('d-none');
    } catch (error) {
        errorMessage.textContent = 'Error al actualizar el perfil: ' + error.message;
        errorMessage.classList.remove('d-none');
    }
});

// Script para el cambio de contraseña
document.getElementById('changePasswordButton').addEventListener('click', async function() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    const successMessage = document.getElementById('passwordSuccessMessage');
    const errorMessage = document.getElementById('passwordErrorMessage');

    // Ocultar mensajes previos
    successMessage.classList.add('d-none');
    errorMessage.classList.add('d-none');

    // Validar que las contraseñas coincidan
    if (newPassword !== confirmPassword) {
        errorMessage.textContent = 'Las contraseñas no coinciden';
        errorMessage.classList.remove('d-none');
        return;
    }

    // Validar longitud de la contraseña
    if (newPassword.length < 8) {
        errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres';
        errorMessage.classList.remove('d-none');
        return;
    }

    try {
        // Aquí iría la lógica para cambiar la contraseña con Supabase
        // Por ahora, solo mostraremos un mensaje de éxito

        successMessage.textContent = 'Contraseña cambiada correctamente';
        successMessage.classList.remove('d-none');

        // Limpiar el formulario
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    } catch (error) {
        errorMessage.textContent = 'Error al cambiar la contraseña: ' + error.message;
        errorMessage.classList.remove('d-none');
    }
});

// Script para habilitar/deshabilitar el botón de eliminar cuenta
document.getElementById('deleteConfirmation').addEventListener('input', function() {
    const deleteButton = document.getElementById('deleteAccountButton');
    deleteButton.disabled = this.value !== 'ELIMINAR';
});

// Script para eliminar la cuenta
document.getElementById('deleteAccountButton').addEventListener('click', async function() {
    const confirmation = document.getElementById('deleteConfirmation').value;

    if (confirmation !== 'ELIMINAR') {
        return;
    }

    try {
        // Aquí iría la lógica para eliminar la cuenta con Supabase
        // Por ahora, solo redirigiremos a la página de inicio

        window.location.href = '/auth/logout.php';
    } catch (error) {
        alert('Error al eliminar la cuenta: ' + error.message);
    }
});
</script>

<?php
// Incluir el footer
include_once __DIR__ . '/../include/footer.php';
?>
