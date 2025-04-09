<?php
/**
 * Página principal pública
 *
 * Esta es la página de inicio accesible para todos los usuarios,
 * estén autenticados o no.
 */

// Incluir el header (que ya carga el middleware)
include_once __DIR__ . '/../include/header.php';

// Ahora podemos usar $isAuthenticated y $currentUser que se definen en el header
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="display-4">Bienvenido a Mi Aplicación</h1>

            <?php if ($isAuthenticated): ?>
            <p class="lead">Hola,
                <?php
                if (isset($currentUser->user_metadata->email)) {
                    echo ''. htmlspecialchars($currentUser->user_metadata->email);
                } else {
                    echo htmlspecialchars($currentUser->email);
                }
                ?><br>Gracias por iniciar sesión.</p>
            <div class="mt-4">
                <a href="/dashboard/index.php" class="btn btn-primary btn-lg">Ir al Dashboard</a>
            </div>
            <?php else: ?>
            <p class="lead">Esta es una aplicación de ejemplo que utiliza PHP y Supabase para autenticación.</p>
            <div class="mt-4">
                <a href="/auth/login.php" class="btn btn-primary btn-lg me-2">Iniciar Sesión</a>
                <a href="/auth/register.php" class="btn btn-outline-primary btn-lg">Registrarse</a>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Características</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-primary rounded-pill me-2">1</span>
                            Autenticación segura con Supabase
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-primary rounded-pill me-2">2</span>
                            Middleware PHP personalizado
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-primary rounded-pill me-2">3</span>
                            Interfaz adaptativa según autenticación
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge bg-primary rounded-pill me-2">4</span>
                            Estructura de proyecto organizada
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Autenticación Segura</h5>
                    <p class="card-text">Utilizamos Supabase para proporcionar un sistema de autenticación seguro y confiable.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Fácil de Usar</h5>
                    <p class="card-text">Interfaz intuitiva y experiencia de usuario fluida en todos los dispositivos.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Personalizable</h5>
                    <p class="card-text">Estructura modular que facilita la personalización y expansión del proyecto.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
include_once __DIR__ . '/../include/footer.php';
?>

<script>
// Detectar si hay un token de acceso en la URL
if (window.location.hash && window.location.hash.includes('access_token')) {
    // Extraer el token de acceso
    const hash = window.location.hash.substring(1);
    const params = new URLSearchParams(hash);
    const accessToken = params.get('access_token');
    const tokenType = params.get('token_type') || 'bearer';
    const actionType = params.get('type');

    if (accessToken) {
        console.log('Token de acceso detectado en la URL');

        // Mostrar un mensaje de carga
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
        loadingDiv.style.zIndex = '9999';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h5>Procesando autenticación...</h5>
            </div>
        `;
        document.body.appendChild(loadingDiv);

        // Guardar el token en la sesión
        fetch('/auth/save_token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                access_token: accessToken,
                token_type: tokenType,
                action_type: actionType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error al guardar el token:', data.error);
                alert('Error al procesar la autenticación: ' + data.error);
            } else {
                console.log('Token guardado correctamente');
                // Redirigir al dashboard
                window.location.href = '/dashboard/index.php';
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Error al procesar la autenticación. Por favor, inténtalo de nuevo.');
        });
    }
}
</script>