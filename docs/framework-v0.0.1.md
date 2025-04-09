# Documentación del Framework PHP con Supabase - Versión 0.0.1

## Introducción

Este framework es una solución ligera para desarrollar aplicaciones web PHP con autenticación basada en Supabase. Proporciona una estructura organizada y componentes reutilizables para acelerar el desarrollo de aplicaciones web.

## Características principales

- **Autenticación completa con Supabase**: Registro, inicio de sesión, confirmación de email, recuperación de contraseña
- **Middleware de autenticación**: Protección de rutas y verificación de sesiones
- **Estructura modular**: Organización clara de archivos y componentes
- **Interfaz adaptativa**: Diseño responsive basado en Bootstrap 5
- **Gestión de perfiles de usuario**: Edición de información de perfil y preferencias

## Estructura de directorios

```
/
├── auth/                     # Componentes de autenticación
│   ├── callback.php          # Maneja redirecciones de autenticación
│   ├── confirmation.php      # Página de confirmación de email
│   ├── login.php             # Página de inicio de sesión
│   ├── logout.php            # Maneja el cierre de sesión
│   ├── process_confirmation.php # Procesa tokens de confirmación
│   ├── register.php          # Página de registro
│   ├── resend_confirmation.php # Reenvía emails de confirmación
│   ├── reset_password.php    # Página de restablecimiento de contraseña
│   └── save_token.php        # Guarda tokens de autenticación
├── dashboard/                # Área protegida para usuarios autenticados
│   ├── index.php             # Página principal del dashboard
│   └── profile.php           # Página de perfil de usuario
├── include/                  # Componentes reutilizables
│   ├── footer.php            # Pie de página común
│   └── header.php            # Encabezado común con navegación
├── middleware/               # Middleware para procesamiento de solicitudes
│   ├── AuthMiddleware.php    # Middleware de autenticación
│   └── init.php              # Inicialización de la aplicación
├── public/                   # Páginas públicas
│   └── index.php             # Página de inicio pública
├── .env                      # Variables de entorno (no en control de versiones)
├── .env.example              # Ejemplo de variables de entorno
└── index.php                 # Punto de entrada principal
```

## Requisitos del sistema

- PHP 7.4 o superior
- Extensión PHP cURL habilitada
- Extensión PHP JSON habilitada
- Cuenta de Supabase (gratuita o de pago)

## Configuración

### Variables de entorno

El framework utiliza un archivo `.env` para configurar las variables de entorno. Copia el archivo `.env.example` a `.env` y configura las siguientes variables:

```
SUPABASE_URL=https://tu-proyecto.supabase.co
SUPABASE_KEY=tu-clave-anon-publica
SUPABASE_SERVICE_KEY=tu-clave-de-servicio
APP_URL=http://localhost
```
### Registro de usuario

1. El usuario accede a `/auth/register.php`
2. Completa el formulario con email y contraseña
3. Se envía la solicitud a Supabase Auth API
4. Se crea el usuario y se envía un email de confirmación
5. El usuario es redirigido a `/auth/confirmation.php`

### Confirmación de email

1. El usuario hace clic en el enlace de confirmación en su email
2. El enlace redirige a la aplicación con un token en la URL
3. La aplicación procesa el token y confirma la cuenta
4. El usuario es autenticado automáticamente y redirigido al dashboard

### Inicio de sesión

1. El usuario accede a `/auth/login.php`
2. Ingresa su email y contraseña
3. Se envía la solicitud a Supabase Auth API
4. Si las credenciales son válidas, se genera un token JWT
5. El token se almacena en la sesión y en una cookie
6. El usuario es redirigido al dashboard

### Cierre de sesión

1. El usuario hace clic en "Cerrar sesión"
2. Se envía una solicitud a `/auth/logout.php`
3. Se elimina el token de la sesión y la cookie
4. El usuario es redirigido a la página de inicio

### Restablecimiento de contraseña

1. El usuario accede a `/auth/reset_password.php`
2. Ingresa su email
3. Se envía la solicitud a Supabase Auth API
4. Se envía un email con un enlace para restablecer la contraseña
5. El usuario hace clic en el enlace y establece una nueva contraseña
6. El usuario es redirigido a la página de inicio de sesión

## Middleware de autenticación

El middleware de autenticación (`middleware/AuthMiddleware.php`) proporciona las siguientes funcionalidades:

- Verificación de tokens JWT
- Protección de rutas para usuarios autenticados
- Obtención de información del usuario actual
- Redirección a la página de inicio de sesión para usuarios no autenticados

### Uso del middleware

```php
// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Verificar si el usuario está autenticado
if (!isLoggedIn()) {
    // Redirigir a la página de inicio de sesión
    header('Location: /auth/login.php');
    exit;
}

// Obtener información del usuario actual
$currentUser = getCurrentUser();
```

## Gestión de perfiles

La página de perfil (`dashboard/profile.php`) permite a los usuarios:

- Ver su información de perfil
- Actualizar su nombre de visualización
- Actualizar su sitio web (opcional)
- Ver su dirección de email (no editable)

## Integración con Supabase

El framework utiliza la API REST de Supabase para:

- Autenticación de usuarios
- Almacenamiento de perfiles
- Verificación de tokens

### Endpoints utilizados

- `/auth/v1/signup` - Registro de usuarios
- `/auth/v1/token` - Inicio de sesión y obtención de tokens
- `/auth/v1/logout` - Cierre de sesión
- `/auth/v1/user` - Obtención de información del usuario actual
- `/auth/v1/verify` - Verificación de tokens de confirmación
- `/auth/v1/recover` - Recuperación de contraseñas
- `/rest/v1/profiles` - Gestión de perfiles de usuario

## Seguridad

El framework implementa las siguientes medidas de seguridad:

- Tokens JWT para autenticación
- Almacenamiento seguro de tokens en cookies HttpOnly
- Validación de entradas de usuario
- Protección contra CSRF
- Encabezados de seguridad HTTP

## Limitaciones conocidas

- No incluye manejo de roles y permisos avanzados
- No implementa autenticación de dos factores
- No incluye integración con proveedores OAuth (Google, Facebook, etc.)
- No incluye funcionalidades de carga de archivos
- No implementa caché de sesiones

## Próximas mejoras

Para futuras versiones, se planean las siguientes mejoras:

- Implementación de roles y permisos
- Autenticación de dos factores
- Integración con proveedores OAuth
- Sistema de carga de archivos
- Mejora del sistema de perfiles
- Implementación de un sistema de notificaciones
- Mejora de la interfaz de usuario
- Documentación más detallada

## Contribución

Este framework está en desarrollo activo. Las contribuciones son bienvenidas a través de pull requests.

## Licencia

Este framework se distribuye bajo la licencia MIT.
