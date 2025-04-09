<?php
/**
 * Página de callback para autenticación
 *
 * Esta página maneja las redirecciones de Supabase después de:
 * - Confirmación de email
 * - Restablecimiento de contraseña
 * - Inicio de sesión con proveedores externos
 */

// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Incluir el encabezado
include_once __DIR__ . '/../include/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Procesando autenticación...</h4>
                </div>
                <div class="card-body text-center">
                    <div class="spinner-border text-primary mb-4" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p>Estamos procesando tu autenticación. Por favor, espera un momento...</p>
                    <div class="alert alert-info mt-3 small d-none" id="debugInfo">
                        <strong>Información de depuración:</strong>
                        <pre id="debugText"></pre>
                    </div>
                    <div class="mt-4 d-none" id="manualContinue">
                        <p class="text-danger">Si la redirección automática no funciona, haz clic en el botón a continuación:</p>
                        <button id="continueButton" class="btn btn-primary">Continuar al Dashboard</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para obtener parámetros de la URL (tanto del hash como de la query string)
function getUrlParameter(name) {
    // Primero buscar en el hash (para tokens de acceso)
    const hash = window.location.hash.substring(1);
    const hashParams = new URLSearchParams(hash);
    const hashValue = hashParams.get(name);

    if (hashValue) return hashValue;

    // Si no está en el hash, buscar en la query string (para tokens de confirmación)
    const queryParams = new URLSearchParams(window.location.search);
    return queryParams.get(name);
}

// Esta función extrae el token de acceso de la URL
function getAccessToken() {
    return getUrlParameter('access_token');
}

// Esta función extrae el token de tipo de la URL
function getTokenType() {
    return getUrlParameter('token_type') || 'bearer';
}

// Esta función extrae el tipo de acción de la URL
function getActionType() {
    return getUrlParameter('type');
}

// Esta función extrae el token de confirmación de la URL
function getConfirmationToken() {
    return getUrlParameter('confirmation_token') || getUrlParameter('token');
}

// Función principal que se ejecuta cuando la página carga
document.addEventListener('DOMContentLoaded', async function() {
    const accessToken = getAccessToken();
    const confirmationToken = getConfirmationToken();
    const tokenType = getTokenType();
    const actionType = getActionType();

    // Mostrar información de depuración en la consola y en la página
    console.log('URL:', window.location.href);
    console.log('Access Token:', accessToken);
    console.log('Confirmation Token:', confirmationToken);
    console.log('Token Type:', tokenType);
    console.log('Action Type:', actionType);

    // Mostrar información de depuración en la página
    const debugInfo = document.getElementById('debugInfo');
    const debugText = document.getElementById('debugText');

    if (debugInfo && debugText) {
        const debugData = {
            url: window.location.href,
            hash: window.location.hash,
            search: window.location.search,
            accessToken: accessToken ? accessToken.substring(0, 10) + '...' : null,
            confirmationToken: confirmationToken ? confirmationToken.substring(0, 10) + '...' : null,
            tokenType: tokenType,
            actionType: actionType
        };

        debugText.textContent = JSON.stringify(debugData, null, 2);
        debugInfo.classList.remove('d-none');

        // Mostrar el botón de continuación manual después de 5 segundos
        setTimeout(() => {
            const manualContinue = document.getElementById('manualContinue');
            if (manualContinue) {
                manualContinue.classList.remove('d-none');
            }
        }, 5000);
    }

    // Configurar el botón de continuación manual
    const continueButton = document.getElementById('continueButton');
    if (continueButton) {
        continueButton.addEventListener('click', function() {
            if (accessToken) {
                // Guardar el token manualmente y redirigir
                localStorage.setItem('supabase_token', accessToken);
                window.location.href = '/dashboard/index.php';
            } else {
                // Si no hay token, redirigir a la página de login
                window.location.href = '/auth/login.php';
            }
        });
    }

    // Si tenemos un token de confirmación pero no un token de acceso
    if (confirmationToken && !accessToken) {
        try {
            // Procesar el token de confirmación
            const response = await fetch('/auth/process_confirmation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    confirmation_token: confirmationToken
                })
            });

            const data = await response.json();

            if (data.error) {
                // Error al procesar el token de confirmación
                window.location.href = '/auth/login.php?error=' + encodeURIComponent(data.error);
            } else if (data.access_token) {
                // Si recibimos un token de acceso, guardarlo y redirigir al dashboard
                const saveResponse = await fetch('/auth/save_token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        access_token: data.access_token,
                        token_type: data.token_type || 'bearer',
                        action_type: 'confirmation'
                    })
                });

                const saveData = await saveResponse.json();

                if (saveData.error) {
                    window.location.href = '/auth/login.php?error=' + encodeURIComponent(saveData.error);
                } else {
                    window.location.href = '/dashboard/index.php';
                }
            } else {
                // Si no hay error ni token, mostrar mensaje de éxito y redirigir a login
                window.location.href = '/auth/login.php?success=' + encodeURIComponent('Tu correo ha sido confirmado. Ahora puedes iniciar sesión.');
            }
            return;
        } catch (error) {
            console.error('Error al procesar el token de confirmación:', error);
            window.location.href = '/auth/login.php?error=' + encodeURIComponent('Error al procesar la confirmación');
            return;
        }
    }

    // Si tenemos un token de acceso (flujo normal)
    if (accessToken) {
        try {
            // Guardar el token en la sesión
            const response = await fetch('/auth/save_token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    access_token: accessToken,
                    token_type: tokenType,
                    action_type: actionType
                })
            });

            const data = await response.json();

            if (data.error) {
                // Error al guardar el token
                window.location.href = '/auth/login.php?error=' + encodeURIComponent(data.error);
            } else {
                // Token guardado correctamente, redirigir al dashboard
                window.location.href = '/dashboard/index.php';
            }
            return;
        } catch (error) {
            console.error('Error al guardar el token:', error);
            window.location.href = '/auth/login.php?error=' + encodeURIComponent('Error al procesar la autenticación');
            return;
        }
    }

    // Si no hay ningún tipo de token
    window.location.href = '/auth/login.php?error=' + encodeURIComponent('No se pudo procesar la autenticación. No se encontró ningún token.');
}
});
</script>

<?php
// Incluir el pie de página
include_once __DIR__ . '/../include/footer.php';
?>
