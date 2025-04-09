<?php
/**
 * Manejador de inicio de sesión
 *
 * Este script procesa las solicitudes de inicio de sesión y se comunica con Supabase.
 */

// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Asegurarse de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Obtener datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que se proporcionaron email y contraseña
if (!isset($data['email']) || !isset($data['password'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Email y contraseña son requeridos']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

// Endpoint de inicio de sesión de Supabase
$url = getenv('SUPABASE_URL') . '/auth/v1/token?grant_type=password';

// Configurar la solicitud
$options = [
    'http' => [
        'header' => [
            'Content-Type: application/json',
            'apikey: ' . getenv('SUPABASE_KEY')
        ],
        'method' => 'POST',
        'content' => json_encode([
            'email' => $email,
            'password' => $password
        ])
    ]
];

// Realizar la solicitud
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

// Verificar si la solicitud fue exitosa
if ($result === false) {
    $error = error_get_last();

    // Registrar el error completo en logs para depuración (opcional)
    error_log('Error de autenticación: ' . $error['message']);

    // Determinar el tipo de error para mostrar un mensaje más amigable
    $errorMessage = 'Error al iniciar sesión. Por favor, inténtalo de nuevo más tarde.';

    // Comprobar si es un error de conexión o de credenciales
    if (strpos($error['message'], '400 Bad Request') !== false) {
        $errorMessage = 'Credenciales incorrectas. Por favor, verifica tu email y contraseña.';
    } elseif (strpos($error['message'], 'Failed to open stream') !== false) {
        $errorMessage = 'No se pudo conectar con el servidor de autenticación. Por favor, verifica tu conexión a internet.';
    }

    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: application/json');
    echo json_encode(['error' => $errorMessage]);
    exit;
}

// Decodificar la respuesta
$response = json_decode($result, true);

// Verificar si hay error en la respuesta
if (isset($response['error'])) {
    // Registrar el error completo en logs para depuración (opcional)
    error_log('Error de autenticación Supabase: ' . ($response['error_description'] ?? $response['error']));

    // Determinar un mensaje de error amigable basado en el código de error
    $errorMessage = 'Credenciales incorrectas. Por favor, verifica tu email y contraseña.';

    // Personalizar mensajes según el tipo de error
    if (isset($response['error_description'])) {
        if (strpos($response['error_description'], 'Invalid login credentials') !== false) {
            $errorMessage = 'Email o contraseña incorrectos. Por favor, inténtalo de nuevo.';
        } elseif (strpos($response['error_description'], 'Email not confirmed') !== false) {
            $errorMessage = 'Tu email aún no ha sido confirmado. Por favor, revisa tu bandeja de entrada.';
        } elseif (strpos($response['error_description'], 'rate limit') !== false) {
            $errorMessage = 'Demasiados intentos fallidos. Por favor, inténtalo de nuevo más tarde.';
        }
    }

    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: application/json');
    echo json_encode(['error' => $errorMessage]);
    exit;
}

// Extraer el token de acceso
$accessToken = $response['access_token'] ?? null;

if (!$accessToken) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se pudo obtener el token de acceso']);
    exit;
}

// Guardar el token en la sesión
$_SESSION['supabase_token'] = $accessToken;

// Establecer cookie para el token (opcional, pero útil para JavaScript)
setcookie('supabase_auth', $accessToken, [
    'expires' => time() + 3600, // 1 hora
    'path' => '/',
    'secure' => true, // Solo HTTPS
    'httponly' => true, // No accesible por JavaScript
    'samesite' => 'Strict' // Protección CSRF
]);

// Responder con éxito
header('Content-Type: application/json');
echo json_encode(['success' => true]);
