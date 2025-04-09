<?php
/**
 * Página de dashboard protegida
 *
 * Esta página solo es accesible para usuarios autenticados.
 * Utiliza el middleware para proteger el acceso.
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
                <h1>Dashboard</h1>
                <span class="badge bg-success">Autenticado</span>
            </div>

            <div class="alert alert-info">
                <strong>Bienvenido, <?php echo htmlspecialchars($user->email); ?>!</strong>
                Has accedido a una página protegida que solo es visible para usuarios autenticados.
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>ID:</strong> <?php echo htmlspecialchars($user->id); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Nickname:</strong>
                            <?php
                            if (isset($user->user_metadata->display_name)) {
                                echo '@' . htmlspecialchars($user->user_metadata->display_name);
                            } else {
                                echo '<span class="text-muted">No establecido</span>';
                            }
                            ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Último inicio de sesión:</strong>
                            <?php echo isset($user->last_sign_in_at) ? date('d/m/Y H:i:s', strtotime($user->last_sign_in_at)) : 'N/A'; ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Creado:</strong>
                            <?php echo isset($user->created_at) ? date('d/m/Y H:i:s', strtotime($user->created_at)) : 'N/A'; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary">Editar Perfil</a>
                        <a href="#" class="btn btn-info">Ver Actividad</a>
                        <a href="/auth/logout.php" class="btn btn-danger">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cómo Funciona el Middleware</h5>
                </div>
                <div class="card-body">
                    <p>Esta página está protegida por el middleware de autenticación. El código que protege esta página es:</p>

                    <pre class="bg-light p-3 rounded"><code>// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Proteger esta página - redirige a login si no está autenticado
$user = $auth->protectPage('/auth/login.php');</code></pre>

                    <p class="mt-3">El middleware verifica si hay un token válido de Supabase. Si no hay token o el token es inválido, el usuario es redirigido a la página de inicio de sesión.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once __DIR__ . '/../include/footer.php';
?>