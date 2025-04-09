<?php
/**
 * Manejador para guardar el token de autenticación
 * 
 * Este script guarda el token de autenticación en la sesión y establece una cookie.
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

// Verificar que se proporcionó el token
if (!isset($data['access_token'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token de acceso es requerido']);
    exit;
}

$accessToken = $data['access_token'];
$tokenType = $data['token_type'] ?? 'bearer';
$actionType = $data['action_type'] ?? '';

// Verificar el token con Supabase
$url = getenv('SUPABASE_URL') . '/auth/v1/user';

// Configurar la solicitud
$options = [
    'http' => [
        'header' => [
            'Authorization: Bearer ' . $accessToken,
            'apikey: ' . getenv('SUPABASE_KEY')
        ],
        'method' => 'GET'
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
    echo json_encode(['error' => 'Error al verificar el token: ' . $error['message']]);
    exit;
}

// Decodificar la respuesta
$response = json_decode($result, true);

// Verificar si hay error en la respuesta
if (isset($response['error'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => $response['error_description'] ?? 'Token inválido']);
    exit;
}

// Guardar el token en la sesión
$_SESSION['supabase_token'] = $accessToken;
$_SESSION['user_id'] = $response['id'] ?? '';
$_SESSION['user_email'] = $response['email'] ?? '';

// Establecer cookie para el token
setcookie('supabase_auth', $accessToken, [
    'expires' => time() + 3600, // 1 hora
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Responder con éxito
header('Content-Type: application/json');
echo json_encode(['success' => true]);
