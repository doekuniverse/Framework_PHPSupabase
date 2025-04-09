<div align="center">

# 🚀 PHP Supabase Auth Framework

<img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white">
<img src="https://img.shields.io/badge/Supabase-3ECF8E?style=for-the-badge&logo=supabase&logoColor=white">
<img src="https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white">
<img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=json-web-tokens&logoColor=white">

**Un framework moderno, seguro y elegante para crear aplicaciones PHP con autenticación robusta**

[Ver Demo](#) | [Reportar Bug](#) | [Solicitar Función](#)

</div>

## 📋 Índice

- [Por qué elegir este framework](#-por-qué-elegir-este-framework)
- [Características](#-características)
- [Comenzando](#-comenzando)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Guía de uso](#-guía-de-uso)
- [Seguridad](#-seguridad)
- [Roadmap](#-roadmap)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

## 🌟 Por qué elegir este framework

![imagen](https://github.com/user-attachments/assets/72d269dd-1771-4604-83ba-c98fdd06b78e)


### La historia detrás del proyecto

Este framework nació de la frustración con las soluciones existentes. Queríamos algo que fuera:

- **Simple pero poderoso**: Sin curvas de aprendizaje pronunciadas
- **Moderno**: Utilizando las mejores prácticas y tecnologías actuales
- **Seguro por defecto**: Sin tener que preocuparse por configuraciones complejas de seguridad
- **Rápido de implementar**: De cero a producción en minutos, no en días

Después de meses de desarrollo y pruebas, creamos un framework que cumple con todos estos requisitos y más.

### Ventajas sobre otras soluciones

| Característica | Este Framework | Otros Frameworks |
|-----------------|---------------|------------------|
| Tiempo de configuración | ⏱️ Minutos | ⏱️⏱️⏱️ Horas/Días |
| Curva de aprendizaje | 📈 Baja | 📈📈📈 Alta |
| Seguridad | 🔒 Incorporada | 🔓 Configuración manual |
| Mantenimiento | 🔧 Mínimo | 🔧🔧🔧 Constante |
| Escalabilidad | 📊 Alta con Supabase | 📊📊 Variable |

## ✨ Características

### Sistema de autenticación completo

- 👤 Registro de usuarios con nicknames personalizados (@username)
- 🔐 Inicio de sesión seguro con validaciones robustas
- 🎫 Gestión de sesiones con tokens JWT
- 🚪 Cierre de sesión y manejo de tokens expirados

### Seguridad avanzada

- 🛡️ Protección contra ataques CSRF, XSS e inyección SQL
- 🔒 Cookies seguras (HttpOnly, Secure, SameSite)
- 🔍 Validación de entradas en frontend y backend
- 🚫 Páginas de error personalizadas y amigables (403, 404)

### Experiencia de desarrollo superior

- 🧩 Estructura modular y organizada
- 📝 Código limpio y bien documentado
- 🔄 Fácil de extender y personalizar
- 📱 Diseño responsive con Bootstrap 5

## 🚀 Comenzando

### Prerrequisitos

- PHP 7.4 o superior
- Servidor web (Apache recomendado)
- Cuenta en [Supabase](https://supabase.io)

### Instalación en 4 pasos

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/tu-repositorio.git
   cd tu-repositorio
   ```

2. **Configura las variables de entorno**
   ```bash
   cp .env-example .env
   # Edita el archivo .env con tu editor favorito
   ```

3. **Configura Supabase**
   - Crea un proyecto en [Supabase](https://supabase.io)
   - Habilita la autenticación por email
   - Copia las credenciales a tu archivo .env

4. **¡Listo para usar!**
   - Apunta tu servidor web al directorio del proyecto
   - Visita la URL en tu navegador

## 📂 Estructura del proyecto

```
📁 auth              # Sistema de autenticación
 ├ 📄 login.php      # Página de inicio de sesión
 ├ 📄 register.php   # Página de registro
 └ 📄 logout.php     # Cierre de sesión

📁 dashboard         # Área protegida para usuarios autenticados

📁 errors            # Páginas de error personalizadas

📁 include           # Componentes reutilizables
 ├ 📄 header.php     # Encabezado dinámico
 └ 📄 footer.php     # Pie de página

📁 middleware        # Lógica de autenticación y protección

📁 public            # Archivos públicos
 └ 📄 index.php      # Página principal

📄 .env              # Variables de entorno (no incluido en Git)
📄 .env-example      # Plantilla de variables de entorno
```

## 📘 Guía de uso

### Crear una página protegida

Añadir una nueva página protegida es tan simple como:

```php
<?php
// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Proteger esta página - muestra error 403 si no está autenticado
$user = $auth->protectPage('/errors/403.php');

// Incluir el header
include_once __DIR__ . '/../include/header.php';
?>

<div class="container mt-5">
    <h1>Mi Página Protegida</h1>
    <p>Bienvenido, <?php echo '@' . htmlspecialchars($user->user_metadata->display_name); ?>!</p>
</div>

<?php include_once __DIR__ . '/../include/footer.php'; ?>
```

### Personalizar el sistema

El framework está diseñado para ser fácilmente extensible:

- Modifica el diseño en `include/header.php` y `include/footer.php`
- Añade nuevas funcionalidades extendiendo `AuthMiddleware.php`
- Personaliza las páginas de error en el directorio `errors/`

## 🔒 Seguridad

Este framework implementa las mejores prácticas de seguridad:

- **Autenticación robusta**: Tokens JWT verificados en cada solicitud
- **Protección de datos**: Validación estricta de entradas de usuario
- **Cookies seguras**: Configuradas con HttpOnly, Secure y SameSite=Strict
- **Protección contra ataques comunes**: CSRF, XSS, inyección SQL
- **Manejo de errores seguro**: Mensajes de error personalizados sin exponer información sensible

## 🛣️ Roadmap

Estas son algunas de las características que planeamos implementar:

- [ ] Autenticación con redes sociales (Google, Facebook, GitHub)
- [ ] Sistema de roles y permisos
- [ ] Recuperación de contraseña mejorada
- [ ] Panel de administración
- [ ] Integración con API de Supabase para almacenamiento

## 👥 Contribuir

Las contribuciones son lo que hacen que la comunidad de código abierto sea un lugar increíble para aprender, inspirar y crear. Cualquier contribución que hagas será **muy apreciada**.

1. Haz un Fork del proyecto
2. Crea tu rama de funcionalidad (`git checkout -b feature/AmazingFeature`)
3. Haz commit de tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Haz Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📜 Licencia

Distribuido bajo la Licencia MIT. Ver `LICENSE` para más información.

## 📬 Contacto

Tu Nombre - [@tu_twitter](https://twitter.com/tu_twitter) - email@ejemplo.com

Enlace del proyecto: [https://github.com/tu-usuario/tu-repositorio](https://github.com/tu-usuario/tu-repositorio)

---

<div align="center">
Hecho con ❤️ por [Tu Nombre](https://github.com/tu-usuario)
</div>
