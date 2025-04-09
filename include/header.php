<?php
/**
 * Header dinámico
 *
 * Este header cambia según si el usuario está autenticado o no.
 * Se incluye en todas las páginas del sitio.
 */

// Si no se ha incluido el middleware, incluirlo
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../middleware/init.php';
}

// Determinar si el usuario está autenticado
$isAuthenticated = isLoggedIn();
$currentUser = $isAuthenticated ? getCurrentUser() : null;

// Determinar la ruta actual para resaltar el elemento de navegación activo
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación con Supabase</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .dropdown-menu {
            min-width: 200px;
        }

        .dropdown-item-text {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/public/index.php">Mi Aplicación</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPath == '/public/index.php' || $currentPath == '/' ? 'active' : ''; ?>" href="/public/index.php">Inicio</a>
                    </li>

                    <?php if ($isAuthenticated): ?>
                    <!-- Enlaces para usuarios autenticados -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($currentPath, '/dashboard') === 0 ? 'active' : ''; ?>" href="/dashboard/index.php">Dashboard</a>
                    </li>
                    <?php endif; ?>

                    
                </ul>

                <div class="d-flex">
                    <?php if ($isAuthenticated): ?>
                    <!-- Usuario autenticado: mostrar menú desplegable -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (isset($currentUser->user_metadata->avatar_url)): ?>
                            <img src="<?php echo htmlspecialchars($currentUser->user_metadata->avatar_url); ?>" alt="Avatar" class="user-avatar">
                            <?php endif; ?>
                            <?php
                            // Mostrar nickname si existe, de lo contrario mostrar email
                            if (isset($currentUser->user_metadata->display_name)) {
                                echo '@' . htmlspecialchars($currentUser->user_metadata->display_name);
                            } else {
                                echo htmlspecialchars($currentUser->email);
                            }
                            ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text">
                                <?php
                                // Mostrar nickname y email
                                if (isset($currentUser->user_metadata->display_name)) {
                                    echo '@' . htmlspecialchars($currentUser->user_metadata->display_name) . '<br>';
                                    echo '<small class="text-muted">' . htmlspecialchars($currentUser->email) . '</small>';
                                } else {
                                    echo htmlspecialchars($currentUser->email);
                                }
                                ?>
                            </span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/dashboard/index.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="/dashboard/profile.php">Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/auth/logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <!-- Usuario no autenticado: mostrar botones de login/registro -->
                    <a href="/auth/login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                    <a href="/auth/register.php" class="btn btn-light">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal para el contenido de la página -->
    <main>
        <!-- El contenido de la página se insertará aquí -->
