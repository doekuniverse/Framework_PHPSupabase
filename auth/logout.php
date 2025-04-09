<?php
/**
 * Script de cierre de sesión
 */

// Incluir el middleware de inicialización
require_once __DIR__ . '/../middleware/init.php';

// Cerrar sesión y redirigir a la página principal
$auth->logout('/public/index.php');
