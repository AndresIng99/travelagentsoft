<?php
// debug.php - ELIMINA DESPUÉS DE USAR

echo "<h2>Debug de Rutas</h2>";

echo "<h3>Variables del Servidor:</h3>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
echo "<br>Path original: " . $path . "<br>";

// Simular el procesamiento del index.php
$path = str_replace('/travel_agency', '', $path);
echo "Path después de remover /travel_agency: " . $path . "<br>";

// Cargar .env para obtener APP_URL
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $_ENV[$name] = $value;
        }
    }
}

$appUrl = $_ENV['APP_URL'] ?? 'NO_DEFINIDO';
echo "APP_URL: " . $appUrl . "<br>";

$parsedAppUrl = parse_url($appUrl, PHP_URL_PATH);
echo "APP_URL path: " . ($parsedAppUrl ?: '/') . "<br>";

$path = str_replace(rtrim($parsedAppUrl ?: '', '/'), '', $path);
echo "Path después de remover APP_URL path: " . $path . "<br>";

$path = $path ?: '/';
echo "<strong>Path final: " . $path . "</strong><br>";

echo "<br><h3>¿Qué ruta debería cargar?</h3>";
switch($path) {
    case '/':
    case '/login':
        echo "✅ Debería cargar: pages/login.php";
        break;
    case '/dashboard':
        echo "✅ Debería cargar: pages/dashboard.php";
        break;
    default:
        echo "❌ Ruta no reconocida - debería cargar: pages/404.php";
        break;
}

echo "<br><br><strong>Si el Path final es '/' entonces debería funcionar. Elimina este archivo y prueba la raíz.</strong>";
?>