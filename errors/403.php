<?php
/**
 * Página de error 403 - Acceso Prohibido
 *
 * Esta página se muestra cuando un usuario intenta acceder a un recurso
 * para el cual no tiene permisos.
 */

// Establecer el código de estado HTTP correcto
http_response_code(403);

// Incluir el header (que ya carga el middleware)
include_once __DIR__ . '/../include/header.php';

// Ahora podemos usar $isAuthenticated y $currentUser que se definen en el header
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-danger">403</h1>
                <h2 class="mb-4">Acceso Prohibido</h2>
                <div class="error-details mb-4">
                    <p class="lead">Lo sentimos, no tienes permiso para acceder a esta página.</p>
                    <?php if (!$isAuthenticated): ?>
                    <p>Parece que no has iniciado sesión. Por favor, inicia sesión para acceder a este contenido.</p>
                    <?php else: ?>
                    <p>Tu cuenta no tiene los permisos necesarios para ver este contenido.</p>
                    <?php endif; ?>
                </div>
                <div class="error-actions">
                    <a href="/public/index.php" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-house-fill me-1"></i>Ir al Inicio
                    </a>
                    <?php if (!$isAuthenticated): ?>
                    <a href="/auth/login.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
                    </a>
                    <?php else: ?>
                    <a href="/dashboard/index.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-speedometer2 me-1"></i>Ir al Dashboard
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">¿Por qué estoy viendo esta página?</h5>
                </div>
                <div class="card-body">
                    <p>Puedes estar viendo esta página por alguna de las siguientes razones:</p>
                    <ul>
                        <li>Intentaste acceder a una página que requiere autenticación sin haber iniciado sesión.</li>
                        <li>Tu sesión ha expirado y necesitas volver a iniciar sesión.</li>
                        <li>Estás intentando acceder a un recurso que requiere permisos especiales que tu cuenta no tiene.</li>
                        <li>Seguiste un enlace incorrecto o mal formado.</li>
                    </ul>
                    <p>Si crees que esto es un error, por favor contacta al administrador del sitio.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once __DIR__ . '/../include/footer.php';
?>
