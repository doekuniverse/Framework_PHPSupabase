# Aplicación PHP con Autenticación Supabase

Este proyecto es una aplicación web PHP que implementa un sistema de autenticación completo utilizando Supabase como backend.

## Características

- Registro de usuarios con nickname personalizado (@username)
- Inicio de sesión seguro
- Gestión de sesiones con tokens JWT
- Páginas protegidas con middleware de autenticación
- Interfaz adaptativa según el estado de autenticación
- Páginas de error personalizadas (403, 404)
- Validaciones de seguridad en formularios

## Requisitos

- PHP 7.4 o superior
- Servidor web (Apache recomendado)
- Cuenta en Supabase (https://supabase.io)

## Instalación

1. Clona este repositorio:
   ```
   git clone https://github.com/tu-usuario/tu-repositorio.git
   cd tu-repositorio
   ```

2. Copia el archivo de ejemplo de variables de entorno:
   ```
   cp .env-example .env
   ```

3. Edita el archivo `.env` con tus credenciales de Supabase:
   ```
   SUPABASE_URL=https://tu-proyecto.supabase.co
   SUPABASE_KEY=tu-clave-anon-publica
   ```

4. Configura tu servidor web para que apunte al directorio del proyecto.

## Estructura del Proyecto

```
|- auth (Carpeta que contiene Login & Registro)
|- dashboard (Carpeta que contiene archivos protegidos)
|- errors (Carpeta que contiene páginas de error personalizadas)
|- include (Carpeta que contiene archivos de inclusión)
|-- header.php (Archivo de encabezado dinámico)
|- middleware (Carpeta que contiene archivos de middleware)
|- public (Carpeta que contiene archivos públicos)
|-- index.php (Archivo principal de la página de inicio)
|.env (Archivo de configuración de variables de entorno)
|.env-example (Ejemplo de configuración de variables de entorno)
```

## Configuración de Supabase

1. Crea una cuenta en Supabase (https://supabase.io)
2. Crea un nuevo proyecto
3. En la sección "Authentication" > "Settings", configura:
   - Habilita "Email" como proveedor de autenticación
   - Configura las URLs de redirección según tu entorno

## Seguridad

Este proyecto implementa varias medidas de seguridad:
- Tokens JWT para autenticación
- Cookies seguras (HttpOnly, Secure, SameSite)
- Validación de entradas en frontend y backend
- Protección contra ataques CSRF
- Manejo seguro de errores

## Licencia

[Especifica tu licencia aquí]

## Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue primero para discutir los cambios que te gustaría hacer.
