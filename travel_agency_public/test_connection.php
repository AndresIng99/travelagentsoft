<?php
// test_connection.php - ELIMINA ESTE ARCHIVO DESPUÉS DE PROBAR

echo "<h2>Prueba de Configuración</h2>";

// 1. Verificar archivo .env
if (file_exists('.env')) {
    echo "✅ Archivo .env encontrado<br>";
    
    // Cargar .env
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
} else {
    echo "❌ Archivo .env NO encontrado<br>";
}

// 2. Verificar variables de entorno
echo "<h3>Variables de Base de Datos:</h3>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "<br>";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NO DEFINIDO') . "<br>";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NO DEFINIDO') . "<br>";
echo "DB_PASS: " . (isset($_ENV['DB_PASS']) ? '***DEFINIDO***' : 'NO DEFINIDO') . "<br>";

// 3. Probar conexión a base de datos
try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? '';
    $username = $_ENV['DB_USER'] ?? '';
    $password = $_ENV['DB_PASS'] ?? '';
    
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ <strong>Conexión a base de datos EXITOSA</strong><br>";
    
    // Verificar tabla users
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'users' encontrada<br>";
        
        // Contar usuarios
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch();
        echo "📊 Total de usuarios: " . $result['total'] . "<br>";
    } else {
        echo "❌ Tabla 'users' NO encontrada<br>";
    }
    
} catch(Exception $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
}

// 4. Verificar URL
echo "<h3>Configuración de URL:</h3>";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$currentUrl = $protocol . '://' . $host;
echo "URL detectada: " . $currentUrl . "<br>";
echo "URL en .env: " . ($_ENV['APP_URL'] ?? 'NO DEFINIDO') . "<br>";

// 5. Verificar archivos principales
echo "<h3>Archivos del Sistema:</h3>";
$files = ['index.php', 'config/app.php', 'config/database.php', 'config/config_functions.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ $file<br>";
    }
}

echo "<br><strong>Una vez que todo esté ✅, elimina este archivo y visita: <a href='/'>/</a></strong>";
?>