<?php
/**
 * Archivo principal de redirección
 *
 * Este archivo maneja las redirecciones desde la raíz del sitio.
 * Si hay un query string en la URL o un hash con token (como en las redirecciones de Supabase),
 * redirige a la página de callback para procesar la autenticación.
 * De lo contrario, redirige a la página principal.
 */

// Verificar si hay un query string en la URL
if (!empty($_SERVER['QUERY_STRING'])) {
    // Redirigir a la página de callback para procesar la autenticación
    header('Location: /auth/callback.php?' . $_SERVER['QUERY_STRING']);
    exit;
} else {
    // Comprobar si hay un hash en la URL usando JavaScript
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Redireccionando...</title>
</head>
<body>
    <p>Redireccionando, por favor espera...</p>
    <script>
        // Comprobar si hay un hash en la URL
        if (window.location.hash && window.location.hash.includes('access_token')) {
            // Redirigir a la página de callback con el hash
            window.location.href = '/auth/callback.php' + window.location.hash;
        } else {
            // Redirigir a la página principal
            window.location.href = '/public/index.php';
        }
    </script>
</body>
</html>";
    exit;
}
?>
