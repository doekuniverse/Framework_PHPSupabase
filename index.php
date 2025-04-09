<?php
/**
 * Archivo principal de redirección
 *
 * Este archivo maneja las redirecciones desde la raíz del sitio.
 * Si hay un query string en la URL (como en las redirecciones de Supabase),
 * redirige a la página de callback para procesar la autenticación.
 * De lo contrario, redirige a la página principal.
 */

// Verificar si hay un query string en la URL
if (!empty($_SERVER['QUERY_STRING'])) {
    // Redirigir a la página de callback para procesar la autenticación
    header('Location: /auth/callback.php');
    exit;
} else {
    // Redirigir a la página principal
    header('Location: /public/index.php');
    exit;
}
?>
