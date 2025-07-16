<?php
// ====================================================================
// ARCHIVO: pages/itinerary.php - ITINERARIO COMPLETO CON ALTERNATIVAS
// ====================================================================

require_once 'config/app.php';
require_once 'config/config_functions.php';

// Obtener ID del programa
$programa_id = $_GET['id'] ?? null;

if (!$programa_id) {
    header('Location: ' . APP_URL . '/itinerarios');
    exit;
}

try {
    ConfigManager::init();
    $company_name = ConfigManager::getCompanyName();
    $config = ConfigManager::get();
} catch(Exception $e) {
    $company_name = 'Travel Agency';
    $config = [];
}

// Cargar datos completos del programa
try {
    $db = Database::getInstance();
    
    // Obtener datos básicos del programa
    $programa = $db->fetch(
        "SELECT ps.*, pp.titulo_programa, pp.foto_portada, pp.idioma_predeterminado,
                DATE_FORMAT(ps.fecha_llegada, '%d/%m/%Y') as fecha_llegada_formatted,
                DATE_FORMAT(ps.fecha_salida, '%d/%m/%Y') as fecha_salida_formatted,
                DATEDIFF(ps.fecha_salida, ps.fecha_llegada) as duracion_dias
         FROM programa_solicitudes ps 
         LEFT JOIN programa_personalizacion pp ON ps.id = pp.solicitud_id 
         WHERE ps.id = ?", 
        [$programa_id]
    );
    
    if (!$programa) {
        throw new Exception('Programa no encontrado');
    }
    
    // Obtener días del programa
    $dias = $db->fetchAll(
        "SELECT * FROM programa_dias WHERE solicitud_id = ? ORDER BY dia_numero ASC", 
        [$programa_id]
    );
    
    // Obtener servicios para cada día con todas las alternativas
    foreach ($dias as &$dia) {
        $servicios_raw = $db->fetchAll(
            "SELECT 
                pds.*,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.nombre
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.titulo
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.nombre
                END as nombre,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.descripcion
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.descripcion
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.descripcion
                END as descripcion,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.ubicacion
                    WHEN pds.tipo_servicio = 'transporte' THEN CONCAT(COALESCE(bt.lugar_salida, ''), ' → ', COALESCE(bt.lugar_llegada, ''))
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.ubicacion
                END as ubicacion,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.imagen1
                    WHEN pds.tipo_servicio = 'transporte' THEN NULL
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.imagen
                END as imagen,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.imagen2
                    ELSE NULL
                END as imagen2,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.imagen3
                    ELSE NULL
                END as imagen3,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.latitud
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.lat_salida
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.latitud
                END as latitud,
                CASE 
                    WHEN pds.tipo_servicio = 'actividad' THEN ba.longitud
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.lng_salida
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.longitud
                END as longitud,
                CASE 
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.lat_llegada
                    ELSE NULL
                END as lat_llegada,
                CASE 
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.lng_llegada
                    ELSE NULL
                END as lng_llegada,
                CASE 
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.duracion
                    ELSE NULL
                END as duracion,
                CASE 
                    WHEN pds.tipo_servicio = 'transporte' THEN bt.medio
                    ELSE NULL
                END as medio_transporte,
                CASE 
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.tipo
                    ELSE NULL
                END as tipo_alojamiento,
                CASE 
                    WHEN pds.tipo_servicio = 'alojamiento' THEN bal.categoria
                    ELSE NULL
                END as categoria_alojamiento
            FROM programa_dias_servicios pds
            LEFT JOIN biblioteca_actividades ba ON pds.tipo_servicio = 'actividad' AND pds.biblioteca_item_id = ba.id AND ba.activo = 1
            LEFT JOIN biblioteca_transportes bt ON pds.tipo_servicio = 'transporte' AND pds.biblioteca_item_id = bt.id AND bt.activo = 1
            LEFT JOIN biblioteca_alojamientos bal ON pds.tipo_servicio = 'alojamiento' AND pds.biblioteca_item_id = bal.id AND bal.activo = 1
            WHERE pds.programa_dia_id = ?
            ORDER BY pds.orden ASC, pds.es_alternativa ASC, pds.orden_alternativa ASC", 
            [$dia['id']]
        );
        
        // Organizar servicios por orden secuencial
        $servicios_organizados = [];
        foreach ($servicios_raw as $servicio) {
            $orden = $servicio['orden'];
            
            if (!isset($servicios_organizados[$orden])) {
                $servicios_organizados[$orden] = [
                    'principal' => null,
                    'alternativas' => []
                ];
            }
            
            if ($servicio['es_alternativa'] == 0) {
                $servicios_organizados[$orden]['principal'] = $servicio;
            } else {
                $servicios_organizados[$orden]['alternativas'][] = $servicio;
            }
        }
        
        ksort($servicios_organizados);
        $dia['servicios'] = $servicios_organizados;
    }
    
    unset($dia);
    
    // Obtener información de precios
    $precios = $db->fetch(
        "SELECT * FROM programa_precios WHERE solicitud_id = ?", 
        [$programa_id]
    );
    
    // Preparar datos para el mapa
    $puntos_mapa = [];
    foreach ($dias as $dia) {
        foreach ($dia['servicios'] as $orden => $servicio_grupo) {
            $servicio = $servicio_grupo['principal'];
            if ($servicio && $servicio['latitud'] && $servicio['longitud']) {
                $puntos_mapa[] = [
                    'lat' => floatval($servicio['latitud']),
                    'lng' => floatval($servicio['longitud']),
                    'titulo' => $servicio['nombre'],
                    'descripcion' => $servicio['descripcion'],
                    'tipo' => $servicio['tipo_servicio'],
                    'dia' => $dia['dia_numero'],
                    'ubicacion' => $servicio['ubicacion'],
                    'imagen' => $servicio['imagen']
                ];
            }
            
            // Agregar punto de llegada para transportes
            if ($servicio && $servicio['tipo_servicio'] == 'transporte' && 
                $servicio['lat_llegada'] && $servicio['lng_llegada']) {
                $puntos_mapa[] = [
                    'lat' => floatval($servicio['lat_llegada']),
                    'lng' => floatval($servicio['lng_llegada']),
                    'titulo' => $servicio['nombre'] . ' (Llegada)',
                    'descripcion' => $servicio['descripcion'],
                    'tipo' => 'transporte_llegada',
                    'dia' => $dia['dia_numero'],
                    'ubicacion' => $servicio['ubicacion']
                ];
            }
        }
    }
    
} catch(Exception $e) {
    error_log("Error cargando programa: " . $e->getMessage());
    header('Location: ' . APP_URL . '/itinerarios');
    exit;
}

