<?php
// =====================================
// ARCHIVO: index.php - CORREGIR LÍNEA 16
// =====================================

require_once 'config/database.php';
require_once 'config/app.php';

App::init();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// ✅ CORRECCIÓN LÍNEA 16: Verificar que parse_url no sea null
$appUrlPath = parse_url(APP_URL, PHP_URL_PATH);
$path = str_replace(rtrim($appUrlPath ?: '', '/'), '', $path);
$path = $path ?: '/';

// Limpiar path de múltiples slashes
$path = preg_replace('#/+#', '/', $path);

switch($path) {
    case '/':
    case '/login':
        if (App::isLoggedIn()) {
            App::redirect('/dashboard');
        }
        include 'pages/login.php';
        break;
        
    case '/auth/login':
        include 'auth/login.php';
        break;
        
    case '/auth/logout':
        include 'auth/logout.php';
        break;
        
    case '/dashboard':
        App::requireLogin();
        $user = App::getUser();
        
        if (isset($_GET['redirect'])) {
            if ($user['role'] === 'admin') {
                App::redirect('/administrador');
            } else {
                include 'pages/dashboard.php';
            }
        } else {
            include 'pages/dashboard.php';
        }
        break;
        
    case '/biblioteca':
        App::requireLogin();
        include 'pages/biblioteca.php';
        break;
        
    case '/biblioteca/api':
        App::requireLogin();
        include 'modules/biblioteca/api.php';
        break;
        
    case '/programa':
        App::requireLogin();
        include 'pages/programa.php';
        break;
        
    case '/programa/api':
        App::requireLogin();
        include 'modules/programa/api.php';
        break;
        
    case '/itinerarios':
        App::requireLogin();
        include 'pages/itinerarios.php';
        break;
        
    case (preg_match('/^\/itinerarios\/(\d+)$/', $path, $matches) ? true : false):
        App::requireLogin();
        $_GET['id'] = $matches[1];
        include 'pages/itinerarios.php';
        break;
        
    case '/administrador':
    case '/administrador/usuarios':
        App::requireRole('admin');
        include 'pages/admin.php';
        break;
        
    case '/administrador/configuracion':
        App::requireRole('admin');
        include 'pages/admin_config.php';
        break;

    case '/admin/api':
        App::requireRole('admin');
        include 'modules/admin/api.php';
        break;

    case '/preview':
        App::requireLogin();
        require_once 'pages/preview.php';
        break;

    case '/itinerary':
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: ' . APP_URL . '/itinerarios');
            exit;
        }
        require_once __DIR__ . '/pages/itinerary.php';
        break;
        
    case '/perfil':
        App::requireLogin();
        include 'pages/perfil.php';
        break;
        
    case '/itinerario':
    case '/mis-itinerarios':
    case '/viajes':
        App::redirect('/itinerarios');
        break;
        
    case '/mi-programa':
        App::redirect('/programa');
        break;
        
    case '/biblioteca-destinos':
    case '/destinos':
        App::redirect('/biblioteca');
        break;
        
    default:
        http_response_code(404);
        include 'pages/404.php';
        break;
}