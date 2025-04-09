<?php
/**
 * Middleware de autenticación para Supabase
 *
 * Este middleware verifica los tokens JWT de Supabase y controla
 * el acceso a rutas protegidas en la aplicación PHP.
 * Diseñado específicamente para la estructura del proyecto.
 */
class AuthMiddleware {
    private $supabaseUrl;
    private $supabaseKey;

    /**
     * Constructor del middleware
     *
     * @param string $supabaseUrl URL de tu proyecto Supabase
     * @param string $supabaseKey Clave anon/service_role de Supabase
     */
    public function __construct($supabaseUrl, $supabaseKey) {
        $this->supabaseUrl = $supabaseUrl;
        $this->supabaseKey = $supabaseKey;
    }

    /**
     * Verifica si el usuario está autenticado
     *
     * @return bool True si el usuario está autenticado, false en caso contrario
     */
    public function isAuthenticated() {
        $token = $this->getToken();

        if (!$token) {
            return false;
        }

        try {
            // Verificar el token con Supabase
            $userData = $this->verifyTokenWithSupabase($token);

            // Guardar datos del usuario en la sesión para uso posterior
            $_SESSION['user'] = $userData;

            return true;
        } catch (Exception $e) {
            // Limpiar cualquier sesión existente
            $this->clearSession();
            return false;
        }
    }

    /**
     * Protege una página, redirigiendo si el usuario no está autenticado
     *
     * @param string $redirectTo Ruta a la que redirigir si no está autenticado
     * @return object|null Datos del usuario si está autenticado, null si se redirige
     */
    public function protectPage($redirectTo = '/auth/login.php') {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAuthenticated()) {
            // Si se especifica una página de error 403 personalizada, usarla
            if ($redirectTo === '/errors/403.php') {
                // Incluir la página de error 403 personalizada
                include_once $_SERVER['DOCUMENT_ROOT'] . '/errors/403.php';
                exit;
            } else {
                // Redirigir a la página especificada (por defecto login)
                header('Location: ' . $redirectTo);
                exit;
            }
        }

        // Devolver los datos del usuario para uso en la página
        return $_SESSION['user'] ?? null;
    }

    /**
     * Protege una API, devolviendo error JSON si no está autenticado
     *
     * @return object|null Datos del usuario si está autenticado, null si se devuelve error
     */
    public function protectApi() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            http_response_code(403); // Cambiado de 401 a 403 para ser consistente
            echo json_encode(['error' => 'Acceso prohibido', 'message' => 'No tienes permiso para acceder a este recurso']);
            exit;
        }

        // Devolver los datos del usuario para uso en la API
        return $_SESSION['user'] ?? null;
    }

    /**
     * Obtiene el token JWT de varias fuentes posibles
     *
     * @return string|null Token JWT o null si no se encuentra
     */
    private function getToken() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Intentar obtener de encabezado Authorization
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        if (isset($headers['Authorization'])) {
            return str_replace('Bearer ', '', $headers['Authorization']);
        }

        // 2. Intentar obtener de cookie
        if (isset($_COOKIE['supabase_auth'])) {
            return $_COOKIE['supabase_auth'];
        }

        // 3. Intentar obtener de la sesión PHP
        if (isset($_SESSION['supabase_token'])) {
            return $_SESSION['supabase_token'];
        }

        // 4. Verificar si hay un token en la URL (para redirecciones de confirmación)
        if (isset($_GET['access_token'])) {
            // Guardar el token en la sesión para futuras solicitudes
            $_SESSION['supabase_token'] = $_GET['access_token'];
            return $_GET['access_token'];
        }

        return null;
    }

    /**
     * Verifica el token usando la API de Supabase
     *
     * @param string $token Token JWT a verificar
     * @return object Datos del usuario decodificados
     * @throws Exception Si el token es inválido
     */
    private function verifyTokenWithSupabase($token) {
        // Endpoint para obtener el usuario actual
        $url = $this->supabaseUrl . '/auth/v1/user';

        // Configurar la solicitud
        $options = [
            'http' => [
                'header' => [
                    'Authorization: Bearer ' . $token,
                    'apikey: ' . $this->supabaseKey
                ],
                'method' => 'GET'
            ]
        ];

        // Realizar la solicitud
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            throw new Exception('Error al verificar el token con Supabase');
        }

        // Decodificar la respuesta
        $userData = json_decode($result);

        if (empty($userData) || isset($userData->error)) {
            throw new Exception('Token inválido o expirado');
        }

        return $userData;
    }

    /**
     * Limpia la sesión y cookies relacionadas con la autenticación
     */
    public function clearSession() {
        // Limpiar cookie
        if (isset($_COOKIE['supabase_auth'])) {
            setcookie('supabase_auth', '', time() - 3600, '/');
        }

        // Limpiar sesión
        if (session_status() == PHP_SESSION_ACTIVE) {
            unset($_SESSION['supabase_token']);
            unset($_SESSION['user']);
        }
    }

    /**
     * Cierra la sesión del usuario
     *
     * @param string $redirectTo Ruta a la que redirigir después de cerrar sesión
     */
    public function logout($redirectTo = '/') {
        $this->clearSession();

        // Redirigir
        header('Location: ' . $redirectTo);
        exit;
    }
}
