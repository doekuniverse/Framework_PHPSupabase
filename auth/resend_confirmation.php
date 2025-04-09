<?php
/**
 * Manejador para reenviar el correo de confirmación
 * 
 * Este script procesa las solicitudes para reenviar el correo de confirmación.
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

// Verificar que se proporcionó el email
if (!isset($data['email'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Email es requerido']);
    exit;
}

$email = $data['email'];

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

// Endpoint para reenviar el correo de confirmación en Supabase
$url = getenv('SUPABASE_URL') . '/auth/v1/resend';

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
            'type' => 'signup'
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
    echo json_encode(['error' => $response['error_description'] ?? 'Error al reenviar el correo de confirmación']);
    exit;
}

// Responder con éxito
header('Content-Type: application/json');
echo json_encode(['success' => true]);
