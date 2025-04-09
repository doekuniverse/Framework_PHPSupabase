<?php
/**
 * Archivo de inicialización del middleware
 * 
 * Este archivo carga las configuraciones necesarias y crea una instancia
 * del middleware de autenticación para ser utilizada en toda la aplicación.
 */

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir el middleware de autenticación
require_once __DIR__ . '/AuthMiddleware.php';

// Cargar variables de entorno desde .env
// Nota: Esto asume que tienes un método para cargar variables de entorno
// Si no tienes uno, puedes usar la biblioteca vlucas/phpdotenv
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parsear línea
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Establecer variable de entorno
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Cargar variables de entorno
loadEnv();

// Obtener configuración de Supabase
$supabaseUrl = getenv('SUPABASE_URL') ?: '';
$supabaseKey = getenv('SUPABASE_KEY') ?: '';

// Crear instancia del middleware
$auth = new AuthMiddleware($supabaseUrl, $supabaseKey);

// Función auxiliar para verificar si el usuario está autenticado
function isLoggedIn() {
    global $auth;
    return $auth->isAuthenticated();
}

// Función auxiliar para obtener los datos del usuario actual
function getCurrentUser() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user'] ?? null;
}
