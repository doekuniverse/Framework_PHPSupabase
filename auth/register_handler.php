<?php
/**
 * Manejador de registro
 *
 * Este script procesa las solicitudes de registro y se comunica con Supabase.
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

// Verificar que se proporcionaron email, nickname y contraseña
if (!isset($data['email']) || !isset($data['password']) || !isset($data['nickname'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Email, nickname y contraseña son requeridos']);
    exit;
}

$email = $data['email'];
$password = $data['password'];
$nickname = $data['nickname'];

// Validar el formato del nickname
if (!preg_match('/^[a-zA-Z0-9_]+$/', $nickname) || strpos($nickname, ' ') !== false) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'El nickname solo puede contener letras, números y guiones bajos, sin espacios']);
    exit;
}

// Verificar que el email no contenga espacios
if (strpos($email, ' ') !== false) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'El email no puede contener espacios']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

// Validar contraseña
if (strlen($password) < 6) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

// Endpoint de registro de Supabase
$url = getenv('SUPABASE_URL') . '/auth/v1/signup';

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
            'password' => $password,
            'data' => [
                'display_name' => $nickname
            ]
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
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => $response['error_description'] ?? 'Error al registrarse']);
    exit;
}

// Verificar si se requiere confirmación de email
$redirectTo = '/auth/login.php';
if (isset($response['id']) && !isset($response['access_token'])) {
    // El usuario se registró pero necesita confirmar su email
    $redirectTo = '/auth/confirmation.php';
} elseif (isset($response['access_token'])) {
    // El usuario se registró y se autenticó automáticamente
    $_SESSION['supabase_token'] = $response['access_token'];

    // Establecer cookie para el token
    setcookie('supabase_auth', $response['access_token'], [
        'expires' => time() + 3600, // 1 hora
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    $redirectTo = '/dashboard/index.php';
}

// Responder con éxito
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'redirectTo' => $redirectTo
]);
