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
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al comunicarse con Supabase: ' . $error['message']]);
    exit;
}

// Decodificar la respuesta
$response = json_decode($result, true);

// Verificar si hay error en la respuesta
if (isset($response['error'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: application/json');
    echo json_encode(['error' => $response['error_description'] ?? 'Credenciales inválidas']);
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
