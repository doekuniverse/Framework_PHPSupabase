<?php
/**
 * Página de error 404 - Página no encontrada
 *
 * Esta página se muestra cuando un usuario intenta acceder a un recurso
 * que no existe en el servidor.
 */

// Establecer el código de estado HTTP correcto
http_response_code(404);

// Incluir el header (que ya carga el middleware)
include_once __DIR__ . '/../include/header.php';

// Ahora podemos usar $isAuthenticated y $currentUser que se definen en el header
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1 text-warning">404</h1>
                <h2 class="mb-4">Página no encontrada</h2>
                <div class="error-details mb-4">
                    <p class="lead">Lo sentimos, la página que estás buscando no existe.</p>
                    <p>Es posible que la página haya sido movida, eliminada o que nunca haya existido.</p>
                </div>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-house-fill me-1"></i>Ir al Inicio
                    </a>
                    <?php if ($isAuthenticated): ?>
                    <a href="/dashboard/index.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-speedometer2 me-1"></i>Ir al Dashboard
                    </a>
                    <?php else: ?>
                    <a href="/auth/login.php" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
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
                    <h5 class="card-title mb-0">¿Qué puedo hacer ahora?</h5>
                </div>
                <div class="card-body">
                    <p>Aquí hay algunas sugerencias de lo que puedes hacer:</p>
                    <ul>
                        <li>Verifica que la URL que has introducido sea correcta.</li>
                        <li>Utiliza la navegación del sitio para encontrar lo que buscas.</li>
                        <li>Regresa a la página anterior e intenta otro enlace.</li>
                        <li>Visita nuestra página de inicio para comenzar de nuevo.</li>
                    </ul>
                    <p>Si llegaste aquí a través de un enlace en nuestro sitio, por favor contacta al administrador para informar del problema.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once __DIR__ . '/../include/footer.php';
?>
