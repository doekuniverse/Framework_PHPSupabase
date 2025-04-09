<?php
/**
 * Archivo principal de redirección
 *
 * Este archivo maneja las redirecciones desde la raíz del sitio.
 * Si hay un hash en la URL (como en las redirecciones de Supabase),
 * redirige a la página de callback para procesar la autenticación.
 */

// Verificar si hay un hash en la URL
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/' && !empty($_SERVER['QUERY_STRING'])) {
    // Redirigir a la página principal
    header('Location: /public/index.php');
    exit;
} else {
    // Redirigir a la página de callback para procesar la autenticación
    header('Location: /auth/callback.php');
    exit;
}
?>
