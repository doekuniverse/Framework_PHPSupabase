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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Esta función extrae el token de acceso de la URL
function getAccessToken() {
    // Buscar en el hash de la URL
    const hash = window.location.hash.substring(1);
    const params = new URLSearchParams(hash);
    return params.get('access_token');
}

// Esta función extrae el token de tipo de la URL
function getTokenType() {
    const hash = window.location.hash.substring(1);
    const params = new URLSearchParams(hash);
    return params.get('token_type');
}

// Esta función extrae el tipo de acción de la URL
function getActionType() {
    const hash = window.location.hash.substring(1);
    const params = new URLSearchParams(hash);
    return params.get('type');
}

// Función principal que se ejecuta cuando la página carga
document.addEventListener('DOMContentLoaded', async function() {
    const accessToken = getAccessToken();
    const tokenType = getTokenType();
    const actionType = getActionType();
    
    if (!accessToken) {
        // No hay token, redirigir a la página de inicio de sesión
        window.location.href = '/auth/login.php?error=' + encodeURIComponent('No se pudo procesar la autenticación');
        return;
    }
    
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
    } catch (error) {
        // Error en la solicitud
        window.location.href = '/auth/login.php?error=' + encodeURIComponent('Error al procesar la autenticación');
    }
});
</script>

<?php
// Incluir el pie de página
include_once __DIR__ . '/../include/footer.php';
?>