// Funciones helper
function getServiceIcon($tipo) {
    switch($tipo) {
        case 'actividad': return 'fas fa-hiking';
        case 'transporte': return 'fas fa-plane';
        case 'alojamiento': return 'fas fa-bed';
        default: return 'fas fa-map-marker-alt';
    }
}

function formatTransportMedium($medio) {
    $medios = [
        'avion' => 'Avión',
        'bus' => 'Bus',
        'coche' => 'Coche',
        'barco' => 'Barco',
        'tren' => 'Tren'
    ];
    return $medios[$medio] ?? ucfirst($medio);
}

function formatAccommodationType($tipo) {
    $tipos = [
        'hotel' => 'Hotel',
        'camping' => 'Camping',
        'casa_huespedes' => 'Casa de Huéspedes',
        'crucero' => 'Crucero',
        'lodge' => 'Lodge',
        'atipico' => 'Alojamiento Atípico',
        'campamento' => 'Campamento',
        'camping_car' => 'Camping Car',
        'tren' => 'Tren Hotel'
    ];
    return $tipos[$tipo] ?? ucfirst($tipo);
}

// Datos para el template
$titulo_programa = $programa['titulo_programa'] ?: 'Viaje a ' . $programa['destino'];
$nombre_viajero = trim($programa['nombre_viajero'] . ' ' . $programa['apellido_viajero']);
$imagen_portada = $programa['foto_portada'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1200&h=600&fit=crop';
$num_dias = count($dias);
$num_pasajeros = $programa['numero_pasajeros'];

// Calcular duración
$duracion_dias = 'N/A';
if ($programa['fecha_llegada'] && $programa['fecha_salida']) {
    $fecha_inicio = new DateTime($programa['fecha_llegada']);
    $fecha_fin = new DateTime($programa['fecha_salida']);
    $diferencia = $fecha_inicio->diff($fecha_fin);
    $duracion_dias = $diferencia->days + 1; // +1 porque si llegas un día y sales el siguiente, son 2 días
}

$fecha_inicio_formatted = $programa['fecha_llegada'] ? 
    date('d M Y', strtotime($programa['fecha_llegada'])) : '';
$fecha_fin_formatted = $programa['fecha_salida'] ? 
    date('d M Y', strtotime($programa['fecha_salida'])) : '';
?>

<!DOCTYPE html>
<html lang="<?= $config['default_language'] ?? 'es' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo_programa) ?> - <?= $company_name ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: #fafbfc;
        }
        
        /* ========================================
           HERO SECTION
           ======================================== */
        .hero-section {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('<?= addslashes($imagen_portada) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 0 20px;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 15px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
        }
        
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-description {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .hero-stat {
            text-align: center;
            background: rgba(255,255,255,0.15);
            padding: 20px 25px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            min-width: 120px;
        }
        
        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        
        .hero-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }
        
        .scroll-indicator i {
            font-size: 2rem;
            opacity: 0.8;
        }
        
        /* ========================================
           NAVIGATION BAR
           ======================================== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            padding: 15px 0;
            z-index: 1000;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .navbar.visible {
            transform: translateY(0);
        }
        
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            gap: 30px;
            list-style: none;
        }
        
        .navbar-nav a {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .navbar-nav a:hover {
            color: #3498db;
        }
        
        /* ========================================
           MAIN CONTENT SECTIONS
           ======================================== */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px;
        }
        
        .section {
            margin-bottom: 100px;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* ========================================
           OVERVIEW SECTION
           ======================================== */
        .overview-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .overview-content {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .overview-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .detail-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .detail-info h4 {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .detail-info p {
            color: #7f8c8d;
            margin: 0;
        }
        
        .overview-summary {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 15px;
            border-left: 5px solid #3498db;
        }
        
        .overview-summary h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .overview-summary p {
            color: #5a6c7d;
            line-height: 1.8;
        }
        
        /* ========================================
           MAP SECTION
           ======================================== */
        .map-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            height: 500px;
        }
        
        #map {
            height: 100%;
            width: 100%;
        }
        
        .map-legend {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        
        .legend-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }
        
        .legend-icon.actividad { background: #e74c3c; }
        .legend-icon.alojamiento { background: #f39c12; }
        .legend-icon.transporte { background: #3498db; }
        
        /* ========================================
           ITINERARY SECTION
           ======================================== */
        .itinerary-timeline {
            position: relative;
        }
        
        .itinerary-timeline::before {
            content: '';
            position: absolute;
            left: 50px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #3498db, #2980b9);
            border-radius: 2px;
        }
        
        .day-card {
            position: relative;
            margin-bottom: 60px;
            padding-left: 120px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .day-number {
            position: absolute;
            left: 0;
            top: 20px;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }
        
        .day-content {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .day-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }
        
        .day-header {
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }
        
        .day-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .day-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .day-images {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 2px;
            height: 300px;
        }
        
        .day-image {
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        
        .day-image:first-child {
            grid-row: span 2;
        }
        
        .day-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .day-image:hover::before {
            opacity: 1;
        }
        
        .day-services {
            padding: 30px;
        }
        
        .services-grid {
            display: grid;
            gap: 20px;
        }
        
        /* ========================================
           SERVICIOS CON ALTERNATIVAS
           ======================================== */
        .service-group {
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            transition: all 0.3s ease;
        }
        
        .service-group:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-color: #3498db;
        }
        
        .service-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .service-item.principal {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-left: 4px solid #3498db;
            position: relative;
        }
        
        .service-item.principal::before {
            content: '';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 20px;
            height: 20px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .service-item:hover {
            transform: translateX(5px);
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .service-icon.actividad {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .service-icon.transporte {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        
        .service-icon.alojamiento {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .service-details {
            flex: 1;
        }
        
        .service-details h4 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .service-details p {
            color: #7f8c8d;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
        
        .service-meta {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        
        .service-meta span {
            font-size: 0.85rem;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Alternativas */
        .alternatives-header {
            padding: 12px 20px;
            background: #f1f3f4;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #5a6c7d;
            font-size: 0.9rem;
            border-top: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
        }
        
        .alternatives-header:hover {
            background: #e8eaed;
        }
        
        .alternatives-header i {
            color: #17a2b8;
        }
        
        .alternatives-toggle {
            margin-left: auto;
            transition: transform 0.3s ease;
        }
        
        .alternatives-toggle.rotated {
            transform: rotate(180deg);
        }
        
        .alternatives-list {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .alternatives-list.expanded {
            max-height: 1000px;
        }
        
        .service-item.alternativa {
            background: #fafbfc;
            border-bottom: 1px solid #e9ecef;
            border-left: 3px solid #17a2b8;
            position: relative;
        }
        
        .service-item.alternativa:last-child {
            border-bottom: none;
        }
        
        .service-item.alternativa .service-icon {
            background: linear-gradient(135deg, #17a2b8, #20c997) !important;
            width: 50px;
            height: 50px;
            font-size: 1.1rem;
        }
        
        .alternative-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #17a2b8;
            color: white;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .alternative-notes {
            margin-top: 8px;
            padding: 8px 12px;
            background: rgba(23, 162, 184, 0.1);
            border-left: 3px solid #17a2b8;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #0c5460;
            font-style: italic;
        }
        
        /* ========================================
           PRICING SECTION
           ======================================== */
        .pricing-section {
            background: #f8f9fa;
            padding: 80px 0;
            margin: 100px 0;
            border-radius: 30px;
        }

        .pricing-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .pricing-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .pricing-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .pricing-header p {
            font-size: 1.1rem;
            color: #7f8c8d;
        }

        .price-main-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            text-align: center;
        }

        .price-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .price-amount {
            display: flex;
            align-items: baseline;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .price-currency {
            font-size: 1.5rem;
            font-weight: 600;
            color: #7f8c8d;
        }

        .price-value {
            font-size: 3.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .price-per {
            font-size: 1.2rem;
            color: #7f8c8d;
        }

        .nights-included {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 50px;
            font-weight: 600;
        }

        .pricing-accordions {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 40px;
        }

        .pricing-accordion {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .pricing-accordion:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .accordion-header {
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafbfc;
            transition: background-color 0.3s ease;
        }

        .accordion-header:hover {
            background: #f1f3f4;
        }

        .accordion-header.active {
            background: #e8f4f8;
        }

        .accordion-title {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .accordion-title i {
            font-size: 1.3rem;
        }

        .accordion-arrow {
            color: #7f8c8d;
            transition: transform 0.3s ease;
        }

        .accordion-arrow.rotated {
            transform: rotate(180deg);
        }

        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background: white;
        }

        .accordion-content.active {
            max-height: 1000px;
            padding: 0 25px 25px 25px;
        }

        .pricing-list {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }

        .pricing-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .pricing-list li:last-child {
            border-bottom: none;
        }

        .pricing-list.included i {
            color: #27ae60;
            margin-top: 2px;
        }

        .pricing-list.excluded i {
            color: #e74c3c;
            margin-top: 2px;
        }

        .pricing-list span {
            color: #2c3e50;
            line-height: 1.5;
        }

        .conditions-text,
        .passport-info,
        .insurance-info,
        .additional-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            line-height: 1.6;
            color: #5a6c7d;
            border-left: 4px solid #3498db;
        }

        .accessibility-info {
            margin-top: 15px;
        }

        .accessibility-status {
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge.fully-accessible {
            background: #d5f4e6;
            color: #27ae60;
        }

        .status-badge.partially-accessible {
            background: #fef9e7;
            color: #f39c12;
        }

        .status-badge.not-accessible {
            background: #fdf2f2;
            color: #e74c3c;
        }

        .accessibility-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            color: #5a6c7d;
            line-height: 1.6;
        }

        .pricing-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* ========================================
           FOOTER
           ======================================== */
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 60px 20px 30px;
        }
        
        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .footer h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .footer p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .btn {
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: #2c3e50;
        }
        
        .footer-bottom {
            border-top: 1px solid #34495e;
            padding-top: 20px;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        /* ========================================
           ANIMATIONS
           ======================================== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0) translateX(-50%);
            }
            40% {
                transform: translateY(-10px) translateX(-50%);
            }
            60% {
                transform: translateY(-5px) translateX(-50%);
            }
        }
        
        /* ========================================
           RESPONSIVE DESIGN
           ======================================== */
        @media (max-width: 1024px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
            
            .day-card {
                padding-left: 80px;
            }
            
            .day-number {
                width: 60px;
                height: 60px;
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                gap: 20px;
            }
            
            .hero-stat {
                min-width: 100px;
                padding: 15px 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .overview-details {
                grid-template-columns: 1fr;
            }
            
            .day-images {
                grid-template-columns: 1fr;
                height: 200px;
            }
            
            .day-image:first-child {
                grid-row: span 1;
            }
            
            .itinerary-timeline::before {
                left: 30px;
            }
            
            .day-card {
                padding-left: 70px;
            }
            
            .day-number {
                left: 0;
                width: 50px;
                height: 50px;
                font-size: 1rem;
            }
            
            .navbar-nav {
                display: none;
            }
            
            .pricing-content {
                padding: 0 15px;
            }
            
            .price-main-card {
                padding: 25px 20px;
            }
            
            .price-value {
                font-size: 2.5rem;
            }
            
            .accordion-header {
                padding: 15px 20px;
            }
            
            .accordion-title {
                font-size: 1rem;
            }
            
            .accordion-content.active {
                padding: 0 20px 20px 20px;
            }
            
            .pricing-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .footer-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .service-item {
                flex-direction: column;
                gap: 15px;
            }
            
            .service-icon {
                width: 50px;
                height: 50px;
                font-size: 1.1rem;
            }
            
            .service-meta {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-content">
            <a href="#" class="navbar-brand"><?= htmlspecialchars($company_name) ?></a>
            <ul class="navbar-nav">
                <li><a href="#overview">Resumen</a></li>
                <li><a href="#map">Mapa</a></li>
                <li><a href="#itinerary">Itinerario</a></li>
                <li><a href="#pricing">Precios</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-subtitle">Tu aventura perfecta</div>
            <h1 class="hero-title"><?= htmlspecialchars($titulo_programa) ?></h1>
            <div class="hero-description">
                Diseñado especialmente para <strong><?= htmlspecialchars($nombre_viajero) ?></strong>
            </div>
            
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= $num_dias ?></span>
                    <span class="hero-stat-label"><?= $num_dias == 1 ? 'Día' : 'Días' ?></span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= $num_pasajeros ?></span>
                    <span class="hero-stat-label"><?= $num_pasajeros == 1 ? 'Viajero' : 'Viajeros' ?></span>
                </div>
                <?php if ($fecha_inicio_formatted): ?>
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= date('j', strtotime($programa['fecha_llegada'])) ?></span>
                    <span class="hero-stat-label"><?= date('M Y', strtotime($programa['fecha_llegada'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Overview Section -->
        <section id="overview" class="section">
            <div class="section-header">
                <h2 class="section-title">Resumen del Viaje</h2>
                <p class="section-subtitle">
                    Todo lo que necesitas saber sobre tu próxima aventura
                </p>
            </div>
            
            <div class="overview-grid">
                <div class="overview-content">
                    <div class="overview-details">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="detail-info">
                                <h4>Destino</h4>
                                <p><?= htmlspecialchars($programa['destino']) ?></p>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="detail-info">
                                <h4>Fechas</h4>
                                <p><?= $fecha_inicio_formatted ?> - <?= $fecha_fin_formatted ?></p>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="detail-info">
                                <h4>Viajeros</h4>
                                <p><?= $num_pasajeros ?> <?= $num_pasajeros == 1 ? 'persona' : 'personas' ?></p>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="detail-info">
                                <h4>Duración</h4>
                                <p><?= $duracion_dias ?> <?= $duracion_dias == 1 ? 'día' : 'días' ?> increíbles</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overview-summary">
                        <h3>Sobre este viaje</h3>
                        <p>
                            Un itinerario cuidadosamente diseñado que combina los mejores destinos, 
                            experiencias únicas y servicios de calidad. Cada día está pensado para 
                            ofrecerte momentos inolvidables y la comodidad que mereces.
                        </p>
                    </div>
                </div>
                
                <div class="overview-content">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">Lo que incluye</h3>
                    <div style="space-y: 15px;">
                        <?php 
                        $total_actividades = 0;
                        $total_alojamientos = 0;
                        $total_transportes = 0;
                        
                        foreach ($dias as $dia) {
                            foreach ($dia['servicios'] as $servicio_grupo) {
                                $servicio = $servicio_grupo['principal'];
                                if ($servicio) {
                                    switch($servicio['tipo_servicio']) {
                                        case 'actividad': $total_actividades++; break;
                                        case 'alojamiento': $total_alojamientos++; break;
                                        case 'transporte': $total_transportes++; break;
                                    }
                                }
                            }
                        }
                        ?>
                        
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="detail-info">
                                <h4>Duración</h4>
                                <p><?= $num_dias ?> <?= $num_dias == 1 ? 'día' : 'días' ?> increíbles</p>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div class="detail-info">
                                <h4><?= $total_alojamientos ?> Alojamientos</h4>
                                <p>Hospedaje confortable y bien ubicado</p>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="detail-info">
                                <h4><?= $total_transportes ?> Transportes</h4>
                                <p>Traslados cómodos y seguros</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <?php if (!empty($puntos_mapa)): ?>
        <section id="map" class="section">
            <div class="section-header">
                <h2 class="section-title">Mapa del Viaje</h2>
                <p class="section-subtitle">
                    Explora todos los lugares que visitarás durante tu aventura
                </p>
            </div>
            
            <div class="map-container">
                <div id="map"></div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Itinerary Section -->
        <section id="itinerary" class="section">
            <div class="section-header">
                <h2 class="section-title">Itinerario Día a Día</h2>
                <p class="section-subtitle">
                    Un recorrido detallado de cada momento de tu viaje
                </p>
            </div>
            
            <div class="itinerary-timeline">
                <?php foreach ($dias as $index => $dia): ?>
                <div class="day-card" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <div class="day-number">
                        <?= $dia['dia_numero'] ?>
                    </div>
                    
                    <div class="day-content">
                        <div class="day-header">
                            <h3 class="day-title"><?= htmlspecialchars($dia['titulo']) ?></h3>
                            <div class="day-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($dia['ubicacion']) ?>
                            </div>
                        </div>
                        
                        <?php if ($dia['imagen1'] || $dia['imagen2'] || $dia['imagen3']): ?>
                        <div class="day-images">
                            <?php if ($dia['imagen1']): ?>
                            <div class="day-image" style="background-image: url('<?= htmlspecialchars($dia['imagen1']) ?>')"></div>
                            <?php endif; ?>
                            <?php if ($dia['imagen2']): ?>
                            <div class="day-image" style="background-image: url('<?= htmlspecialchars($dia['imagen2']) ?>')"></div>
                            <?php endif; ?>
                            <?php if ($dia['imagen3']): ?>
                            <div class="day-image" style="background-image: url('<?= htmlspecialchars($dia['imagen3']) ?>')"></div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="day-services">
                            <?php if (!empty($dia['descripcion'])): ?>
                            <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 15px; border-left: 4px solid #3498db;">
                                <p style="margin: 0; color: #5a6c7d; line-height: 1.7;">
                                    <?= nl2br(htmlspecialchars($dia['descripcion'])) ?>
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($dia['servicios'])): ?>
                            <h4 style="margin-bottom: 20px; color: #2c3e50; font-size: 1.3rem;">
                                <i class="fas fa-list-ul"></i> Servicios del día
                            </h4>
                            
                            <div class="services-grid">
                                <?php foreach ($dia['servicios'] as $servicio_grupo): ?>
                                    <?php $servicio = $servicio_grupo['principal']; ?>
                                    <?php if ($servicio): ?>
                                    <div class="service-group">
                                        <!-- Servicio Principal -->
                                        <div class="service-item principal">
                                            <div class="service-icon <?= $servicio['tipo_servicio'] ?>">
                                                <i class="<?= getServiceIcon($servicio['tipo_servicio']) ?>"></i>
                                            </div>
                                            
                                            <div class="service-details">
                                                <h4>
                                                    <i class="fas fa-star" style="color: #ffc107; font-size: 12px; margin-right: 4px;"></i>
                                                    <?= htmlspecialchars($servicio['nombre']) ?>
                                                </h4>
                                                
                                                <?php if ($servicio['descripcion']): ?>
                                                <p><?= htmlspecialchars($servicio['descripcion']) ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="service-meta">
                                                    <?php if ($servicio['ubicacion']): ?>
                                                    <span>
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <?= htmlspecialchars($servicio['ubicacion']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($servicio['tipo_servicio'] == 'transporte' && $servicio['duracion']): ?>
                                                    <span>
                                                        <i class="fas fa-clock"></i>
                                                        <?= htmlspecialchars($servicio['duracion']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($servicio['tipo_servicio'] == 'transporte' && $servicio['medio_transporte']): ?>
                                                    <span>
                                                        <i class="fas fa-plane"></i>
                                                        <?= formatTransportMedium($servicio['medio_transporte']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($servicio['tipo_servicio'] == 'alojamiento' && $servicio['categoria_alojamiento']): ?>
                                                    <span>
                                                        <i class="fas fa-star"></i>
                                                        <?= $servicio['categoria_alojamiento'] ?> estrellas
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <?php if ($servicio['imagen']): ?>
                                            <div style="width: 80px; height: 80px; border-radius: 10px; background-image: url('<?= htmlspecialchars($servicio['imagen']) ?>'); background-size: cover; background-position: center; flex-shrink: 0;"></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Alternativas -->
                                        <?php if (!empty($servicio_grupo['alternativas'])): ?>
                                        <div class="alternatives-header" onclick="toggleAlternatives(<?= $servicio['id'] ?>)">
                                            <i class="fas fa-sync-alt"></i>
                                            <span><?= count($servicio_grupo['alternativas']) ?> alternativa<?= count($servicio_grupo['alternativas']) > 1 ? 's' : '' ?> disponible<?= count($servicio_grupo['alternativas']) > 1 ? 's' : '' ?></span>
                                            <i class="fas fa-chevron-down alternatives-toggle" id="toggle-<?= $servicio['id'] ?>"></i>
                                        </div>
                                        
                                        <div class="alternatives-list" id="alternatives-<?= $servicio['id'] ?>">
                                            <?php foreach ($servicio_grupo['alternativas'] as $alternativa): ?>
                                            <div class="service-item alternativa">
                                                <div class="alternative-badge">Alt <?= $alternativa['orden_alternativa'] ?></div>
                                                
                                                <div class="service-icon">
                                                    <i class="<?= getServiceIcon($alternativa['tipo_servicio']) ?>"></i>
                                                </div>
                                                
                                                <div class="service-details">
                                                    <h4 style="color: #0c5460; margin-bottom: 5px;">
                                                        <?= htmlspecialchars($alternativa['nombre']) ?>
                                                    </h4>
                                                    
                                                    <?php if ($alternativa['descripcion']): ?>
                                                    <p style="font-size: 0.9rem; color: #5a6c7d;">
                                                        <?= htmlspecialchars($alternativa['descripcion']) ?>
                                                    </p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($alternativa['notas_alternativa']): ?>
                                                    <div class="alternative-notes">
                                                        <i class="fas fa-sticky-note"></i>
                                                        <?= htmlspecialchars($alternativa['notas_alternativa']) ?>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="service-meta" style="margin-top: 8px;">
                                                        <?php if ($alternativa['ubicacion']): ?>
                                                        <span style="font-size: 0.8rem;">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                            <?= htmlspecialchars($alternativa['ubicacion']) ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <?php if ($alternativa['imagen']): ?>
                                                <div style="width: 60px; height: 60px; border-radius: 8px; background-image: url('<?= htmlspecialchars($alternativa['imagen']) ?>'); background-size: cover; background-position: center; flex-shrink: 0;"></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #7f8c8d;">
                                <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 15px;"></i>
                                <p>Los servicios para este día están siendo planificados</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Pricing Section -->
        <?php if ($precios): ?>
        <section id="pricing" class="pricing-section">
            <div class="pricing-content">
                <div class="pricing-header">
                    <h2>Información de Precios</h2>
                    <p>Todos los detalles sobre la inversión de tu viaje</p>
                </div>
                
                <!-- Precio Principal -->
                <div class="price-main-card">
                    <div class="price-display">
                        <div class="price-amount">
                            <span class="price-currency"><?= htmlspecialchars($precios['moneda']) ?></span>
                            <?php if ($precios['precio_por_persona']): ?>
                            <span class="price-value"><?= number_format($precios['precio_por_persona'], 0, ',', '.') ?></span>
                            <span class="price-per">por persona</span>
                            <?php elseif ($precios['precio_total']): ?>
                            <span class="price-value"><?= number_format($precios['precio_total'], 0, ',', '.') ?></span>
                            <span class="price-per">precio total</span>
                            <?php else: ?>
                            <span class="price-value">Consultar</span>
                            <span class="price-per">precio</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($precios['noches_incluidas'] > 0): ?>
                        <div class="nights-included">
                            <i class="fas fa-bed"></i>
                            <?= $precios['noches_incluidas'] ?> <?= $precios['noches_incluidas'] == 1 ? 'noche' : 'noches' ?> incluidas
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Desplegables de Información -->
                <div class="pricing-accordions">
                    
                    <!-- ¿Qué incluye? -->
                    <?php if ($precios['precio_incluye']): ?>
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('incluye')">
                            <div class="accordion-title">
                                <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                                <span>¿Qué incluye el precio?</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-incluye"></i>
                        </div>
                        <div class="accordion-content" id="content-incluye">
                            <ul class="pricing-list included">
                                <?php foreach (explode("\n", $precios['precio_incluye']) as $item): ?>
                                <?php if (trim($item)): ?>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span><?= htmlspecialchars(trim($item)) ?></span>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- ¿Qué NO incluye? -->
                    <?php if ($precios['precio_no_incluye']): ?>
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('no-incluye')">
                            <div class="accordion-title">
                                <i class="fas fa-times-circle" style="color: #e74c3c;"></i>
                                <span>¿Qué NO incluye?</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-no-incluye"></i>
                        </div>
                        <div class="accordion-content" id="content-no-incluye">
                            <ul class="pricing-list excluded">
                                <?php foreach (explode("\n", $precios['precio_no_incluye']) as $item): ?>
                                <?php if (trim($item)): ?>
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span><?= htmlspecialchars(trim($item)) ?></span>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Condiciones Generales -->
                    <?php if ($precios['condiciones_generales']): ?>
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('condiciones')">
                            <div class="accordion-title">
                                <i class="fas fa-file-contract" style="color: #3498db;"></i>
                                <span>Condiciones Generales</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-condiciones"></i>
                        </div>
                        <div class="accordion-content" id="content-condiciones">
                            <div class="conditions-text">
                                <?= nl2br(htmlspecialchars($precios['condiciones_generales'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Información de Pasaporte -->
                    <?php if ($precios['info_pasaporte']): ?>
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('pasaporte')">
                            <div class="accordion-title">
                                <i class="fas fa-passport" style="color: #8e44ad;"></i>
                                <span>Requisitos de Pasaporte y Documentación</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-pasaporte"></i>
                        </div>
                        <div class="accordion-content" id="content-pasaporte">
                            <div class="passport-info">
                                <?= nl2br(htmlspecialchars($precios['info_pasaporte'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Información de Seguros -->
                    <?php if ($precios['info_seguros']): ?>
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('seguros')">
                            <div class="accordion-title">
                                <i class="fas fa-shield-alt" style="color: #16a085;"></i>
                                <span>Información de Seguros</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-seguros"></i>
                        </div>
                        <div class="accordion-content" id="content-seguros">
                            <div class="insurance-info">
                                <?= nl2br(htmlspecialchars($precios['info_seguros'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Accesibilidad -->
                    <div class="pricing-accordion">
                        <div class="accordion-header" onclick="toggleAccordion('accesibilidad')">
                            <div class="accordion-title">
                                <i class="fas fa-universal-access" style="color: #f39c12;"></i>
                                <span>Accesibilidad para Personas con Movilidad Reducida</span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow" id="arrow-accesibilidad"></i>
                        </div>
                        <div class="accordion-content" id="content-accesibilidad">
                            <div class="accessibility-info">
                                <div class="accessibility-status">
                                    <div class="status-badge <?= $precios['movilidad_reducida'] ? 'fully-accessible' : 'not-accessible' ?>">
                                        <i class="fas fa-<?= $precios['movilidad_reducida'] ? 'check' : 'times' ?>"></i>
                                        <span>
                                            <?= $precios['movilidad_reducida'] 
                                                ? 'Viaje adaptado para movilidad reducida' 
                                                : 'Este viaje no está adaptado para movilidad reducida' ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="accessibility-details">
                                    <?php if ($precios['movilidad_reducida']): ?>
                                    <p><strong>Servicios incluidos para movilidad reducida:</strong></p>
                                    <ul>
                                        <li>Alojamientos con acceso para sillas de ruedas</li>
                                        <li>Transporte adaptado cuando sea necesario</li>
                                        <li>Actividades modificadas según necesidades</li>
                                        <li>Acompañamiento especializado si se requiere</li>
                                    </ul>
                                    <p><em>Recomendamos contactar con nuestro equipo para personalizar los servicios según necesidades específicas.</em></p>
                                    <?php else: ?>
                                    <p>Este itinerario incluye actividades y ubicaciones que pueden no ser accesibles para personas con movilidad reducida:</p>
                                    <ul>
                                        <li>Caminatas en terrenos irregulares</li>
                                        <li>Escalones y accesos sin rampa</li>
                                        <li>Transportes no adaptados</li>
                                        <li>Sitios históricos con limitaciones arquitectónicas</li>
                                    </ul>
                                    <p><em>Si necesitas adaptaciones, contacta con nuestro equipo para evaluar alternativas viables.</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>

    

    <!-- JavaScript para funcionalidad -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // =====================================================
        // NAVBAR SCROLL FUNCTIONALITY
        // =====================================================
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('visible');
            } else {
                navbar.classList.remove('visible');
            }
        });

        // =====================================================
        // SMOOTH SCROLLING FOR NAVIGATION
        // =====================================================
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // =====================================================
        // MAP INITIALIZATION
        // =====================================================
        <?php if (!empty($puntos_mapa)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const puntosMapa = <?= json_encode($puntos_mapa) ?>;
            
            if (puntosMapa.length > 0) {
                let centerLat = puntosMapa.reduce((sum, loc) => sum + loc.lat, 0) / puntosMapa.length;
                let centerLng = puntosMapa.reduce((sum, loc) => sum + loc.lng, 0) / puntosMapa.length;
                
                const map = L.map('map').setView([centerLat, centerLng], 8);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                const iconColors = {
                    'actividad': '#e74c3c',
                    'alojamiento': '#f39c12',
                    'transporte': '#3498db',
                    'transporte_llegada': '#9b59b6'
                };
                
                puntosMapa.forEach(function(punto, index) {
                    const color = iconColors[punto.tipo] || '#95a5a6';
                    
                    const customIcon = L.divIcon({
                        html: `
                            <div style="
                                background-color: ${color};
                                width: 35px;
                                height: 35px;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: white;
                                font-weight: bold;
                                font-size: 14px;
                                border: 3px solid white;
                                box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                            ">${punto.dia}</div>
                        `,
                        className: 'custom-div-icon',
                        iconSize: [35, 35],
                        iconAnchor: [17, 17]
                    });
                    
                    const marker = L.marker([punto.lat, punto.lng], {
                        icon: customIcon
                    }).addTo(map);
                    
                    const popupContent = `
                        <div style="text-align: center; min-width: 220px;">
                            <h4 style="margin: 0 0 10px 0; color: ${color}; font-size: 1.1rem;">
                                ${punto.titulo}
                            </h4>
                            <p style="margin: 0 0 8px 0; color: #666; text-transform: capitalize; font-size: 0.9rem;">
                                <i class="fas fa-${punto.tipo === 'actividad' ? 'hiking' : (punto.tipo === 'alojamiento' ? 'bed' : 'car')}"></i>
                                ${punto.tipo} - Día ${punto.dia}
                            </p>
                            <p style="margin: 0 0 10px 0; color: #888; font-size: 0.85rem;">
                                <i class="fas fa-map-marker-alt"></i>
                                ${punto.ubicacion}
                            </p>
                            ${punto.descripcion ? `
                                <p style="margin: 10px 0 0 0; color: #555; font-size: 0.8rem; line-height: 1.3;">
                                    ${punto.descripcion.substring(0, 80)}...
                                </p>
                            ` : ''}
                            ${punto.imagen ? `
                                <img src="${punto.imagen}" 
                                     style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px;"
                                     alt="${punto.titulo}">
                            ` : ''}
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                });
                
                if (puntosMapa.length > 1) {
                    const group = new L.featureGroup(map._layers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
                
                // Conexiones entre puntos del mismo día
                const puntosPerDia = {};
                puntosMapa.forEach(punto => {
                    if (!puntosPerDia[punto.dia]) {
                        puntosPerDia[punto.dia] = [];
                    }
                    puntosPerDia[punto.dia].push(punto);
                });
                
                const colores = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
                
                Object.keys(puntosPerDia).forEach((dia, index) => {
                    const puntosDia = puntosPerDia[dia];
                    if (puntosDia.length > 1) {
                        const coordenadas = puntosDia.map(p => [p.lat, p.lng]);
                        const color = colores[index % colores.length];
                        
                        L.polyline(coordenadas, {
                            color: color,
                            weight: 3,
                            opacity: 0.7,
                            dashArray: '8, 5'
                        }).addTo(map);
                    }
                });
            }
        });
        <?php endif; ?>

        // =====================================================
        // ACCORDION FUNCTIONALITY FOR PRICING SECTION
        // =====================================================
        function toggleAccordion(sectionId) {
            const content = document.getElementById(`content-${sectionId}`);
            const arrow = document.getElementById(`arrow-${sectionId}`);
            const header = arrow.closest('.accordion-header');
            
            document.querySelectorAll('.accordion-content.active').forEach(function(otherContent) {
                if (otherContent.id !== `content-${sectionId}`) {
                    otherContent.classList.remove('active');
                    const otherId = otherContent.id.replace('content-', '');
                    const otherArrow = document.getElementById(`arrow-${otherId}`);
                    const otherHeader = otherArrow.closest('.accordion-header');
                    otherArrow.classList.remove('rotated');
                    otherHeader.classList.remove('active');
                }
            });
            
            content.classList.toggle('active');
            arrow.classList.toggle('rotated');
            header.classList.toggle('active');
        }

        // =====================================================
        // ALTERNATIVES FUNCTIONALITY
        // =====================================================
        function toggleAlternatives(servicioId) {
            const alternativesList = document.getElementById(`alternatives-${servicioId}`);
            const toggle = document.getElementById(`toggle-${servicioId}`);
            
            alternativesList.classList.toggle('expanded');
            toggle.classList.toggle('rotated');
        }

        // =====================================================
        // ACTION BUTTONS FUNCTIONALITY
        // =====================================================
        function requestQuote() {
            alert('Funcionalidad de cotización - Implementar según necesidades');
        }

        function downloadItinerary() {
            window.print();
        }

        // =====================================================
        // ANIMATION ON SCROLL
        // =====================================================
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = '0s';
                        entry.target.classList.add('animate');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.day-card, .service-group, .detail-item').forEach(function(el) {
                observer.observe(el);
            });
        });

        // =====================================================
        // PARALLAX EFFECT - REMOVED
        // =====================================================
        // Parallax effect removed to prevent image overlap with text
        
        // =====================================================
        // SMOOTH SCROLL ENHANCEMENT
        // =====================================================
        window.addEventListener('scroll', function() {
            // Only handle navbar visibility, no parallax effects
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('visible');
            } else {
                navbar.classList.remove('visible');
            }
        });

        // =====================================================
        // PRINT FUNCTIONALITY
        // =====================================================
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('.accordion-content').forEach(function(content) {
                content.style.maxHeight = 'none';
                content.style.display = 'block';
            });
            
            document.querySelectorAll('.alternatives-list').forEach(function(list) {
                list.style.maxHeight = 'none';
                list.style.display = 'block';
            });
        });

        window.addEventListener('afterprint', function() {
            document.querySelectorAll('.accordion-content:not(.active)').forEach(function(content) {
                content.style.maxHeight = '0';
                content.style.display = 'none';
            });
            
            document.querySelectorAll('.alternatives-list:not(.expanded)').forEach(function(list) {
                list.style.maxHeight = '0';
                list.style.display = 'none';
            });
        });
    </script>

    <!-- Estilos adicionales para impresión -->
    <style media="print">
        .navbar, .scroll-indicator, .pricing-actions, .footer-actions {
            display: none !important;
        }
        
        .hero-section {
            height: 300px !important;
            background-attachment: scroll !important;
        }
        
        .day-card {
            page-break-inside: avoid;
            margin-bottom: 30px !important;
        }
        
        .pricing-section {
            page-break-before: always;
        }
        
        .accordion-content {
            max-height: none !important;
            padding: 0 25px 25px 25px !important;
        }
        
        .alternatives-list {
            max-height: none !important;
        }
        
        body {
            font-size: 12px !important;
        }
        
        .section-title {
            font-size: 1.8rem !important;
        }
        
        .day-title {
            font-size: 1.5rem !important;
        }
        
        .map-container {
            display: none !important;
        }
    </style>

</body>
</html>