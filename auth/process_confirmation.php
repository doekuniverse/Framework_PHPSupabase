<?php
/**
 * Manejador para procesar tokens de confirmación
 * 
 * Este script procesa los tokens de confirmación de email y devuelve un token de acceso.
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

// Verificar que se proporcionó el token de confirmación
if (!isset($data['confirmation_token'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token de confirmación es requerido']);
    exit;
}

$confirmationToken = $data['confirmation_token'];

// Endpoint para verificar el token de confirmación
$url = getenv('SUPABASE_URL') . '/auth/v1/verify';

// Configurar la solicitud
$options = [
    'http' => [
        'header' => [
            'Content-Type: application/json',
            'apikey: ' . getenv('SUPABASE_KEY')
        ],
        'method' => 'POST',
        'content' => json_encode([
            'type' => 'signup',
            'token' => $confirmationToken
        ])
    ]
];

// Realizar la solicitud
$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

// Verificar si la solicitud fue exitosa
if ($result === false) {
    $error = error_get_last();
    
    // Registrar el error para depuración
    error_log('Error al procesar token de confirmación: ' . ($error['message'] ?? 'Error desconocido'));
    
    // Obtener información adicional sobre la respuesta HTTP
    $responseHeaders = $http_response_header ?? [];
    $statusLine = $responseHeaders[0] ?? '';
    error_log('Respuesta HTTP: ' . $statusLine);
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Error al procesar el token de confirmación. Por favor, inténtalo de nuevo más tarde.',
        'debug_info' => [
            'error_message' => $error['message'] ?? 'Error desconocido',
            'status_line' => $statusLine
        ]
    ]);
    exit;
}

// Decodificar la respuesta
$response = json_decode($result, true);

// Verificar si hay error en la respuesta
if (isset($response['error'])) {
    $errorMsg = 'Error al procesar el token de confirmación: ' . ($response['error_description'] ?? $response['error']);
    error_log($errorMsg);
    
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $errorMsg,
        'debug_info' => $response
    ]);
    exit;
}

// Si la confirmación fue exitosa pero no tenemos un token de acceso,
// intentar iniciar sesión automáticamente
if (!isset($response['access_token']) && isset($response['email'])) {
    // Intentar obtener un token de acceso usando la API de inicio de sesión mágico
    $loginUrl = getenv('SUPABASE_URL') . '/auth/v1/token?grant_type=password';
    
    // Configurar la solicitud
    $loginOptions = [
        'http' => [
            'header' => [
                'Content-Type: application/json',
                'apikey: ' . getenv('SUPABASE_KEY')
            ],
            'method' => 'POST',
            'content' => json_encode([
                'email' => $response['email'],
                'password' => '' // No tenemos la contraseña, pero podemos intentar
            ])
        ]
    ];
    
    // Realizar la solicitud
    $loginContext = stream_context_create($loginOptions);
    $loginResult = @file_get_contents($loginUrl, false, $loginContext);
    
    if ($loginResult !== false) {
        $loginResponse = json_decode($loginResult, true);
        
        if (isset($loginResponse['access_token'])) {
            // Si obtuvimos un token, usarlo
            $response = $loginResponse;
        }
    }
}

// Responder con el token de acceso si está disponible
header('Content-Type: application/json');
if (isset($response['access_token'])) {
    echo json_encode([
        'success' => true,
        'access_token' => $response['access_token'],
        'token_type' => $response['token_type'] ?? 'bearer',
        'expires_in' => $response['expires_in'] ?? 3600,
        'refresh_token' => $response['refresh_token'] ?? null
    ]);
} else {
    // Si no hay token de acceso, simplemente indicar que la confirmación fue exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Email confirmado correctamente. Por favor, inicia sesión.'
    ]);
}
