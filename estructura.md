



HTDOCS
|- auth (Carpeta que contiene Login & Registro)
|- components (Carpeta que contiene componentes reutilizables)
|- dashboard (Carpeta que contiene archivos protegidos con tokens de usuario)
|-- index.php (Archivo principal de la página de inicio protegido por el token de usuario)
|- include (Carpeta que contiene archivos de inclusión)
|-- header.php (Archivo de encabezado que cambia si el usuario está autenticado o no)
|- middleware (Carpeta que contiene archivos de middleware)
|- public (Carpeta que contiene archivos públicos)
|-- index.php (Archivo principal de la página de inicio)
|.env (Archivo de configuración de variables de entorno)
|estructura.md (Archivo de documentación de la estructura del proyecto)

Estructura de carpetas y archivos del proyecto para comenzar un framework propio de inicializacion de proyectos autenticados con tokens de usuario con supabase y php.