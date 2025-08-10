<?php 
// =====================================
// ARCHIVO: pages/biblioteca.php - Biblioteca con Componentes UI Integrados
// =====================================

App::requireLogin();

// Incluir ConfigManager y componentes UI
require_once 'config/config_functions.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/ui_components.php';

$user = App::getUser(); 

// Obtener configuración de colores según el rol del usuario
ConfigManager::init();
$userColors = ConfigManager::getColorsForRole($user['role']);
$companyName = ConfigManager::getCompanyName();
$logo = ConfigManager::getLogo();
$defaultLanguage = ConfigManager::getDefaultLanguage();
?>
<!DOCTYPE html>
<html lang="<?= $defaultLanguage ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - <?= htmlspecialchars($companyName) ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Incluir estilos de componentes -->
    <?= UIComponents::getComponentStyles() ?>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary-color: <?= $userColors['primary'] ?>;
            --secondary-color: <?= $userColors['secondary'] ?>;
            --primary-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            color: #333;
            min-height: 100vh;
        }

        /* Header con componentes */
        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            backdrop-filter: blur(10px);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }


        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .search-input {
            transition: all 0.3s ease;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }

        .filter-select:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .user-info:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Google Translate mejorado */
        /* Google Translate en la esquina */
        /* ===== MEJORAR EL SELECTOR DE GOOGLE TRANSLATE ===== */

        /* Contenedor principal */
        .translate-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        /* Caja del widget */
        #google_translate_element {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 8px 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #google_translate_element:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        /* Ocultar el icono de Google */
        .goog-te-gadget-icon {
            display: none !important;
        }

        /* Contenedor del gadget */
        .goog-te-gadget-simple {
            background: transparent !important;
            border: none !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        /* El enlace principal */
        .VIpgJd-ZVi9od-xl07Ob-lTBxed {
            background: transparent !important;
            border: none !important;
            color: #2d3748 !important;
            text-decoration: none !important;
            font-family: inherit !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .VIpgJd-ZVi9od-xl07Ob-lTBxed:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
        }

        /* El texto "Seleccionar idioma" */
        .VIpgJd-ZVi9od-xl07Ob-lTBxed span:first-child {
            color: inherit !important;
            font-weight: inherit !important;
        }

        /* Ocultar las imágenes separadoras */
        .VIpgJd-ZVi9od-xl07Ob-lTBxed img {
            display: none !important;
        }

        /* Ocultar el separador */
        .VIpgJd-ZVi9od-xl07Ob-lTBxed span[style*="border-left"] {
            display: none !important;
        }

        /* Mejorar la flecha */
        .VIpgJd-ZVi9od-xl07Ob-lTBxed span[aria-hidden="true"] {
            color: #6b7280 !important;
            font-size: 12px !important;
            margin-left: 4px !important;
            transition: all 0.2s ease !important;
        }

        .VIpgJd-ZVi9od-xl07Ob-lTBxed:hover span[aria-hidden="true"] {
            color: #667eea !important;
            transform: translateY(1px) !important;
        }

        /* Menú desplegable cuando aparece */
        .goog-te-menu-frame {
            border: none !important;
            border-radius: 10px !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15) !important;
            backdrop-filter: blur(10px) !important;
            overflow: hidden !important;
            margin-top: 4px !important;
        }

        .goog-te-menu2 {
            background: rgba(255, 255, 255, 0.98) !important;
            border: none !important;
            padding: 8px 0 !important;
        }

        /* Items de la lista */
        .goog-te-menu2-item {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #374151 !important;
            padding: 10px 16px !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
            border: none !important;
            margin: 0 6px !important;
            border-radius: 6px !important;
        }

        .goog-te-menu2-item:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
            transform: translateX(2px) !important;
        }

        .goog-te-menu2-item:active {
            transform: translateX(2px) scale(0.98) !important;
        }

        .goog-te-menu2-item-selected {
            background: #667eea !important;
            color: white !important;
            font-weight: 600 !important;
        }

        /* Ocultar banner azul */
        .goog-te-banner-frame.skiptranslate { 
            display: none !important; 
        }

        body { 
            top: 0px !important; 
        }

        /* Responsive */
        @media (max-width: 768px) {
            .translate-container {
                top: 10px;
                right: 10px;
            }
            
            #google_translate_element {
                padding: 6px 10px;
            }
            
            .VIpgJd-ZVi9od-xl07Ob-lTBxed {
                font-size: 12px !important;
                padding: 3px 6px !important;
            }
            
            .goog-te-menu2-item {
                font-size: 12px !important;
                padding: 8px 14px !important;
            }
        }

        .VIpgJd-ZVi9od-ORHb-OEVmcd {
            left: 0;
            display: none !important;
            top: 0;
        }
        
        .goog-te-gadget img {
            vertical-align: middle;
            border: none;
            display: none;
        }
        /* Main Content mejorado */
        .main-content {
            margin-left: 0;
            margin-top: 70px;
            padding: 40px;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: calc(100vh - 70px);
        }

        .main-content.sidebar-open {
            margin-left: 320px;
        }

        /* Tabs Container */
    

        .tab-btn.active {
            background: var(--primary-gradient);
            color: white;
        }

.tabs-nav {
   display: flex;
   gap: 0;
   margin-bottom: 25px;
   border-bottom: 2px solid #e2e8f0;
   padding-bottom: 0;
}

.tab-btn {
   background: none;
   border: none;
   padding: 12px 20px;
   border-radius: 0;
   cursor: pointer;
   font-size:16px;
   font-weight: 800;
   transition: all 0.3s ease;
   color: #4a5568;
   flex: 1;
   text-align: center;
   border-bottom: 3px solid transparent;
}

.tab-btn.active {
   background: var(--primary-gradient);
   color: white;
   border-bottom: 3px solid var(--primary-color);
}

.tab-btn:hover:not(.active) {
   background: #f7fafc;
}

        /* Search and Filters */
        .filters-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .filter-select {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            font-size: 14px;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .add-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: transform 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: 1px solid #e2e8f0;
        }

        .item-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .item-card {
            position: relative;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-color);
        }

        .card-image {
            width: 100%;
            height: 200px;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .card-description {
            color: #718096;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-location {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--primary-color);
            font-size: 13px;
            font-weight: 500;
        }

        .card-actions {
            padding: 15px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 8px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: none;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .action-btn.edit {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .action-btn.edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .action-btn.delete {
            color: #e53e3e;
            border-color: #e53e3e;
        }

        .action-btn.delete:hover {
            background: #e53e3e;
            color: white;
        }

        /* Modal Styles */
        /* =====================================
   MEJORAS PARA MODALS DE BIBLIOTECA
   ===================================== */

/* Modal Principal - Mejorar backdrop y animaciones */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    overflow-y: auto;
    backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 100px 20px 20px 20px;
    opacity: 1;
    animation: modalFadeIn 0.3s ease-out;
}

/* Contenido del Modal - Diseño más moderno */
.modal-content {
    background: white;
    border-radius: 24px;
    padding: 0;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.25),
        0 10px 20px rgba(0, 0, 0, 0.15);
    transform: scale(0.9) translateY(20px);
    animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Header del Modal - Más elegante */
.modal-header {
    background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
    color: white;
    padding: 10px 40px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0;
    border-bottom: none;
    position: relative;
    overflow: hidden;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="grain" width="100" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="5" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="30" cy="15" r="0.3" fill="rgba(255,255,255,0.03)"/><circle cx="70" cy="8" r="0.4" fill="rgba(255,255,255,0.04)"/><circle cx="90" cy="12" r="0.2" fill="rgba(255,255,255,0.02)"/></pattern></defs><rect width="100" height="20" fill="url(%23grain)"/></svg>');
    opacity: 0.6;
}

.modal-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    letter-spacing: -0.5px;
}

/* Botón cerrar - Más elegante */
.close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    font-weight: 300;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Contenido del formulario - Con scroll personalizado */
.modal-content form {
    padding: 40px;
    max-height: calc(90vh - 120px);
    overflow-y: auto;
}

/* Scrollbar personalizado para el modal */
.modal-content form::-webkit-scrollbar {
    width: 6px;
}

.modal-content form::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 3px;
}

.modal-content form::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2));
    border-radius: 3px;
}

.modal-content form::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, var(--secondary-color, #764ba2), var(--primary-color, #667eea));
}

/* Grid del formulario - Mejor espaciado */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

/* Grupos de formulario - Más modernos */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
}

.form-group label {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Campos de entrada - Diseño premium */
.form-group input,
.form-group select,
.form-group textarea {
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    background: #fafbfc;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color, #667eea);
    background: white;
    box-shadow: 
        0 0 0 4px rgba(102, 126, 234, 0.1),
        0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
    line-height: 1.6;
}

/* Grid de imágenes - Más atractivo */
.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* Upload de imágenes - Diseño mejorado */
.image-upload {
    border: 3px dashed #cbd5e0;
    border-radius: 16px;
    padding: 30px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
    min-height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.image-upload::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.image-upload:hover {
    border-color: var(--primary-color, #667eea);
    background: linear-gradient(135deg, #f0f4ff 0%, #e6f3ff 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.image-upload:hover::before {
    transform: translateX(100%);
}

.upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 1;
}

.upload-content > div:first-child {
    font-size: 32px;
    margin-bottom: 8px;
    transition: transform 0.3s ease;
}

.image-upload:hover .upload-content > div:first-child {
    transform: scale(1.2);
}

.upload-content > div:nth-child(2) {
    font-weight: 600;
    color: #4a5568;
    font-size: 16px;
}

.upload-content > div:last-child {
    font-size: 13px;
    color: #718096;
    font-style: italic;
}

/* Contenedor del mapa - Más elegante */
.map-container {
    height: 350px;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 30px;
    border: 3px solid #e2e8f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    position: relative;
}

.map-container::before {
    content: '🗺️ Selecciona una ubicación en el mapa';
    position: absolute;
    top: 15px;
    left: 20px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

#map {
    width: 100%;
    height: 100%;
    border-radius: 13px;
}

/* Acciones del formulario - Botones mejorados */
.form-actions {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    padding-top: 30px;
    border-top: 2px solid #f7fafc;
    margin-top: 40px;
}

.btn-secondary,
.btn-primary {
    padding: 16px 32px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.3px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    min-width: 120px;
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-secondary:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Estados de loading para botones */
.btn-primary:disabled {
    background: #a0aec0;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Animaciones personalizadas */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        backdrop-filter: blur(0px);
    }
    to {
        opacity: 1;
        backdrop-filter: blur(8px);
    }
}

@keyframes modalSlideIn {
    from {
        transform: scale(0.9) translateY(40px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

/* Responsive - Adaptaciones móviles */
@media (max-width: 768px) {
    .modal.show {
        padding: 10px;
        align-items: flex-start;
        padding-top: 40px;
    }
    
    .modal-content {
        max-width: 100%;
        max-height: 95vh;
        border-radius: 20px;
    }
    
    .modal-header {
        padding: 25px 30px 20px;
    }
    
    .modal-title {
        font-size: 24px;
    }
    
    .modal-content form {
        padding: 30px 25px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .images-grid {
        grid-template-columns: 1fr;
    }
    
    .image-upload {
        min-height: 150px;
        padding: 25px 15px;
    }
    
    .map-container {
        height: 280px;
    }
    
    .form-actions {
        flex-direction: column-reverse;
        gap: 15px;
    }
    
    .btn-secondary,
    .btn-primary {
        width: 100%;
        padding: 18px 24px;
    }
}

/* Estados de error y éxito */
.form-group.error input,
.form-group.error select,
.form-group.error textarea {
    border-color: #e53e3e;
    background: #fef5f5;
    box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.form-group.success input,
.form-group.success select,
.form-group.success textarea {
    border-color: #38a169;
    background: #f0fff4;
    box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.1);
}

/* Mensajes de estado */
.field-message {
    font-size: 13px;
    margin-top: 5px;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 500;
}

.field-message.error {
    background: #fed7d7;
    color: #c53030;
    border: 1px solid #feb2b2;
}

.field-message.success {
    background: #c6f6d5;
    color: #2f855a;
    border: 1px solid #9ae6b4;
}

/* Indicadores de carga en inputs */
.form-group.loading {
    position: relative;
}

.form-group.loading::after {
    content: '';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    border: 2px solid #e2e8f0;
    border-top: 2px solid var(--primary-color, #667eea);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}

/* Mejoras para el selector de idioma */
.form-group select {
    background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23666" d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 12px;
    appearance: none;
    padding-right: 48px;
}

/* Placeholders mejorados */
.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0aec0;
    font-style: italic;
    font-weight: 400;
}

/* Focus visible mejorado */
.form-group input:focus-visible,
.form-group select:focus-visible,
.form-group textarea:focus-visible,
.btn-secondary:focus-visible,
.btn-primary:focus-visible,
.close-btn:focus-visible {
    outline: 3px solid rgba(102, 126, 234, 0.5);
    outline-offset: 2px;
}

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Location suggestions */
        .location-suggestions {
            animation: slideDown 0.2s ease-out;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .suggestion-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
            font-size: 14px;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background-color: #f7fafc !important;
        }

        /* Loading indicator */
        .location-loading {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(5px);
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }

            .main-content {
                padding: 20px;
            }

            .main-content.sidebar-open {
                margin-left: 0;
            }

            .tabs-nav {
                flex-wrap: wrap;
            }

            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }

            .search-input {
                min-width: auto;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                margin: 10px;
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .images-grid {
                grid-template-columns: 1fr;
            }
        }
      
        .image-count {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .card-category,
        .card-type,
        .card-transport {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
            font-size: 12px;
            color: #4a5568;
        }

        .image-preview.existing {
            border-color: #10b981 !important;
        }

        .image-preview.new {
            border-color: #3b82f6 !important;
        }

        .existing-image-indicator {
            background: #10b981 !important;
        }

        .new-image-indicator {
            background: #3b82f6 !important;
        }

        /* Hover effect para cards con imágenes */
        .item-card:hover .card-image img {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .card-image {
            overflow: hidden;
        }

/* Botón flotante para Itinerarios */
.floating-itinerarios-btn {
   position: fixed;
   bottom: 30px;
   right: 30px;
   width: 60px;
   height: 60px;
   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
   border: none;
   border-radius: 50%;
   cursor: pointer;
   box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
   z-index: 1000;
   display: flex;
   align-items: center;
   justify-content: center;
   transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
   overflow: hidden;
   text-decoration: none;
   color: white;
   font-size: 24px;
   backdrop-filter: blur(10px);
   border: 2px solid rgba(255, 255, 255, 0.2);
}

.floating-itinerarios-btn::before {
   content: '';
   position: absolute;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
   transform: translateX(-100%);
   transition: transform 0.6s ease;
}

.floating-itinerarios-btn:hover::before {
   transform: translateX(100%);
}

.floating-itinerarios-btn:hover {
   width: 180px;
   border-radius: 30px;
   background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
   box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
   transform: translateY(-3px) translateX(-60px);
}

.floating-itinerarios-btn .btn-icon {
   font-size: 24px;
   transition: all 0.4s ease;
   position: relative;
   z-index: 1;
}

.floating-itinerarios-btn .btn-text {
   position: absolute;
   right: 20px;
   font-weight: 600;
   font-size: 14px;
   white-space: nowrap;
   opacity: 0;
   transform: translateX(10px);
   transition: all 0.4s ease;
   z-index: 1;
   letter-spacing: 0.5px;
}

.floating-itinerarios-btn:hover .btn-text {
   opacity: 1;
   transform: translateX(0);
}

.floating-itinerarios-btn:hover .btn-icon {
   transform: translateX(-50px) scale(1.1);
}

.floating-itinerarios-btn:active {
   transform: translateY(-1px) scale(0.95);
   box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Animación de pulso sutil */
@keyframes gentlePulse {
   0%, 100% {
       box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
   }
   50% {
       box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
   }
}

.floating-itinerarios-btn {
   animation: gentlePulse 3s ease-in-out infinite;
}

.floating-itinerarios-btn:hover {
   animation: none;
}

/* Responsive */
@media (max-width: 768px) {
   .floating-itinerarios-btn {
       bottom: 20px;
       right: 20px;
       width: 50px;
       height: 50px;
   }
   
   .floating-itinerarios-btn .btn-icon {
       font-size: 20px;
   }
   
   .floating-itinerarios-btn:hover {
       width: 150px;
       border-radius: 25px;
       transform: translateY(-3px) translateX(-50px);
   }
   
   .floating-itinerarios-btn .btn-text {
       right: 15px;
       font-size: 13px;
   }
   
   .floating-itinerarios-btn:hover .btn-icon {
       transform: translateX(-40px) scale(1.1);
   }
}

/* Toast notifications */
.toast {
    position: fixed;
    top: 90px;
    right: 20px;
    padding: 20px 25px;
    border-radius: 15px;
    color: white;
    z-index: 20000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
    min-width: 300px;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
}

.toast.error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

    </style>
</head>
<body>
    <!-- Header con componentes -->
    <?= UIComponents::renderHeader($user) ?>

    <!-- Sidebar con componentes -->
    <?= UIComponents::renderSidebar($user, '/biblioteca') ?>

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="tabs-container">
            <!-- Tabs Navigation -->
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="dias">Días</button>
                <button class="tab-btn" data-tab="alojamientos">Alojamientos</button>
                <button class="tab-btn" data-tab="actividades">Actividades</button>
                <button class="tab-btn" data-tab="transportes">Transportes</button>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <input type="text" class="search-input" placeholder="Buscar por título, descripción, ubicación..." id="searchInput">
                <select class="filter-select" id="languageFilter">
                    <option value="">Todos los idiomas</option>
                    <option value="es">Español</option>
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                    <option value="pt">Português</option>
                </select>
                <button class="add-btn" onclick="openModal('create')">➕ Agregar Nuevo</button>
            </div>

            <!-- Content Grid -->
            <div class="content-grid" id="contentGrid">
                <!-- El contenido se carga dinámicamente aquí -->
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-state-icon">📂</div>
                <h3>No hay recursos disponibles</h3>
                <p>Comienza agregando tu primer recurso haciendo clic en "Agregar Nuevo"</p>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar -->
    <div class="modal" id="resourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Agregar Nuevo Día</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form id="resourceForm">
                <input type="hidden" id="resourceId">
                <input type="hidden" id="resourceType">

                <!-- Formulario común -->
                <div class="form-grid">
                    <div class="form-group">
                        <label for="idioma">Idioma</label>
                        <select id="idioma" name="idioma" required>
                            <option value="es">Español</option>
                            <option value="en">English</option>
                            <option value="fr">Français</option>
                            <option value="pt">Português</option>
                        </select>
                    </div>
                </div>

                <!-- Campos específicos se cargan dinámicamente -->
                <div id="specificFields"></div>

                <!-- Mapa para ubicación -->
                <div class="form-group" id="mapSection">
                    <label>Seleccionar Ubicación en el Mapa</label>
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Configuración global - SIN API KEYS
        const APP_URL = '<?= APP_URL ?>';
        const DEFAULT_LANGUAGE = '<?= $defaultLanguage ?>';

        let currentTab = 'dias';
        let map = null;
        let currentMarker = null;
        let sidebarOpen = false;
        let resources = {
            dias: [],
            alojamientos: [],
            actividades: [],
            transportes: []
        };

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeTabs();
            loadResources();
            setupSearch();
            initializeGoogleTranslate();
        });
        
        // Funciones de sidebar CORREGIDAS
        function toggleSidebar() {
            // Buscar por clase, no por ID
            const sidebar = document.querySelector('.enhanced-sidebar');
            const overlay = document.getElementById('overlay');
            const mainContent = document.getElementById('mainContent');
            
            // Debug para verificar elementos
            console.log('🔍 Elementos sidebar:', {
                sidebar: !!sidebar,
                overlay: !!overlay,
                mainContent: !!mainContent
            });
            
            if (!sidebar) {
                console.error('❌ Sidebar no encontrado con clase .enhanced-sidebar');
                return;
            }
            
            sidebarOpen = !sidebarOpen;
            
            if (sidebarOpen) {
                sidebar.classList.add('open');
                if (overlay) overlay.classList.add('show');
                if (mainContent && window.innerWidth > 768) {
                    mainContent.classList.add('sidebar-open');
                }
                console.log('✅ Sidebar abierto');
            } else {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('show');
                if (mainContent) mainContent.classList.remove('sidebar-open');
                console.log('✅ Sidebar cerrado');
            }
        }

        function closeSidebar() {
            if (sidebarOpen) {
                toggleSidebar();
            }
        }

        function toggleUserMenu() {
            if (confirm('¿Desea cerrar sesión?')) {
                window.location.href = '<?= APP_URL ?>/auth/logout';
            }
        }

        // ============================================= 
        // NUEVA FUNCIÓN DE MAPA CON OPENSTREETMAP
        // ============================================= 

        // Inicializar mapa con OpenStreetMap (GRATIS)
        function initializeMap() {
            const mapContainer = document.getElementById('map');
            
            try {
                // Limpiar contenedor
                mapContainer.innerHTML = '';
                
                // Crear mapa con OpenStreetMap
                map = L.map('map').setView([4.7110, -74.0721], 10); // Bogotá por defecto

                // Agregar capa gratuita de OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18,
                    minZoom: 2
                }).addTo(map);

                // Control de zoom
                L.control.zoom({
                    position: 'topright'
                }).addTo(map);

                // Click en el mapa para seleccionar ubicación
                map.on('click', function(e) {
                    const coords = e.latlng;
                    
                    // Remover marcador anterior
                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }
                    
                    // Agregar nuevo marcador (azul como el tema)
                    currentMarker = L.marker([coords.lat, coords.lng], {
                        draggable: true
                    }).addTo(map);

                    // Popup informativo
                    currentMarker.bindPopup(`
                        <div style="text-align: center;">
                            <strong>📍 Ubicación Seleccionada</strong><br>
                            <small>Lat: ${coords.lat.toFixed(6)}<br>
                            Lng: ${coords.lng.toFixed(6)}</small>
                        </div>
                    `).openPopup();
                    
                    // Geocodificación gratuita
                    reverseGeocodeOSM(coords.lat, coords.lng);
                    
                    // Event listener para arrastrar marcador
                    currentMarker.on('dragend', function(e) {
                        const newCoords = e.target.getLatLng();
                        reverseGeocodeOSM(newCoords.lat, newCoords.lng);
                        
                        // Actualizar popup
                        currentMarker.setPopupContent(`
                            <div style="text-align: center;">
                                <strong>📍 Ubicación Actualizada</strong><br>
                                <small>Lat: ${newCoords.lat.toFixed(6)}<br>
                                Lng: ${newCoords.lng.toFixed(6)}</small>
                            </div>
                        `);
                    });
                });

                // Evento cuando el mapa se carga
                map.whenReady(function() {
                    console.log('✅ Mapa OpenStreetMap cargado - 100% GRATIS');
                    
                    // Mensaje de bienvenida
                    setTimeout(() => {
                        if (!currentMarker) {
                            L.popup()
                                .setLatLng([4.7110, -74.0721])
                                .setContent(`
                                    <div style="text-align: center;">
                                        <strong>🗺️ Mapa Interactivo</strong><br>
                                        <small>Haz clic en cualquier lugar para seleccionar ubicación</small>
                                    </div>
                                `)
                                .openOn(map);
                        }
                    }, 1000);
                });

                // Redimensionar mapa cuando se abre el modal
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);

            } catch (error) {
                console.error('Error cargando mapa:', error);
                initializeMapFallback();
            }
        }

        // Función de respaldo si falla el mapa
        function initializeMapFallback() {
            const mapContainer = document.getElementById('map');
            mapContainer.innerHTML = `
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; border-radius: 10px; padding: 20px;">
                    <div style="font-size: 48px; margin-bottom: 20px;">📍</div>
                    <h3 style="margin-bottom: 15px;">Seleccionar Ubicación</h3>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 20px; width: 100%; max-width: 300px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                            <input type="number" id="manual-lat" placeholder="Latitud" step="any" style="padding: 10px; border: none; border-radius: 5px; text-align: center;">
                            <input type="number" id="manual-lng" placeholder="Longitud" step="any" style="padding: 10px; border: none; border-radius: 5px; text-align: center;">
                        </div>
                        <button onclick="useCurrentLocation()" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid white; padding: 10px 20px; border-radius: 25px; cursor: pointer; margin-right: 10px;">📱 Mi Ubicación</button>
                        <button onclick="searchLocationPrompt()" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid white; padding: 10px 20px; border-radius: 25px; cursor: pointer;">🔍 Buscar</button>
                    </div>
                </div>
            `;
            
            setTimeout(() => {
                const latInput = document.getElementById('manual-lat');
                const lngInput = document.getElementById('manual-lng');
                
                if (latInput && lngInput) {
                    latInput.addEventListener('change', updateLocationFromCoords);
                    lngInput.addEventListener('change', updateLocationFromCoords);
                }
            }, 100);
        }

        // ============================================= 
        // GEOCODIFICACIÓN GRATUITA CON NOMINATIM
        // ============================================= 

        function reverseGeocodeOSM(lat, lng) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=es`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        const ubicacionField = document.getElementById('ubicacion');
                        if (ubicacionField) {
                            ubicacionField.value = data.display_name;
                        }
                        
                        // Guardar coordenadas en campos ocultos
                        updateCoordinateFields(lat, lng);
                        
                        console.log('📍 Ubicación encontrada:', data.display_name);
                    } else {
                        // Si no hay resultado, usar coordenadas
                        const ubicacionField = document.getElementById('ubicacion');
                        if (ubicacionField) {
                            ubicacionField.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        }
                        updateCoordinateFields(lat, lng);
                    }
                })
                .catch(error => {
                    console.warn('Geocodificación no disponible:', error);
                    const ubicacionField = document.getElementById('ubicacion');
                    if (ubicacionField) {
                        ubicacionField.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    }
                    updateCoordinateFields(lat, lng);
                });
        }

       // ============================================= 
        // FUNCIONES AUXILIARES
        // ============================================= 

        function updateCoordinateFields(lat, lng) {
            // Buscar campos de latitud y longitud en el formulario
            const latField = document.getElementById('latitud') || document.querySelector('input[name="latitud"]');
            const lngField = document.getElementById('longitud') || document.querySelector('input[name="longitud"]');
            
            if (latField) latField.value = lat;
            if (lngField) lngField.value = lng;

            // Para transportes, también actualizar campos específicos si es el campo activo
            const currentInput = document.activeElement;
            if (currentInput && currentInput.name === 'lugar_salida') {
                const latSalidaField = document.getElementById('lat_salida');
                const lngSalidaField = document.getElementById('lng_salida');
                if (latSalidaField) latSalidaField.value = lat;
                if (lngSalidaField) lngSalidaField.value = lng;
            } else if (currentInput && currentInput.name === 'lugar_llegada') {
                const latLlegadaField = document.getElementById('lat_llegada');
                const lngLlegadaField = document.getElementById('lng_llegada');
                if (latLlegadaField) latLlegadaField.value = lat;
                if (lngLlegadaField) lngLlegadaField.value = lng;
            }
        }

        function updateLocationFromCoords() {
            const latInput = document.getElementById('manual-lat');
            const lngInput = document.getElementById('manual-lng');
            
            if (latInput && lngInput) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    reverseGeocodeOSM(lat, lng);
                }
            }
        }

        function searchLocationOSM(query) {
            if (!query || query.length < 3) return;
            
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&accept-language=es`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);
                        
                        if (map) {
                            // Centrar mapa en el resultado
                            map.setView([lat, lng], 15);
                            
                            // Agregar/mover marcador
                            if (currentMarker) {
                                map.removeLayer(currentMarker);
                            }
                            
                            currentMarker = L.marker([lat, lng], {
                                draggable: true
                            }).addTo(map);
                            
                            currentMarker.bindPopup(`
                                <div style="text-align: center;">
                                    <strong>🔍 ${result.display_name}</strong><br>
                                    <small>Lat: ${lat.toFixed(6)}<br>
                                    Lng: ${lng.toFixed(6)}</small>
                                </div>
                            `).openPopup();
                        }
                        
                        // Actualizar campos
                        const ubicacionField = document.getElementById('ubicacion');
                        if (ubicacionField) {
                            ubicacionField.value = result.display_name;
                        }
                        
                        updateCoordinateFields(lat, lng);
                        console.log('🔍 Búsqueda exitosa:', result.display_name);
                    } else {
                        alert('No se encontraron resultados para: ' + query);
                    }
                })
                .catch(error => {
                    console.error('Error en búsqueda:', error);
                    alert('Error en la búsqueda. Verifica tu conexión a internet.');
                });
        }

        function useCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (map) {
                        map.setView([lat, lng], 15);
                        
                        if (currentMarker) {
                            map.removeLayer(currentMarker);
                        }
                        
                        currentMarker = L.marker([lat, lng], {
                            draggable: true
                        }).addTo(map);
                        
                        currentMarker.bindPopup(`
                            <div style="text-align: center;">
                                <strong>📱 Tu Ubicación Actual</strong><br>
                                <small>Lat: ${lat.toFixed(6)}<br>
                                Lng: ${lng.toFixed(6)}</small>
                            </div>
                        `).openPopup();
                    } else {
                        // Para modo fallback
                        const latInput = document.getElementById('manual-lat');
                        const lngInput = document.getElementById('manual-lng');
                        if (latInput && lngInput) {
                            latInput.value = lat.toFixed(6);
                            lngInput.value = lng.toFixed(6);
                        }
                    }
                    
                    reverseGeocodeOSM(lat, lng);
                }, function(error) {
                    alert('No se pudo obtener la ubicación: ' + error.message);
                });
            } else {
                alert('La geolocalización no es compatible con este navegador');
            }
        }

        function searchLocationPrompt() {
            const query = prompt('Ingresa el nombre del lugar que quieres buscar:\n(Ejemplo: "Torre Eiffel, París" o "Medellín, Colombia")');
            if (query && query.trim()) {
                searchLocationOSM(query.trim());
            }
        }

        // Configuración de tabs
        function initializeTabs() {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Actualizar tabs activos
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Cambiar contenido
                    currentTab = this.dataset.tab;
                    loadResources();
                });
            });
        }

        // MODIFICAR la función loadResources existente
        async function loadResources() {
            const grid = document.getElementById('contentGrid');
            const emptyState = document.getElementById('emptyState');
            
            try {
                // Indicador de carga más sutil
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 20px;">
                        <div style="display: inline-flex; align-items: center; gap: 10px; background: white; padding: 15px 25px; border-radius: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <div style="width: 16px; height: 16px; border: 2px solid #e2e8f0; border-top: 2px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                            <span>Buscando recursos...</span>
                        </div>
                    </div>
                `;
                
                const params = new URLSearchParams({
                    action: 'list',
                    type: currentTab
                });
                
                const search = document.getElementById('searchInput').value.trim();
                const language = document.getElementById('languageFilter').value;
                
                if (search) params.append('search', search);
                if (language) params.append('language', language);
                
                const response = await fetch(`${APP_URL}/biblioteca/api?${params}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'Error desconocido');
                }
                
                resources[currentTab] = result.data || [];
                
                // Si hay filtros activos, mostrar resultados filtrados
                if (search || language) {
                    renderFilteredResults(resources[currentTab]);
                } else {
                    renderResources();
                }
                
            } catch (error) {
                console.error('Error al cargar recursos:', error);
                showSearchError(error.message);
            }
        }

        // AGREGAR esta función
        function showSearchError(message) {
            const grid = document.getElementById('contentGrid');
            grid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #fef2f2; border-radius: 15px; border: 1px solid #fecaca;">
                    <div style="font-size: 48px; margin-bottom: 20px;">⚠️</div>
                    <h3 style="color: #dc2626; margin-bottom: 10px;">Error en la búsqueda</h3>
                    <p style="color: #b91c1c; margin-bottom: 20px;">${message}</p>
                    <button onclick="loadResources()" style="background: #dc2626; color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer; font-weight: 500;">
                        🔄 Intentar de nuevo
                    </button>
                </div>
            `;
        }

        // Modificar SOLO esta parte de renderResources()
        function renderResources() {
            const grid = document.getElementById('contentGrid');
            const emptyState = document.getElementById('emptyState');
            
            try {
                if (!resources[currentTab] || resources[currentTab].length === 0) {
                    grid.style.display = 'none';
                    emptyState.style.display = 'block';
                    emptyState.innerHTML = `
                        <div class="empty-state-icon">📂</div>
                        <h3>No hay recursos disponibles</h3>
                        <p>Comienza agregando tu primer recurso haciendo clic en "Agregar Nuevo"</p>
                    `;
                    return;
                }

                grid.style.display = 'grid';
                emptyState.style.display = 'none';
                
                // AGREGAR ESTA LÍNEA - Aplicar filtros si hay alguno activo
                const search = document.getElementById('searchInput').value.trim();
                const language = document.getElementById('languageFilter').value;
                
                if (search || language) {
                    filtrarRecursos();
                    return;
                }
                
                grid.innerHTML = resources[currentTab].map(item => {
                    return createResourceCard(item);
                }).join('');
                
            } catch (error) {
                console.error('Error al renderizar recursos:', error);
                grid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #fed7d7; border-radius: 15px;">
                        <div style="font-size: 48px; margin-bottom: 20px;">⚠️</div>
                        <h3 style="color: #e53e3e;">Error al mostrar recursos</h3>
                        <p style="color: #c53030;">${error.message}</p>
                    </div>
                `;
            }
        }

        // Función para limpiar filtros
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('languageFilter').value = '';
            loadResources();
        }

        // NUEVA FUNCIÓN: Obtener imagen principal
function getPrimaryImage(item, type) {
    switch(type) {
        case 'dias':
        case 'actividades':
            return item.imagen1 || item.imagen2 || item.imagen3 || null;
        case 'alojamientos':
            return item.imagen || null;
        default:
            return null;
    }
}
// NUEVA FUNCIÓN: Contar imágenes
function getImageCount(item, type) {
    let count = 0;
    switch(type) {
        case 'dias':
        case 'actividades':
            if (item.imagen1) count++;
            if (item.imagen2) count++;
            if (item.imagen3) count++;
            break;
        case 'alojamientos':
            if (item.imagen) count++;
            break;
    }
    return count;
}

// NUEVA FUNCIÓN: Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

        // Crear card de recurso
        function createResourceCard(item) {
            const icons = {
                dias: '📅',
                alojamientos: '🏨',
                actividades: '🎯',
                transportes: '🚗'
            };

            const title = item.titulo || item.nombre || 'Sin título';
            const location = item.ubicacion || `${item.lugar_salida} → ${item.lugar_llegada}` || '';
            
            // Obtener la primera imagen disponible
            const primaryImage = getPrimaryImage(item, currentTab);
            
            return `
                <div class="item-card" onclick="editResource(${item.id})">
                    <div class="card-image">
                        ${primaryImage ? 
                            `<img src="${primaryImage}" alt="${title}" style="width: 100%; height: 100%; object-fit: cover;">` : 
                            icons[currentTab]
                        }
                        ${getImageCount(item, currentTab) > 0 ? `<div class="image-count">📷 ${getImageCount(item, currentTab)}</div>` : ''}
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">${escapeHtml(title)}</h3>
                        <p class="card-description">${escapeHtml(item.descripcion || 'Sin descripción')}</p>
                        <div class="card-location">📍 ${escapeHtml(location)}</div>
                        ${item.categoria ? `<div class="card-category">⭐ ${item.categoria} estrellas</div>` : ''}
                        ${item.tipo ? `<div class="card-type">🏷️ ${item.tipo}</div>` : ''}
                        ${item.medio ? `<div class="card-transport">🚗 ${item.medio}</div>` : ''}
                    </div>
                    <div class="card-actions">
                        <button class="action-btn edit" onclick="event.stopPropagation(); editResource(${item.id})">
                            ✏️ Editar
                        </button>
                        <button class="action-btn delete" onclick="event.stopPropagation(); deleteResource(${item.id})">
                            🗑️ Eliminar
                        </button>
                    </div>
                </div>
            `;
        }

        // Funciones del modal
        function openModal(mode, id = null) {
            const modal = document.getElementById('resourceModal');
            const title = document.getElementById('modalTitle');
            
            // Configurar título
            const titles = {
                dias: mode === 'create' ? 'Agregar Nuevo Día' : 'Editar Día',
                alojamientos: mode === 'create' ? 'Agregar Nuevo Alojamiento' : 'Editar Alojamiento',
                actividades: mode === 'create' ? 'Agregar Nueva Actividad' : 'Editar Actividad',
                transportes: mode === 'create' ? 'Agregar Nuevo Transporte' : 'Editar Transporte'
            };
            
            title.textContent = titles[currentTab];
            document.getElementById('resourceType').value = currentTab;
            document.getElementById('resourceId').value = id || '';
            
            // Cargar campos específicos
            loadSpecificFields();
            
            // Mostrar modal
            modal.classList.add('show');
            
            // Inicializar mapa después de mostrar modal
            setTimeout(() => {
                initializeMap();
            }, 200);

            setTimeout(() => {
                setupLocationAutocomplete();
            }, 300);
            
            // Si es edición, cargar datos
            if (mode === 'edit' && id) {
                loadResourceData(id);
            }
        }

        function closeModal() {
            const modal = document.getElementById('resourceModal');
            modal.classList.remove('show');
            
            // Limpiar formulario
            document.getElementById('resourceForm').reset();
            
            // Destruir mapa
            if (map) {
                map.remove();
                map = null;
                currentMarker = null;
            }
        }

        // Submit del formulario - CORREGIDO PARA MANEJAR IMÁGENES
document.getElementById('resourceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    try {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';
        
        // Crear FormData para manejar archivos
        const formData = new FormData(this);
        
        const id = document.getElementById('resourceId').value;
        const type = document.getElementById('resourceType').value;
        
        if (id) {
            formData.append('action', 'update');
            formData.append('id', id);
        } else {
            formData.append('action', 'create');
        }
        
        formData.append('type', type);
        
        // Realizar petición
        const response = await fetch(`${APP_URL}/biblioteca/api`, {
            method: 'POST',
            body: formData // No establecer Content-Type, el navegador lo hará automáticamente
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Error desconocido');
        }
        
        // Éxito
        alert(result.message || 'Operación exitosa');
        closeModal();
        loadResources();
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

// Función mejorada para manejar la vista previa de imágenes
function setupImagePreviews() {
    // Configurar vista previa para todos los inputs de imagen
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            handleImagePreview(this);
        });
    });
}

// Función mejorada para manejar la vista previa de imágenes
function handleImagePreview(input) {
    const file = input.files[0];
    const container = input.closest('.image-upload') || input.parentElement;
    
    // Remover vista previa anterior
    const existingPreview = container.querySelector('.image-preview');
    const existingIndicator = container.querySelector('.existing-image-indicator');
    if (existingPreview) existingPreview.remove();
    if (existingIndicator) existingIndicator.remove();
    
    if (file) {
        // Validar archivo
        if (!file.type.startsWith('image/')) {
            alert('Por favor selecciona un archivo de imagen válido');
            input.value = '';
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo es demasiado grande. Máximo 5MB permitido');
            input.value = '';
            return;
        }
        
        // Crear vista previa
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'image-preview new';
            preview.style.cssText = `
                max-width: 100%;
                max-height: 150px;
                border-radius: 8px;
                margin-top: 10px;
                object-fit: cover;
                border: 2px solid #3b82f6;
            `;
            
            // Agregar indicador de nueva imagen
            const indicator = document.createElement('div');
            indicator.className = 'new-image-indicator';
            indicator.style.cssText = `
                background: #3b82f6;
                color: white;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 10px;
                margin-top: 5px;
                text-align: center;
            `;
            indicator.textContent = '🆕 Nueva imagen';
            
            container.appendChild(preview);
            container.appendChild(indicator);
        };
        reader.readAsDataURL(file);
    }
}

// Función mejorada para cargar campos específicos
function loadSpecificFields() {
    const container = document.getElementById('specificFields');
    let fieldsHTML = '';
    
    switch(currentTab) {
        case 'dias':
            fieldsHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label for="titulo">Título de la Jornada</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Día en París">
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" required placeholder="Ciudad, País">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required placeholder="Describe las actividades del día..."></textarea>
                </div>
                <div class="form-group">
                    <label>Imágenes (máximo 3)</label>
                    <div class="images-grid">
                        <div class="image-upload" onclick="document.getElementById('imagen1').click()">
                            <input type="file" id="imagen1" name="imagen1" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 1</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                        <div class="image-upload" onclick="document.getElementById('imagen2').click()">
                            <input type="file" id="imagen2" name="imagen2" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 2</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                        <div class="image-upload" onclick="document.getElementById('imagen3').click()">
                            <input type="file" id="imagen3" name="imagen3" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 3</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">
            `;
            break;
            
        case 'alojamientos':
            fieldsHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre del Alojamiento</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Hotel París Centro">
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" required placeholder="Dirección completa">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Alojamiento</label>
                        <select id="tipo" name="tipo" required onchange="updateCategoryField()">
                            <option value="">Seleccionar tipo</option>
                            <option value="hotel">Hotel</option>
                            <option value="camping">Camping</option>
                            <option value="casa_huespedes">Casa de Huéspedes</option>
                            <option value="crucero">Crucero</option>
                            <option value="lodge">Lodge</option>
                            <option value="atipico">Atípico</option>
                            <option value="campamento">Campamento</option>
                            <option value="camping_car">Camping Car</option>
                            <option value="tren">Tren</option>
                        </select>
                    </div>
                    <div class="form-group" id="categoryGroup" style="display: none;">
                        <label for="categoria">Categoría (Estrellas)</label>
                        <select id="categoria" name="categoria">
                            <option value="">Sin categoría</option>
                            <option value="1">⭐ 1 Estrella</option>
                            <option value="2">⭐⭐ 2 Estrellas</option>
                            <option value="3">⭐⭐⭐ 3 Estrellas</option>
                            <option value="4">⭐⭐⭐⭐ 4 Estrellas</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5 Estrellas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sitio_web">Sitio Web (Opcional)</label>
                        <input type="url" id="sitio_web" name="sitio_web" placeholder="https://...">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required placeholder="Describe el alojamiento..."></textarea>
                </div>
                <div class="form-group">
                    <label>Imagen Representativa</label>
                    <div class="image-upload" onclick="document.getElementById('imagen').click()">
                        <input type="file" id="imagen" name="imagen" accept="image/*" style="display: none;">
                        <div class="upload-content">
                            <div style="font-size: 32px; margin-bottom: 8px;">📷</div>
                            <div>Subir Imagen</div>
                            <div style="font-size: 12px; color: #718096;">Click para seleccionar archivo</div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">
            `;
            break;
            
        case 'actividades':
            fieldsHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre de la Actividad</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Tour Eiffel">
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" required placeholder="Lugar donde se realiza">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required placeholder="Describe la actividad..."></textarea>
                </div>
                <div class="form-group">
                    <label>Imágenes (máximo 3)</label>
                    <div class="images-grid">
                        <div class="image-upload" onclick="document.getElementById('imagen1').click()">
                            <input type="file" id="imagen1" name="imagen1" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 1</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                        <div class="image-upload" onclick="document.getElementById('imagen2').click()">
                            <input type="file" id="imagen2" name="imagen2" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 2</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                        <div class="image-upload" onclick="document.getElementById('imagen3').click()">
                            <input type="file" id="imagen3" name="imagen3" accept="image/*" style="display: none;">
                            <div class="upload-content">
                                <div style="font-size: 24px; margin-bottom: 8px;">📷</div>
                                <div>Imagen 3</div>
                                <div style="font-size: 12px; color: #718096;">Click para seleccionar</div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="latitud" name="latitud">
                <input type="hidden" id="longitud" name="longitud">
            `;
            break;
            
        case 'transportes':
            fieldsHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label for="medio">Medio de Transporte</label>
                        <select id="medio" name="medio" required>
                            <option value="">Seleccionar medio</option>
                            <option value="bus">🚌 Bus</option>
                            <option value="avion">✈️ Avión</option>
                            <option value="coche">🚗 Coche</option>
                            <option value="barco">🚢 Barco</option>
                            <option value="tren">🚂 Tren</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="titulo">Título del Transporte</label>
                        <input type="text" id="titulo" name="titulo" required placeholder="Ej: Vuelo París-Roma">
                    </div>
                    <div class="form-group">
                        <label for="lugar_salida">Lugar de Salida</label>
                        <input type="text" id="lugar_salida" name="lugar_salida" required placeholder="Ciudad/Aeropuerto de salida">
                    </div>
                    <div class="form-group">
                        <label for="lugar_llegada">Lugar de Llegada</label>
                        <input type="text" id="lugar_llegada" name="lugar_llegada" required placeholder="Ciudad/Aeropuerto de llegada">
                    </div>
                    <div class="form-group">
                        <label for="duracion">Duración</label>
                        <input type="text" id="duracion" name="duracion" placeholder="Ej: 2 horas 30 minutos">
                    </div>
                    <div class="form-group">
                        <label for="distancia_km">Distancia (km)</label>
                        <input type="number" id="distancia_km" name="distancia_km" step="0.01" placeholder="Distancia en kilómetros">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Detalles adicionales del transporte..."></textarea>
                </div>
                <input type="hidden" id="lat_salida" name="lat_salida">
                <input type="hidden" id="lng_salida" name="lng_salida">
                <input type="hidden" id="lat_llegada" name="lat_llegada">
                <input type="hidden" id="lng_llegada" name="lng_llegada">
            `;
            break;
    }
    
    container.innerHTML = fieldsHTML;
    
    // Configurar vista previa de imágenes después de cargar los campos
        setTimeout(() => {
            setupImagePreviews();
            setupTransportLocationFields();
            setupLocationAutocomplete(); // Asegurar que se llame
            console.log('🚀 Campos específicos cargados y autocompletado inicializado');
        }, 200);
}
        
        // Función para configurar autocompletado bidireccional
// Función para configurar autocompletado bidireccional - VERSIÓN CORREGIDA
function setupLocationAutocomplete() {
    console.log('🔧 Configurando autocompletado de ubicación...');
    
    const ubicacionField = document.getElementById('ubicacion');
    if (!ubicacionField) {
        console.log('❌ Campo ubicación no encontrado');
        return;
    }

    console.log('✅ Campo ubicación encontrado, configurando eventos...');

    let searchTimeout;
    let suggestionsList = null;

    // Event listener para cuando el usuario escribe
    ubicacionField.addEventListener('input', function() {
        const query = this.value.trim();
        console.log('👤 Usuario escribiendo:', query);
        
        // Limpiar timeout anterior
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Remover sugerencias anteriores
        removeSuggestions();

        // Si la consulta es muy corta, no buscar
        if (query.length < 3) {
            return;
        }

        // Buscar después de 300ms de pausa en escritura
        searchTimeout = setTimeout(() => {
            console.log('🔍 Iniciando búsqueda para:', query);
            searchLocationWithCoordinates(query, ubicacionField, 'ubicacion');
        }, 300);
    });

    // Event listener para cuando pierde el foco
    ubicacionField.addEventListener('blur', function() {
        setTimeout(() => {
            removeSuggestions();
        }, 200);
    });

    // Event listener para teclas especiales
    ubicacionField.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            removeSuggestions();
        }
    });

    console.log('✅ Autocompletado configurado correctamente');
}
function setupTransportLocationFields() {
    // Configurar autocompletado para lugar de salida
    const salidaField = document.getElementById('lugar_salida');
    if (salidaField) {
        setupFieldAutocomplete(salidaField, 'salida');
    }

    // Configurar autocompletado para lugar de llegada
    const llegadaField = document.getElementById('lugar_llegada');
    if (llegadaField) {
        setupFieldAutocomplete(llegadaField, 'llegada');
    }
}

function setupFieldAutocomplete(field, type) {
    let searchTimeout;

    field.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        removeSuggestions();

        if (query.length < 3) {
            return;
        }

        searchTimeout = setTimeout(() => {
            searchAndShowFieldSuggestions(query, field, type);
        }, 500);
    });

    field.addEventListener('blur', function() {
        setTimeout(() => {
            removeSuggestions();
        }, 200);
    });
}

function searchAndShowFieldSuggestions(query, inputField, type) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&accept-language=es`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                showFieldSuggestions(data, inputField, type);
            }
        })
        .catch(error => {
            console.warn('Error en búsqueda:', error);
        });
}

function showFieldSuggestions(suggestions, inputField, type) {
    removeSuggestions();

    suggestionsList = document.createElement('div');
    suggestionsList.className = 'location-suggestions';
    suggestionsList.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    `;

    suggestions.forEach((suggestion) => {
        const suggestionItem = document.createElement('div');
        suggestionItem.className = 'suggestion-item';
        suggestionItem.style.cssText = `
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
            font-size: 14px;
        `;

        suggestionItem.innerHTML = `
            <div style="font-weight: 500; color: #2d3748;">
                ${getLocationTitle(suggestion)}
            </div>
            <div style="font-size: 12px; color: #718096;">
                ${suggestion.display_name}
            </div>
        `;

        suggestionItem.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f7fafc';
        });

        suggestionItem.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });

        suggestionItem.addEventListener('click', function() {
            selectFieldLocation(suggestion, inputField, type);
        });

        suggestionsList.appendChild(suggestionItem);
    });

    const inputContainer = inputField.parentElement;
    inputContainer.style.position = 'relative';
    inputContainer.appendChild(suggestionsList);
}

function selectFieldLocation(suggestion, inputField, type) {
    const lat = parseFloat(suggestion.lat);
    const lng = parseFloat(suggestion.lon);

    // Actualizar campo
    inputField.value = suggestion.display_name;

    // Actualizar coordenadas específicas según el tipo
    if (type === 'salida') {
        const latField = document.getElementById('lat_salida');
        const lngField = document.getElementById('lng_salida');
        if (latField) latField.value = lat;
        if (lngField) lngField.value = lng;
    } else if (type === 'llegada') {
        const latField = document.getElementById('lat_llegada');
        const lngField = document.getElementById('lng_llegada');
        if (latField) latField.value = lat;
        if (lngField) lngField.value = lng;
    }

    removeSuggestions();
    console.log(`📍 ${type} seleccionada:`, suggestion.display_name);
}

// Buscar sugerencias y mostrarlas
function searchAndShowSuggestions(query, inputField) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&accept-language=es&addressdetails=1`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                showSuggestions(data, inputField);
            }
        })
        .catch(error => {
            console.warn('Error en búsqueda de sugerencias:', error);
        });
}

// Mostrar lista de sugerencias
function showSuggestions(suggestions, inputField) {
    // Remover sugerencias anteriores
    removeSuggestions();

    // Crear contenedor de sugerencias
    suggestionsList = document.createElement('div');
    suggestionsList.className = 'location-suggestions';
    suggestionsList.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    `;

    // Crear elementos de sugerencia
    suggestions.forEach((suggestion, index) => {
        const suggestionItem = document.createElement('div');
        suggestionItem.className = 'suggestion-item';
        suggestionItem.style.cssText = `
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
            font-size: 14px;
            line-height: 1.4;
        `;

        // Contenido de la sugerencia
        suggestionItem.innerHTML = `
            <div style="font-weight: 500; color: #2d3748; margin-bottom: 2px;">
                ${getLocationTitle(suggestion)}
            </div>
            <div style="font-size: 12px; color: #718096;">
                ${suggestion.display_name}
            </div>
        `;

        // Event listeners para hover
        suggestionItem.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f7fafc';
        });

        suggestionItem.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });

        // Event listener para click
        suggestionItem.addEventListener('click', function() {
            selectLocation(suggestion, inputField);
        });

        suggestionsList.appendChild(suggestionItem);
    });

    // Posicionar relativo al input
    const inputContainer = inputField.parentElement;
    inputContainer.style.position = 'relative';
    inputContainer.appendChild(suggestionsList);
}

// Obtener título limpio para la ubicación
function getLocationTitle(suggestion) {
    // Extraer el nombre principal de la ubicación
    const parts = suggestion.display_name.split(',');
    if (parts.length > 0) {
        return parts[0].trim();
    }
    return suggestion.display_name;
}

// Seleccionar una ubicación de las sugerencias
function selectLocation(suggestion, inputField) {
    const lat = parseFloat(suggestion.lat);
    const lng = parseFloat(suggestion.lon);

    // Actualizar campo de ubicación
    inputField.value = suggestion.display_name;

    // Actualizar coordenadas
    updateCoordinateFields(lat, lng);

    // Actualizar mapa si existe
    if (map) {
        // Centrar mapa en la ubicación
        map.setView([lat, lng], 15);

        // Remover marcador anterior
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }

        // Agregar nuevo marcador
        currentMarker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Popup informativo
        currentMarker.bindPopup(`
            <div style="text-align: center;">
                <strong>📍 ${getLocationTitle(suggestion)}</strong><br>
                <small>${suggestion.display_name}</small><br>
                <small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
            </div>
        `).openPopup();

        // Event listener para arrastrar
        currentMarker.on('dragend', function(e) {
            const newCoords = e.target.getLatLng();
            reverseGeocodeOSM(newCoords.lat, newCoords.lng);
            
            currentMarker.setPopupContent(`
                <div style="text-align: center;">
                    <strong>📍 Ubicación Actualizada</strong><br>
                    <small>Lat: ${newCoords.lat.toFixed(6)}<br>
                    Lng: ${newCoords.lng.toFixed(6)}</small>
                </div>
            `);
        });
    }

    // Remover sugerencias
    removeSuggestions();

    console.log('📍 Ubicación seleccionada:', suggestion.display_name);
}

// Remover lista de sugerencias
function removeSuggestions() {
    const existingList = document.querySelector('.location-suggestions');
    if (existingList) {
        existingList.remove();
    }
    suggestionsList = null;
}

        // Actualizar campo de categoría según tipo de alojamiento
        function updateCategoryField() {
            const tipo = document.getElementById('tipo').value;
            const categoryGroup = document.getElementById('categoryGroup');
            
            // Tipos que requieren categoría (estrellas)
            const typesWithCategory = ['hotel', 'camping', 'casa_huespedes', 'crucero', 'lodge'];
            
            if (typesWithCategory.includes(tipo)) {
                categoryGroup.style.display = 'block';
                document.getElementById('categoria').required = true;
            } else {
                categoryGroup.style.display = 'none';
                document.getElementById('categoria').required = false;
                document.getElementById('categoria').value = '';
            }
        }

        // Reemplazar la configuración de búsqueda para que funcione con la API real:
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            const languageFilter = document.getElementById('languageFilter');
            
            let searchTimeout;
            
            function buscarAhora() {
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                searchTimeout = setTimeout(() => {
                    filtrarRecursos();
                }, 200);
            }
            
            searchInput.addEventListener('input', buscarAhora);
            languageFilter.addEventListener('change', filtrarRecursos);
            
            // Limpiar con ESC
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    filtrarRecursos();
                }
            });
        }

        function filtrarRecursos() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const languageFilter = document.getElementById('languageFilter').value;
            const grid = document.getElementById('contentGrid');
            const emptyState = document.getElementById('emptyState');
            
            // Si no hay datos, cargar desde API
            if (!resources[currentTab] || resources[currentTab].length === 0) {
                loadResources();
                return;
            }
            
            // Filtrar datos existentes
            let filtered = resources[currentTab];
            
            // Filtrar por búsqueda
            if (searchTerm) {
                filtered = filtered.filter(item => {
                    return (item.titulo && item.titulo.toLowerCase().includes(searchTerm)) ||
                        (item.nombre && item.nombre.toLowerCase().includes(searchTerm)) ||
                        (item.descripcion && item.descripcion.toLowerCase().includes(searchTerm)) ||
                        (item.ubicacion && item.ubicacion.toLowerCase().includes(searchTerm)) ||
                        (item.lugar_salida && item.lugar_salida.toLowerCase().includes(searchTerm)) ||
                        (item.lugar_llegada && item.lugar_llegada.toLowerCase().includes(searchTerm)) ||
                        (item.medio && item.medio.toLowerCase().includes(searchTerm));
                });
            }
            
            // Filtrar por idioma
            if (languageFilter) {
                filtered = filtered.filter(item => item.idioma === languageFilter);
            }
            
            // Mostrar resultados
            if (filtered.length === 0) {
                grid.style.display = 'none';
                emptyState.style.display = 'block';
                emptyState.innerHTML = `
                    <div class="empty-state-icon">🔍</div>
                    <h3>No se encontraron resultados</h3>
                    <p>Intenta con otros términos de búsqueda</p>
                    <button onclick="limpiarFiltros()" style="background: var(--primary-gradient); color: white; border: none; padding: 10px 20px; border-radius: 20px; margin-top: 15px; cursor: pointer;">
                        🗑️ Limpiar Filtros
                    </button>
                `;
            } else {
                grid.style.display = 'grid';
                emptyState.style.display = 'none';
                grid.innerHTML = filtered.map(item => createResourceCard(item)).join('');
            }
        }

        // Función para limpiar filtros
        function limpiarFiltros() {
            document.getElementById('searchInput').value = '';
            document.getElementById('languageFilter').value = '';
            filtrarRecursos();
        }

// Función para renderizar resultados filtrados
function renderFilteredResults(filtered) {
    const grid = document.getElementById('contentGrid');
    const emptyState = document.getElementById('emptyState');
    
    if (filtered.length === 0) {
        grid.style.display = 'none';
        emptyState.style.display = 'block';
        
        const search = document.getElementById('searchInput').value.trim();
        const language = document.getElementById('languageFilter').value;
        
        if (search || language) {
            emptyState.innerHTML = `
                <div class="empty-state-icon">🔍</div>
                <h3>No se encontraron resultados</h3>
                <p>No hay recursos que coincidan con "<strong>${escapeHtml(search)}</strong>"</p>
                <button onclick="clearAllFilters()" style="background: var(--primary-gradient); color: white; border: none; padding: 10px 20px; border-radius: 20px; margin-top: 15px; cursor: pointer;">
                    🗑️ Limpiar Búsqueda
                </button>
            `;
        }
        return;
    }

    grid.style.display = 'grid';
    emptyState.style.display = 'none';
    
    // Agregar indicador de resultados filtrados
    const searchTerm = document.getElementById('searchInput').value.trim();
    if (searchTerm) {
        grid.innerHTML = `
            <div style="grid-column: 1/-1; background: #e3f2fd; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid var(--primary-color);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>🔍 <strong>${filtered.length}</strong> resultado(s) para "<em>${escapeHtml(searchTerm)}</em>"</span>
                    <button onclick="clearAllFilters()" style="background: none; border: none; color: var(--primary-color); cursor: pointer; font-size: 14px;">✕ Limpiar</button>
                </div>
            </div>
            ${filtered.map(item => createResourceCard(item)).join('')}
        `;
    } else {
        grid.innerHTML = filtered.map(item => createResourceCard(item)).join('');
    }
}

// Función para limpiar todos los filtros
function clearAllFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('languageFilter').value = '';
    renderResources();
    document.getElementById('searchInput').focus();
}
        

function showSearchError(message) {
    const grid = document.getElementById('contentGrid');
    grid.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #fef2f2; border-radius: 15px; border: 1px solid #fecaca;">
            <div style="font-size: 48px; margin-bottom: 20px;">⚠️</div>
            <h3 style="color: #dc2626; margin-bottom: 10px;">Error en la búsqueda</h3>
            <p style="color: #b91c1c; margin-bottom: 20px;">${message}</p>
            <button onclick="loadResources()" style="background: #dc2626; color: white; border: none; padding: 12px 24px; border-radius: 25px; cursor: pointer; font-weight: 500;">
                🔄 Intentar de nuevo
            </button>
        </div>
    `;
}

        function filterResources() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const languageFilter = document.getElementById('languageFilter').value;
            
            // Filtrar recursos
            const filtered = resources[currentTab].filter(item => {
                const matchesSearch = !searchTerm || 
                    (item.titulo && item.titulo.toLowerCase().includes(searchTerm)) ||
                    (item.nombre && item.nombre.toLowerCase().includes(searchTerm)) ||
                    (item.descripcion && item.descripcion.toLowerCase().includes(searchTerm)) ||
                    (item.ubicacion && item.ubicacion.toLowerCase().includes(searchTerm));
                
                const matchesLanguage = !languageFilter || item.idioma === languageFilter;
                
                return matchesSearch && matchesLanguage;
            });
            
            // Renderizar resultados filtrados
            const grid = document.getElementById('contentGrid');
            const emptyState = document.getElementById('emptyState');
            
            if (filtered.length === 0) {
                grid.style.display = 'none';
                emptyState.style.display = 'block';
                emptyState.innerHTML = `
                    <div class="empty-state-icon">🔍</div>
                    <h3>No se encontraron resultados</h3>
                    <p>Intenta con otros términos de búsqueda</p>
                `;
            } else {
                grid.style.display = 'grid';
                emptyState.style.display = 'none';
                grid.innerHTML = filtered.map(item => createResourceCard(item)).join('');
            }
        }

        // Funciones CRUD
       function viewResource(id) {
            viewResourceDetails(id, currentTab);
        }

        function editResource(id) {
            openModal('edit', id);
        }
        // Agregar esta función para manejar errores de subida de imagen de forma más elegante:
        function handleImageUploadError(field, error) {
            const container = document.getElementById(field).closest('.image-upload') || document.getElementById(field).parentElement;
            
            // Remover mensaje de error anterior
            const existingError = container.querySelector('.upload-error');
            if (existingError) existingError.remove();
            
            // Agregar mensaje de error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'upload-error';
            errorDiv.style.cssText = `
                background: #fed7d7;
                color: #e53e3e;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 12px;
                margin-top: 8px;
                border: 1px solid #feb2b2;
            `;
            errorDiv.textContent = `❌ ${error}`;
            
            container.appendChild(errorDiv);
            
            // Remover el error después de 5 segundos
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.remove();
                }
            }, 5000);
        }
        // Función mejorada para mostrar mensajes de éxito
        function showSuccessMessage(message) {
            const toast = document.createElement('div');
            toast.className = 'success-toast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                padding: 16px 20px;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 350px;
            `;
            
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-size: 20px;">✅</div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 2px;">Éxito</div>
                        <div style="font-size: 13px; opacity: 0.9;">${message}</div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Remover después de 3 segundos
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Función mejorada para mostrar mensajes de error
        function showErrorMessage(message) {
            const toast = document.createElement('div');
            toast.className = 'error-toast';
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #e53e3e 0%, #dc2626 100%);
                color: white;
                padding: 16px 20px;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3);
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 350px;
            `;
            
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="font-size: 20px;">❌</div>
                    <div>
                        <div style="font-weight: 600; margin-bottom: 2px;">Error</div>
                        <div style="font-size: 13px; opacity: 0.9;">${message}</div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animar entrada
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Remover después de 4 segundos
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 4000);
        }

        async function deleteResource(id) {
            const confirmed = await showConfirmModal({
                title: '¿Eliminar recurso?',
                message: '¿Estás seguro de que quieres eliminar este recurso?',
                details: 'Esta acción no se puede deshacer.',
                icon: '🗑️',
                confirmText: 'Eliminar',
                cancelText: 'Cancelar'
            });

            if (!confirmed) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('type', currentTab);
                formData.append('id', id);
                
                const response = await fetch(`${APP_URL}/biblioteca/api`, {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'Error al eliminar recurso');
                }
                
                showMessage(result.message || 'Recurso eliminado correctamente', 'success');
                loadResources(); // Recargar la lista

                } catch (error) {
                    console.error('Error al eliminar recurso:', error);
                    showMessage('Error al eliminar el recurso: ' + error.message, 'error');
                }
        }

        // Cargar datos de recurso para editar - MEJORADO
        async function loadResourceData(id) {
            try {
                const response = await fetch(`${APP_URL}/biblioteca/api?action=get&type=${currentTab}&id=${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'Error al cargar recurso');
                }
                
                const resource = result.data;
                console.log('Cargando recurso desde API:', resource);
                
                document.getElementById('resourceId').value = resource.id;
                
                // Cargar campos comunes
                const commonFields = ['idioma', 'descripcion'];
                commonFields.forEach(field => {
                    const element = document.getElementById(field);
                    if (element && resource[field]) {
                        element.value = resource[field];
                    }
                });
                
                // Cargar campos específicos por tipo
                switch(currentTab) {
                    case 'dias':
                        setFieldValue('titulo', resource.titulo);
                        setFieldValue('ubicacion', resource.ubicacion);
                        setFieldValue('latitud', resource.latitud);
                        setFieldValue('longitud', resource.longitud);
                        loadImagePreviews(['imagen1', 'imagen2', 'imagen3'], resource);
                        break;
                        
                    case 'alojamientos':
                        setFieldValue('nombre', resource.nombre);
                        setFieldValue('ubicacion', resource.ubicacion);
                        setFieldValue('tipo', resource.tipo);
                        setFieldValue('categoria', resource.categoria);
                        setFieldValue('sitio_web', resource.sitio_web);
                        setFieldValue('latitud', resource.latitud);
                        setFieldValue('longitud', resource.longitud);
                        loadImagePreviews(['imagen'], resource);
                        updateCategoryField(); // Actualizar visibilidad de categoría
                        break;
                        
                    case 'actividades':
                        setFieldValue('nombre', resource.nombre);
                        setFieldValue('ubicacion', resource.ubicacion);
                        setFieldValue('latitud', resource.latitud);
                        setFieldValue('longitud', resource.longitud);
                        loadImagePreviews(['imagen1', 'imagen2', 'imagen3'], resource);
                        break;
                        
                    case 'transportes':
                        setFieldValue('medio', resource.medio);
                        setFieldValue('titulo', resource.titulo);
                        setFieldValue('lugar_salida', resource.lugar_salida);
                        setFieldValue('lugar_llegada', resource.lugar_llegada);
                        setFieldValue('duracion', resource.duracion);
                        setFieldValue('distancia_km', resource.distancia_km);
                        setFieldValue('lat_salida', resource.lat_salida);
                        setFieldValue('lng_salida', resource.lng_salida);
                        setFieldValue('lat_llegada', resource.lat_llegada);
                        setFieldValue('lng_llegada', resource.lng_llegada);
                        break;
                }
                
                // Actualizar mapa si hay coordenadas
                if (resource.latitud && resource.longitud && map) {
                    setTimeout(() => {
                        map.setView([resource.latitud, resource.longitud], 15);
                        
                        if (currentMarker) {
                            map.removeLayer(currentMarker);
                        }
                        
                        currentMarker = L.marker([resource.latitud, resource.longitud], {
                            draggable: true
                        }).addTo(map);
                        
                        currentMarker.bindPopup(`
                            <div style="text-align: center;">
                                <strong>📍 ${resource.titulo || resource.nombre}</strong><br>
                                <small>${resource.ubicacion}</small>
                            </div>
                        `).openPopup();
                    }, 500);
                }
                
            } catch (error) {
                console.error('Error al cargar datos del recurso:', error);
                showMessage('Error al cargar los datos del recurso: ' + error.message, 'error');
            }
        }

        // NUEVA FUNCIÓN: Establecer valor de campo
        function setFieldValue(fieldId, value) {
            const element = document.getElementById(fieldId);
            if (element && value) {
                element.value = value;
            }
        }
        // Función de notificaciones toast (igual que admin.php)
function showMessage(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">${icon}</span>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 4000);
}


        // Función para mostrar imagen en modal
        function showImageModal(imageSrc, title) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.9);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            `;
            
            modal.innerHTML = `
                <div style="max-width: 90%; max-height: 90%; text-align: center;">
                    <div style="color: white; margin-bottom: 20px; font-size: 18px; font-weight: 600;">
                        ${escapeHtml(title)}
                    </div>
                    <img src="${imageSrc}" style="max-width: 100%; max-height: 80vh; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                    <div style="margin-top: 20px;">
                        <button onclick="this.closest('.image-modal').remove()" style="background: #e53e3e; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">
                            ✕ Cerrar
                        </button>
                    </div>
                </div>
            `;
            
            modal.className = 'image-modal';
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.remove();
                }
            });
            
            document.body.appendChild(modal);
        }

        // Función para remover imagen existente (marcar para eliminación)
        function removeExistingImage(field) {
            if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                const input = document.getElementById(field);
                const container = input.closest('.image-upload') || input.parentElement;
                
                // Remover preview e indicador
                const preview = container.querySelector('.image-preview');
                const indicator = container.querySelector('.existing-image-indicator');
                if (preview) preview.remove();
                if (indicator) indicator.remove();
                
                // Agregar campo oculto para indicar eliminación
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = `delete_${field}`;
                deleteInput.value = '1';
                container.appendChild(deleteInput);
                
                // Mostrar mensaje de confirmación
                const confirmDiv = document.createElement('div');
                confirmDiv.style.cssText = `
                    background: #fef5e7;
                    color: #d69e2e;
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-size: 12px;
                    margin-top: 8px;
                    border: 1px solid #fbd38d;
                `;
                confirmDiv.textContent = '⚠️ Esta imagen será eliminada al guardar';
                container.appendChild(confirmDiv);
            }
        }

            // Función mejorada para manejar la vista previa de imágenes existentes
            function loadImagePreviews(imageFields, resource) {
                imageFields.forEach(field => {
                    if (resource[field]) {
                        const input = document.getElementById(field);
                        if (input) {
                            const container = input.closest('.image-upload') || input.parentElement;
                            
                            // Remover vista previa anterior
                            const existingPreview = container.querySelector('.image-preview');
                            const existingIndicator = container.querySelector('.existing-image-indicator');
                            if (existingPreview) existingPreview.remove();
                            if (existingIndicator) existingIndicator.remove();
                            
                            // Crear vista previa de imagen existente
                            const preview = document.createElement('img');
                            preview.src = resource[field];
                            preview.className = 'image-preview existing';
                            preview.style.cssText = `
                                max-width: 100%;
                                max-height: 150px;
                                border-radius: 8px;
                                margin-top: 10px;
                                object-fit: cover;
                                border: 2px solid #10b981;
                                cursor: pointer;
                            `;
                            
                            // Agregar funcionalidad para ver imagen en grande
                            preview.addEventListener('click', function() {
                                showImageModal(resource[field], resource.titulo || resource.nombre || 'Imagen');
                            });
                            
                            // Agregar indicador de imagen existente
                            const indicator = document.createElement('div');
                            indicator.className = 'existing-image-indicator';
                            indicator.style.cssText = `
                                background: #10b981;
                                color: white;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 10px;
                                margin-top: 5px;
                                text-align: center;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 4px;
                            `;
                            indicator.innerHTML = '✅ Imagen actual <span style="cursor: pointer;" onclick="removeExistingImage(\'' + field + '\')">🗑️</span>';
                            
                            container.appendChild(preview);
                            container.appendChild(indicator);
                        }
                    }
                });
            }

        // Submit del formulario
        document.getElementById('resourceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const id = document.getElementById('resourceId').value;
            
            if (id) {
                const index = resources[currentTab].findIndex(item => item.id == id);
                if (index !== -1) {
                    resources[currentTab][index] = { ...resources[currentTab][index], ...data };
                }
                showMessage('Recurso actualizado correctamente', 'success');
            } else {
                data.id = Date.now();
                resources[currentTab].push(data);
                showMessage('Recurso creado correctamente', 'success');
            }
            
            closeModal();
            renderResources();
        });

        // Google Translate con idioma por defecto del sistema
        function initializeGoogleTranslate() {
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: DEFAULT_LANGUAGE,
                    includedLanguages: 'en,fr,pt,it,de,es',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');

                setTimeout(loadSavedLanguage, 1000);
            }

            function saveLanguage(lang) {
                sessionStorage.setItem('language', lang);
                localStorage.setItem('preferredLanguage', lang);
            }

            function loadSavedLanguage() {
                const saved = sessionStorage.getItem('language') || 
                             localStorage.getItem('preferredLanguage') || 
                             DEFAULT_LANGUAGE;
                
                if (saved && saved !== DEFAULT_LANGUAGE) {
                    const select = document.querySelector('.goog-te-combo');
                    if (select) {
                        select.value = saved;
                        select.dispatchEvent(new Event('change'));
                    }
                }
            }

            if (!window.googleTranslateElementInit) {
                window.googleTranslateElementInit = googleTranslateElementInit;
                const script = document.createElement('script');
                script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                document.head.appendChild(script);
            }

            setTimeout(function() {
                const select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.addEventListener('change', function() {
                        if (this.value) saveLanguage(this.value);
                    });
                }
            }, 2000);
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('resourceModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

    <!-- Script del sistema de autocompletado -->
    <script src="<?= APP_URL ?>/assets/js/location-autocomplete.js"></script>
    
    <script>
        // =====================================
        // INTEGRACIÓN CON EL SISTEMA EXISTENTE
        // =====================================
        
(function() {
    // Guardar referencias a las funciones originales
    const originalOpenModal = window.openModal;
    const originalCloseModal = window.closeModal;
    
    // Sobrescribir openModal con mejoras
    window.openModal = function(mode, id = null) {
        console.log('🎭 Abriendo modal mejorado:', mode, id);
        
        // Llamar función original
        if (originalOpenModal) {
            originalOpenModal.call(this, mode, id);
        }
        
        // Aplicar mejoras visuales
        setTimeout(() => {
            enhanceModalAppearance();
            addModalAnimations();
            setupFormValidation();
            setupImageUploadEnhancements();
            
            // Enfocar primer campo
            const firstInput = document.querySelector('.modal.show input:not([type="hidden"])');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    };
    
    // Sobrescribir closeModal con mejoras
    window.closeModal = function() {
        console.log('🎭 Cerrando modal mejorado');
        
        const modal = document.getElementById('resourceModal');
        if (modal && modal.classList.contains('show')) {
            // Animación de cierre
            modal.style.animation = 'modalFadeOut 0.3s ease-out forwards';
            
            setTimeout(() => {
                // Llamar función original después de la animación
                if (originalCloseModal) {
                    originalCloseModal.call(this);
                }
                
                // Limpiar estado
                clearFormValidation();
                modal.style.animation = '';
            }, 300);
        } else if (originalCloseModal) {
            originalCloseModal.call(this);
        }
    };
})();

function enhanceModalAppearance() {
    const modal = document.getElementById('resourceModal');
    if (!modal) return;
    
    // Añadir clase de tema
    modal.classList.add('enhanced-modal');
    
    // Mejorar el título con iconos
    const title = document.getElementById('modalTitle');
    if (title) {
        const currentTab = window.currentTab || 'dias';
        const icons = {
            'dias': '📅',
            'alojamientos': '🏨', 
            'actividades': '🎯',
            'transportes': '🚗'
        };
        
        if (!title.textContent.includes(icons[currentTab])) {
            title.textContent = `${icons[currentTab]} ${title.textContent}`;
        }
    }
    
    // Mejorar labels con iconos
    enhanceFormLabels();
}

function enhanceFormLabels() {
    const labelIcons = {
        'idioma': '🌐',
        'titulo': '📝',
        'nombre': '🏷️',
        'ubicacion': '📍',
        'descripcion': '📄',
        'tipo': '🏷️',
        'categoria': '⭐',
        'sitio_web': '🌐',
        'medio': '🚗',
        'lugar_salida': '🛫',
        'lugar_llegada': '🛬',
        'duracion': '⏱️',
        'distancia_km': '📏',
        'precio': '💰'
    };
    
    Object.keys(labelIcons).forEach(fieldName => {
        const label = document.querySelector(`label[for="${fieldName}"]`);
        if (label && !label.textContent.includes(labelIcons[fieldName])) {
            label.innerHTML = `${labelIcons[fieldName]} ${label.textContent}`;
        }
    });
}

// Función para añadir animaciones al modal
function addModalAnimations() {
    const modal = document.getElementById('resourceModal');
    if (!modal) return;
    
    // Animación de entrada para elementos internos
    const formGroups = modal.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        group.style.animation = `slideInUp 0.4s ease-out ${index * 0.05}s forwards`;
    });
    
    // Añadir CSS de animación
    if (!document.getElementById('toast-animations')) {
        const style = document.createElement('style');
        style.id = 'toast-animations';
        style.textContent = `
            @keyframes slideInFromRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            @keyframes slideOutToRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Función para limpiar validación del formulario
function clearFormValidation() {
    const form = document.getElementById('resourceForm');
    if (!form) return;
    
    // Remover clases de estado
    form.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('error', 'success', 'loading');
    });
    
    // Remover mensajes
    form.querySelectorAll('.field-message').forEach(message => {
        message.remove();
    });
}

// Función para mejorar la subida de imágenes
function setupImageUploadEnhancements() {
    const imageUploads = document.querySelectorAll('.image-upload');
    
    imageUploads.forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        if (!input) return;
        
        // Drag & Drop mejorado
        setupDragAndDrop(upload, input);
        
        // Preview mejorado
        input.addEventListener('change', function() {
            handleImagePreviewEnhanced(this, upload);
        });
        
        // Indicador de progreso
        setupProgressIndicator(upload, input);
    });
}

// Función para configurar drag & drop
function setupDragAndDrop(uploadArea, input) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.add('drag-over');
            uploadArea.style.borderColor = 'var(--primary-color, #667eea)';
            uploadArea.style.background = 'linear-gradient(135deg, #f0f4ff 0%, #e6f3ff 100%)';
            uploadArea.style.transform = 'scale(1.02)';
        });
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.remove('drag-over');
            uploadArea.style.borderColor = '';
            uploadArea.style.background = '';
            uploadArea.style.transform = '';
        });
    });
    
    uploadArea.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            handleImagePreviewEnhanced(input, uploadArea);
        }
    });
}

// Función para preview de imagen mejorado
function handleImagePreviewEnhanced(input, uploadArea) {
    const file = input.files[0];
    if (!file) return;
    
    // Validar archivo
    if (!file.type.startsWith('image/')) {
        showUploadError(uploadArea, 'Solo se permiten archivos de imagen');
        input.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        showUploadError(uploadArea, 'El archivo es demasiado grande (máx. 5MB)');
        input.value = '';
        return;
    }
    
    // Mostrar indicador de carga
    showUploadProgress(uploadArea);
    
    // Crear preview
    const reader = new FileReader();
    reader.onload = function(e) {
        setTimeout(() => { // Simular tiempo de procesamiento
            showImagePreview(uploadArea, e.target.result, file.name);
        }, 800);
    };
    
    reader.onerror = function() {
        showUploadError(uploadArea, 'Error al leer el archivo');
    };
    
    reader.readAsDataURL(file);
}

// Función para mostrar progreso de subida
function showUploadProgress(uploadArea) {
    const content = uploadArea.querySelector('.upload-content');
    if (!content) return;
    
    const originalContent = content.innerHTML;
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
            <div style="width: 40px; height: 40px; border: 3px solid #e2e8f0; border-top: 3px solid var(--primary-color, #667eea); border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <div style="font-weight: 600; color: #4a5568;">Procesando imagen...</div>
            <div style="width: 100%; background: #e2e8f0; border-radius: 10px; height: 6px; overflow: hidden;">
                <div style="height: 100%; background: linear-gradient(90deg, var(--primary-color, #667eea), var(--secondary-color, #764ba2)); width: 0%; animation: progressBar 0.8s ease-out forwards;"></div>
            </div>
        </div>
    `;
    
    // Añadir CSS de progreso
    if (!document.getElementById('progress-animations')) {
        const style = document.createElement('style');
        style.id = 'progress-animations';
        style.textContent = `
            @keyframes progressBar {
                to { width: 100%; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Función para mostrar preview de imagen
function showImagePreview(uploadArea, imageSrc, fileName) {
    const content = uploadArea.querySelector('.upload-content');
    if (!content) return;
    
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; width: 100%;">
            <div style="position: relative; border-radius: 12px; overflow: hidden; max-width: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <img src="${imageSrc}" alt="${fileName}" style="max-width: 150px; max-height: 100px; object-fit: cover; border-radius: 12px;">
                <div style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.7); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px;" onclick="clearImagePreview(this)">×</div>
            </div>
            <div style="text-align: center;">
                <div style="font-weight: 600; color: #2d3748; font-size: 14px;">✅ Imagen cargada</div>
                <div style="font-size: 12px; color: #718096; margin-top: 2px;">${fileName}</div>
            </div>
        </div>
    `;
    
    // Animación de entrada
    const img = content.querySelector('img');
    if (img) {
        img.style.opacity = '0';
        img.style.transform = 'scale(0.8)';
        img.style.transition = 'all 0.3s ease';
        
        setTimeout(() => {
            img.style.opacity = '1';
            img.style.transform = 'scale(1)';
        }, 50);
    }
}

// Función para limpiar preview de imagen
function clearImagePreview(button) {
    const uploadArea = button.closest('.image-upload');
    const input = uploadArea.querySelector('input[type="file"]');
    const content = uploadArea.querySelector('.upload-content');
    
    if (input) input.value = '';
    
    if (content) {
        // Obtener el tipo de campo para restaurar contenido original
        const fieldName = input.name;
        const icons = {
            'imagen': '📷',
            'imagen1': '📷',
            'imagen2': '📷', 
            'imagen3': '📷'
        };
        
        content.innerHTML = `
            <div style="font-size: 32px; margin-bottom: 8px;">${icons[fieldName] || '📷'}</div>
            <div>Subir Imagen</div>
            <div style="font-size: 12px; color: #718096;">Click para seleccionar archivo</div>
        `;
    }
    
    // Remover errores
    const errorMsg = uploadArea.querySelector('.upload-error');
    if (errorMsg) errorMsg.remove();
}

// Función para mostrar error de subida
function showUploadError(uploadArea, message) {
    const content = uploadArea.querySelector('.upload-content');
    if (!content) return;
    
    // Restaurar contenido original con error
    const input = uploadArea.querySelector('input[type="file"]');
    const fieldName = input?.name || 'imagen';
    
    content.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <div style="font-size: 32px; color: #e53e3e;">⚠️</div>
            <div style="font-weight: 600; color: #e53e3e;">Error</div>
            <div style="font-size: 12px; color: #c53030; text-align: center;">${message}</div>
            <button type="button" onclick="clearImagePreview(this)" style="background: #fed7d7; color: #c53030; border: 1px solid #feb2b2; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                Intentar de nuevo
            </button>
        </div>
    `;
    
    // Animación de error
    uploadArea.style.animation = 'shake 0.5s ease-in-out';
    setTimeout(() => {
        uploadArea.style.animation = '';
    }, 500);
}

// Función para configurar indicador de progreso
function setupProgressIndicator(uploadArea, input) {
    // Esta función se puede expandir para mostrar progreso real de subida
    // Por ahora solo maneja la interfaz visual
}

// Funciones de validación auxiliares
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

// Función para mejorar el botón de envío
function enhanceSubmitButton() {
    const form = document.getElementById('resourceForm');
    const submitBtn = form?.querySelector('button[type="submit"]');
    
    if (!submitBtn) return;
    
    // Guardar texto original
    const originalText = submitBtn.textContent;
    
    // Mejorar estado de carga
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.style.position = 'relative';
        submitBtn.innerHTML = `
            <span style="opacity: 0.7;">${originalText}</span>
            <div style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top: 2px solid white; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        `;
        
        // Restaurar después de un tiempo (esto debería manejarse en el callback real)
        setTimeout(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }, 3000);
    });
}

// Función para mejorar navegación por teclado
function enhanceKeyboardNavigation() {
    const modal = document.getElementById('resourceModal');
    if (!modal) return;
    
    modal.addEventListener('keydown', function(e) {
        // ESC para cerrar
        if (e.key === 'Escape') {
            closeModal();
            return;
        }
        
        // Tab mejorado
        if (e.key === 'Tab') {
            const focusableElements = modal.querySelectorAll(
                'input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])'
            );
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
        
        // Enter en campos que no sean textarea
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            const form = modal.querySelector('form');
            const submitBtn = form?.querySelector('button[type="submit"]');
            
            if (submitBtn && !submitBtn.disabled) {
                e.preventDefault();
                submitBtn.click();
            }
        }
    });
}

// Función de inicialización principal
function initializeModalEnhancements() {
    console.log('🎨 Inicializando mejoras de modals...');
    
    // Observar cuando se abre un modal
    const modal = document.getElementById('resourceModal');
    if (modal) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (modal.classList.contains('show')) {
                        setTimeout(() => {
                            enhanceSubmitButton();
                            enhanceKeyboardNavigation();
                        }, 150);
                    }
                }
            });
        });
        
        observer.observe(modal, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
    
    console.log('✅ Mejoras de modals inicializadas');
}

// Auto-inicialización
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeModalEnhancements);
} else {
    initializeModalEnhancements();
}

// Función para aplicar tema de colores dinámico
function applyDynamicTheme() {
    const root = document.documentElement;
    const primaryColor = getComputedStyle(root).getPropertyValue('--primary-color').trim();
    const secondaryColor = getComputedStyle(root).getPropertyValue('--secondary-color').trim();
    
    if (primaryColor && secondaryColor) {
        console.log('🎨 Aplicando tema dinámico:', { primaryColor, secondaryColor });
        
        // Los colores ya están definidos en CSS, solo necesitamos asegurar que se usen
        const style = document.createElement('style');
        style.id = 'dynamic-theme';
        style.textContent = `
            .modal-header {
                background: linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%);
            }
            
            .btn-primary {
                background: linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%);
                box-shadow: 0 4px 15px ${primaryColor}40;
            }
            
            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                border-color: ${primaryColor};
                box-shadow: 0 0 0 4px ${primaryColor}20, 0 4px 12px rgba(0, 0, 0, 0.08);
            }
        `;
        
        // Reemplazar si ya existe
        const existing = document.getElementById('dynamic-theme');
        if (existing) existing.remove();
        
        document.head.appendChild(style);
    }
}

// Aplicar tema al cargar
document.addEventListener('DOMContentLoaded', applyDynamicTheme);

// Función global para debugging
window.debugModalEnhancements = function() {
    console.log('🔍 DEBUG - Modal Enhancements:', {
        modalExists: !!document.getElementById('resourceModal'),
        enhancementsLoaded: true,
        currentTab: window.currentTab,
        activeModals: document.querySelectorAll('.modal.show').length
    });
}; si no existe
    if (!document.getElementById('modal-animations')) {
        const style = document.createElement('style');
        style.id = 'modal-animations';
        style.textContent = `
            @keyframes slideInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes modalFadeOut {
                from {
                    opacity: 1;
                    backdrop-filter: blur(8px);
                }
                to {
                    opacity: 0;
                    backdrop-filter: blur(0px);
                }
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(style);
    }
}

// Función para configurar validación visual del formulario
function setupFormValidation() {
    const form = document.getElementById('resourceForm');
    if (!form) return;
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        // Remover listeners anteriores
        input.removeEventListener('blur', validateField);
        input.removeEventListener('input', clearFieldError);
        
        // Añadir nuevos listeners
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
        
        // Validación al enviar
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            inputs.forEach(field => {
                if (!validateField.call(field)) {
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                showValidationSummary();
                
                // Scroll al primer error
                const firstError = form.querySelector('.form-group.error');
                if (firstError) {
                    firstError.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }
        });
    });
}

// Función para validar un campo individual
function validateField() {
    const formGroup = this.closest('.form-group');
    if (!formGroup) return true;
    
    let isValid = true;
    let message = '';
    
    // Limpiar estado anterior
    formGroup.classList.remove('error', 'success');
    const existingMessage = formGroup.querySelector('.field-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Validaciones específicas
    if (this.hasAttribute('required') && !this.value.trim()) {
        isValid = false;
        message = 'Este campo es requerido';
    } else if (this.type === 'email' && this.value && !isValidEmail(this.value)) {
        isValid = false;
        message = 'Ingresa un email válido';
    } else if (this.type === 'url' && this.value && !isValidUrl(this.value)) {
        isValid = false;
        message = 'Ingresa una URL válida';
    } else if (this.type === 'number' && this.value && this.value < 0) {
        isValid = false;
        message = 'El valor no puede ser negativo';
    } else if (this.name === 'titulo' && this.value.trim().length < 3) {
        isValid = false;
        message = 'El título debe tener al menos 3 caracteres';
    }
    
    // Aplicar estado visual
    if (!isValid) {
        formGroup.classList.add('error');
        showFieldMessage(formGroup, message, 'error');
        
        // Animación de error
        this.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            this.style.animation = '';
        }, 500);
    } else if (this.value.trim()) {
        formGroup.classList.add('success');
    }
    
    return isValid;
}

// Función para limpiar errores al escribir
function clearFieldError() {
    const formGroup = this.closest('.form-group');
    if (formGroup) {
        formGroup.classList.remove('error');
        const errorMessage = formGroup.querySelector('.field-message.error');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

// Función para mostrar mensaje en campo
function showFieldMessage(formGroup, message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `field-message ${type}`;
    messageDiv.textContent = message;
    
    // Icono según tipo
    const icon = type === 'error' ? '⚠️' : '✅';
    messageDiv.textContent = `${icon} ${message}`;
    
    formGroup.appendChild(messageDiv);
}

function showValidationSummary() {
   const errors = document.querySelectorAll('.form-group.error');
   if (errors.length === 0) return;
   
   // Crear toast de error
   const toast = document.createElement('div');
   toast.style.cssText = `
       position: fixed;
       top: 20px;
       right: 20px;
       background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
       color: white;
       padding: 16px 20px;
       border-radius: 12px;
       box-shadow: 0 8px 25px rgba(229, 62, 62, 0.3);
       z-index: 10001;
       max-width: 350px;
       animation: slideInFromRight 0.3s ease-out;
   `;
   
   toast.innerHTML = `
       <div style="display: flex; align-items: center; gap: 12px;">
           <div style="font-size: 20px;">⚠️</div>
           <div>
               <div style="font-weight: 600; margin-bottom: 4px;">Revisa los campos</div>
               <div style="font-size: 13px; opacity: 0.9;">
                   ${errors.length} campo${errors.length > 1 ? 's' : ''} necesita${errors.length > 1 ? 'n' : ''} corrección
               </div>
           </div>
       </div>
   `;
   
   document.body.appendChild(toast);
   
   // Remover después de 4 segundos
   setTimeout(() => {
       toast.style.animation = 'slideOutToRight 0.3s ease-in';
       setTimeout(() => {
           if (document.body.contains(toast)) {
               document.body.removeChild(toast);
           }
       }, 300);
   }, 4000);
   
   // Añadir CSS de animación
   if (!document.getElementById('toast-animations')) {
       const style = document.createElement('style');
       style.id = 'toast-animations';
       style.textContent = `
           @keyframes slideInFromRight {
               from { transform: translateX(100%); opacity: 0; }
               to { transform: translateX(0); opacity: 1; }
           }
           
           @keyframes slideOutToRight {
               from { transform: translateX(0); opacity: 1; }
               to { transform: translateX(100%); opacity: 0; }
           }
       `;
       document.head.appendChild(style);
   }
}
    

        
        // Inicialización automática cuando se detecten campos
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📚 Biblioteca con SÚPER autocompletado lista');
            
            // Verificar si ya hay campos presentes
            const existingFields = document.querySelectorAll('#ubicacion, #lugar_salida, #lugar_llegada');
            if (existingFields.length > 0) {
                setTimeout(() => {
                    initializeSuperLocationAutocomplete();
                }, 500);
            }
        });

        // Función para debugging desde consola del navegador
        window.debugBibliotecaAutocomplete = function() {
            console.log('🔍 DEBUG INFO:', {
                autocompleteLoaded: !!window.superLocationAutocomplete,
                debugInfo: window.superLocationAutocomplete ? window.superLocationAutocomplete.getDebugInfo() : null,
                fieldsFound: document.querySelectorAll('#ubicacion, #lugar_salida, #lugar_llegada').length
            });
        };

        // Agregar los estilos adicionales
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = additionalCSS;
    document.head.appendChild(style);
});
</script>
<script>

// Función mejorada para actualizar mapa cuando se selecciona ubicación
function updateMapWithSelectedLocation(location, coordinates) {
    console.log('📍 Actualizando mapa con ubicación seleccionada:', location);
    
    if (!map) {
        console.warn('⚠️ Mapa no disponible');
        return;
    }
    
    try {
        const lat = coordinates.lat || coordinates.latitude || coordinates[0];
        const lng = coordinates.lng || coordinates.longitude || coordinates[1];
        
        if (!lat || !lng) {
            console.warn('⚠️ Coordenadas no válidas:', coordinates);
            return;
        }
        
        // Animar hacia la nueva ubicación
        map.flyTo([lat, lng], 16, {
            animate: true,
            duration: 1.5
        });
        
        // Remover marcador anterior
        if (window.currentMarker) {
            map.removeLayer(window.currentMarker);
        }
        
        // Crear nuevo marcador
        window.currentMarker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);
        
        // Popup informativo
        window.currentMarker.bindPopup(`
            <div style="text-align: center;">
                <strong>📍 ${location}</strong><br>
                <small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
            </div>
        `).openPopup();
        
        // Actualizar campos ocultos de coordenadas
        updateCoordinateFields(lat, lng);
        
        // Event listener para arrastrar
        window.currentMarker.on('dragend', function(e) {
            const newCoords = e.target.getLatLng();
            updateCoordinateFields(newCoords.lat, newCoords.lng);
            reverseGeocodeOSM(newCoords.lat, newCoords.lng);
        });
        
        console.log('✅ Mapa actualizado correctamente');
        
    } catch (error) {
        console.error('❌ Error actualizando mapa:', error);
    }
}

// Función para actualizar campos de coordenadas
function updateCoordinateFields(lat, lng) {
    const latField = document.getElementById('latitud');
    const lngField = document.getElementById('longitud');
    
    if (latField) latField.value = lat;
    if (lngField) lngField.value = lng;
    
    console.log('📍 Coordenadas actualizadas:', { lat, lng });
}

// Mejorar el autocompletado de ubicaciones para días, alojamientos y actividades
function setupAdvancedLocationAutocomplete() {
    const locationFields = ['ubicacion', 'lugar_salida', 'lugar_llegada'];
    
    locationFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        let searchTimeout;
        let suggestionsList;
        
        field.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            // Limpiar timeout anterior
            clearTimeout(searchTimeout);
            
            // Remover sugerencias anteriores
            if (suggestionsList) {
                suggestionsList.remove();
                suggestionsList = null;
            }
            
            if (query.length < 3) return;
            
            // Buscar después de 300ms
            searchTimeout = setTimeout(() => {
                searchLocationWithCoordinates(query, field, fieldId);
            }, 300);
        });
        
        // Limpiar sugerencias al salir del campo
        field.addEventListener('blur', function() {
            setTimeout(() => {
                if (suggestionsList) {
                    suggestionsList.remove();
                    suggestionsList = null;
                }
            }, 200);
        });
    });
}

// Función mejorada para buscar ubicaciones con coordenadas
async function searchLocationWithCoordinates(query, field, fieldType) {
    try {
        console.log('🔍 Buscando ubicación:', query);
        
        // Mostrar indicador simple
        field.style.background = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'2\'%3E%3Cpath d=\'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z\'/%3E%3Ccircle cx=\'12\' cy=\'10\' r=\'3\'/%3E%3C/svg%3E") no-repeat right 12px center';
        field.style.backgroundSize = '16px 16px';
        field.style.backgroundColor = '#f8fafc';
        
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?` +
            `q=${encodeURIComponent(query)}&` +
            `format=json&` +
            `limit=5&` +
            `addressdetails=1&` +
            `accept-language=es`
        );
        
        if (!response.ok) throw new Error('Error en la búsqueda');
        
        const results = await response.json();
        console.log('📍 Resultados encontrados:', results.length);
        
        // Restaurar estilo normal
        field.style.background = '';
        field.style.backgroundColor = '';
        
        if (results.length > 0) {
            showLocationSuggestions(results, field, fieldType);
        }
        
    } catch (error) {
        console.error('❌ Error buscando ubicación:', error);
        // Restaurar estilo normal en caso de error
        field.style.background = '';
        field.style.backgroundColor = '';
    }
}

// Mostrar sugerencias de ubicación mejoradas
function showLocationSuggestions(results, field, fieldType) {
    console.log('📋 Mostrando sugerencias:', results.length);
    
    // Remover lista anterior
    removeSuggestions();
    
    suggestionsList = document.createElement('div');
    suggestionsList.className = 'location-suggestions';
    suggestionsList.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    `;
    
    results.forEach((result, index) => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.style.cssText = `
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f7fafc;
            transition: all 0.2s ease;
        `;
        
        item.innerHTML = `
            <div style="font-weight: 600; color: #2d3748;">${result.display_name.split(',')[0]}</div>
            <div style="font-size: 12px; color: #718096;">${result.display_name}</div>
        `;
        
        item.addEventListener('mouseenter', () => {
            item.style.backgroundColor = '#f8fafc';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.backgroundColor = '';
        });
        
        item.addEventListener('click', () => {
            selectLocationWithMap(result, field, fieldType);
            removeSuggestions();
        });
        
        suggestionsList.appendChild(item);
    });
    
    // Posicionar la lista
    field.parentElement.style.position = 'relative';
    field.parentElement.appendChild(suggestionsList);
    
    console.log('✅ Sugerencias mostradas correctamente');
}
// Función para seleccionar ubicación y actualizar mapa automáticamente
function selectLocationWithMap(location, field, fieldType) {
    console.log('✅ Ubicación seleccionada:', location.display_name);
    
    // Actualizar campo de texto
    field.value = location.display_name;
    
    // Coordenadas
    const lat = parseFloat(location.lat);
    const lng = parseFloat(location.lon);
    
    // AUTOMÁTICAMENTE actualizar el mapa con la nueva ubicación
    updateMapWithSelectedLocation(location.display_name, { lat, lng });
    
    // Disparar evento personalizado
    field.dispatchEvent(new CustomEvent('locationSelected', {
        detail: {
            location: location,
            coordinates: { lat, lng },
            fieldType: fieldType
        }
    }));
}

// Función para obtener icono según tipo de ubicación
function getLocationIcon(location) {
    const type = location.type || '';
    const category = location.category || '';
    
    const icons = {
        city: '🏙️',
        town: '🏘️',
        village: '🏡',
        country: '🌍',
        hotel: '🏨',
        restaurant: '🍽️',
        airport: '✈️',
        station: '🚂',
        museum: '🏛️',
        park: '🌳',
        beach: '🏖️',
        mountain: '⛰️'
    };
    
    return icons[type] || icons[category] || '📍';
}

// ===== CORRECCIÓN 2: IMAGEN CORRECTA PARA TRANSPORTES =====

// Función para obtener icono correcto del medio de transporte
function getTransportIcon(medio) {
    const transportIcons = {
        'bus': '🚌',
        'avion': '✈️',
        'coche': '🚗',
        'barco': '🚢',
        'tren': '🚂',
        'metro': '🚇',
        'taxi': '🚕',
        'bicicleta': '🚲',
        'moto': '🏍️',
        'walking': '🚶'
    };
    
    return transportIcons[medio] || '🚗';
}

// Función mejorada para crear card de transporte
function createTransportCard(item) {
    const transportIcon = getTransportIcon(item.medio);
    const title = item.titulo || 'Transporte';
    const route = `${item.lugar_salida || 'Origen'} → ${item.lugar_llegada || 'Destino'}`;
    
    return `
        <div class="item-card transport-card" onclick="editResource(${item.id}, 'transportes')">
            <div class="card-image transport-image">
                <div class="transport-icon">${transportIcon}</div>
                <div class="transport-type">${item.medio || 'Transporte'}</div>
            </div>
            <div class="card-content">
                <h3 class="card-title">${escapeHtml(title)}</h3>
                <p class="card-description">${escapeHtml(item.descripcion || 'Sin descripción')}</p>
                <div class="card-route">🛣️ ${escapeHtml(route)}</div>
                ${item.duracion ? `<div class="card-duration">⏱️ ${escapeHtml(item.duracion)}</div>` : ''}
                ${item.precio ? `<div class="card-price">💰 ${escapeHtml(item.precio)}</div>` : ''}
            </div>
            <div class="card-actions">
                <button class="action-btn edit" onclick="event.stopPropagation(); editResource(${item.id})">
                    ✏️ Editar
                </button>
                <button class="action-btn delete" onclick="event.stopPropagation(); deleteResource(${item.id})">
                    🗑️ Eliminar
                </button>
            </div>
        </div>
    `;
}
// CSS específico para cards de transporte
const transportCardCSS = `
<style>
.transport-card .card-image {
    background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    text-align: center;
    padding: 20px;
}

.transport-icon {
    font-size: 36px;
    margin-bottom: 8px;
    animation: bounce 2s infinite;
}

.transport-type {
    font-size: 14px;
    font-weight: 600;
    text-transform: capitalize;
    opacity: 0.9;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

.card-route {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
    font-weight: 500;
}

.card-duration,
.card-price {
    font-size: 12px;
    color: #888;
    margin-top: 3px;
}
</style>
`;

// ===== CORRECCIÓN 3: CLICK DIRECTO PARA ABRIR DETALLES =====

// Función para ver detalles del recurso (reemplaza el alert)
function viewResourceDetails(id, type) {
    console.log(`📋 Abriendo detalles del ${type} con ID: ${id}`);
    
    try {
        // Buscar el recurso en los datos
        const resource = resources[type]?.find(item => item.id === id);
        
        if (!resource) {
            showErrorMessage(`No se encontró el recurso con ID: ${id}`);
            return;
        }
        
        // Crear modal de detalles
        showResourceDetailsModal(resource, type);
        
    } catch (error) {
        console.error('❌ Error abriendo detalles:', error);
        showErrorMessage('Error al cargar los detalles del recurso');
    }
}

// Función para mostrar modal de detalles del recurso
function showResourceDetailsModal(resource, type) {
    // Crear overlay del modal
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'resource-details-modal-overlay';
    modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    `;
    
    // Crear contenido del modal
    const modalContent = document.createElement('div');
    modalContent.className = 'resource-details-modal';
    modalContent.style.cssText = `
        background: white;
        border-radius: 16px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    `;
    
    // Generar contenido según el tipo
    modalContent.innerHTML = generateResourceDetailsContent(resource, type);
    
    modalOverlay.appendChild(modalContent);
    
    // Cerrar modal al hacer click en el overlay
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeResourceDetailsModal(modalOverlay);
        }
    });
    
    // Cerrar con ESC
    document.addEventListener('keydown', function escapeHandler(e) {
        if (e.key === 'Escape') {
            closeResourceDetailsModal(modalOverlay);
            document.removeEventListener('keydown', escapeHandler);
        }
    });
    
    document.body.appendChild(modalOverlay);
}

// Función para generar contenido del modal de detalles
function generateResourceDetailsContent(resource, type) {
    const typeConfig = {
        'dias': {
            icon: '📅',
            title: 'Detalles del Día',
            fields: [
                { key: 'titulo', label: 'Título', icon: '📝' },
                { key: 'ubicacion', label: 'Ubicación', icon: '📍' },
                { key: 'descripcion', label: 'Descripción', icon: '📄' },
                { key: 'idioma', label: 'Idioma', icon: '🌐' }
            ]
        },
        'alojamientos': {
            icon: '🏨',
            title: 'Detalles del Alojamiento',
            fields: [
                { key: 'nombre', label: 'Nombre', icon: '🏨' },
                { key: 'tipo', label: 'Tipo', icon: '🏷️' },
                { key: 'categoria', label: 'Categoría', icon: '⭐' },
                { key: 'ubicacion', label: 'Ubicación', icon: '📍' },
                { key: 'descripcion', label: 'Descripción', icon: '📄' },
                { key: 'sitio_web', label: 'Sitio Web', icon: '🌐' }
            ]
        },
        'actividades': {
            icon: '🎯',
            title: 'Detalles de la Actividad',
            fields: [
                { key: 'titulo', label: 'Título', icon: '🎯' },
                { key: 'ubicacion', label: 'Ubicación', icon: '📍' },
                { key: 'descripcion', label: 'Descripción', icon: '📄' },
                { key: 'duracion', label: 'Duración', icon: '⏱️' },
                { key: 'precio', label: 'Precio', icon: '💰' }
            ]
        },
        'transportes': {
            icon: '🚗',
            title: 'Detalles del Transporte',
            fields: [
                { key: 'titulo', label: 'Título', icon: '📝' },
                { key: 'medio', label: 'Medio de Transporte', icon: '🚗' },
                { key: 'lugar_salida', label: 'Lugar de Salida', icon: '🛫' },
                { key: 'lugar_llegada', label: 'Lugar de Llegada', icon: '🛬' },
                { key: 'duracion', label: 'Duración', icon: '⏱️' },
                { key: 'precio', label: 'Precio', icon: '💰' },
                { key: 'descripcion', label: 'Descripción', icon: '📄' }
            ]
        }
    };
    
    const config = typeConfig[type];
    if (!config) return '<p>Tipo de recurso no reconocido</p>';
    
    // Header del modal
    let html = `
        <div style="padding: 24px; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px;">${config.icon}</span>
                    <h2 style="margin: 0; color: #1a202c;">${config.title}</h2>
                </div>
                <button onclick="closeResourceDetailsModal(this.closest('.resource-details-modal-overlay'))" 
                        style="background: none; border: none; font-size: 24px; cursor: pointer; color: #718096;">
                    ×
                </button>
            </div>
        </div>
        
        <div style="padding: 24px;">
    `;
    
    // Imágenes
    const images = getResourceImages(resource, type);
    if (images.length > 0) {
        html += `
            <div style="margin-bottom: 24px;">
                <h3 style="margin-bottom: 12px; color: #2d3748;">📷 Imágenes</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    ${images.map(img => `
                        <img src="${img}" alt="Imagen" 
                             style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer;"
                             onclick="showImageModal('${img}', '${escapeHtml(resource.titulo || resource.nombre || 'Imagen')}')">
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    // Campos de información
    html += '<div style="display: grid; gap: 16px;">';
    
    config.fields.forEach(field => {
        const value = resource[field.key];
        if (value) {
            let displayValue = value;
            
            // Formateo especial para ciertos campos
            if (field.key === 'categoria') {
                displayValue = `${'⭐'.repeat(parseInt(value))} (${value} estrellas)`;
            } else if (field.key === 'sitio_web') {
                displayValue = `<a href="${value}" target="_blank" style="color: var(--primary-color, #667eea); text-decoration: none;">${value}</a>`;
            } else if (field.key === 'medio') {
                displayValue = `${getTransportIcon(value)} ${value}`;
            }
            
            html += `
                <div style="display: flex; align-items: start; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <span style="font-size: 18px; margin-top: 2px;">${field.icon}</span>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2d3748; margin-bottom: 4px;">${field.label}</div>
                        <div style="color: #4a5568;">${displayValue}</div>
                    </div>
                </div>
            `;
        }
    });
    
    html += '</div>';
    
    // Coordenadas si existen
    if (resource.latitud && resource.longitud) {
        html += `
            <div style="margin-top: 24px; padding: 16px; background: #edf2f7; border-radius: 8px;">
                <h4 style="margin: 0 0 8px 0; color: #2d3748;">📍 Coordenadas</h4>
                <div style="font-family: monospace; color: #4a5568;">
                    Latitud: ${resource.latitud}<br>
                    Longitud: ${resource.longitud}
                </div>
            </div>
        `;
    }
    
    // Botones de acción
    html += `
        </div>
        <div style="padding: 24px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; justify-content: flex-end;">
            <button onclick="editResource(${resource.id}); closeResourceDetailsModal(this.closest('.resource-details-modal-overlay'));"
                    style="background: var(--primary-color, #667eea); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                ✏️ Editar
            </button>
            <button onclick="closeResourceDetailsModal(this.closest('.resource-details-modal-overlay'))"
                    style="background: #e2e8f0; color: #4a5568; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                Cerrar
            </button>
        </div>
    `;
    
    return html;
}

// Función para obtener imágenes del recurso
function getResourceImages(resource, type) {
    const images = [];
    
    switch(type) {
        case 'dias':
        case 'actividades':
            if (resource.imagen1) images.push(resource.imagen1);
            if (resource.imagen2) images.push(resource.imagen2);
            if (resource.imagen3) images.push(resource.imagen3);
            break;
        case 'alojamientos':
            if (resource.imagen) images.push(resource.imagen);
            break;
    }
    
    return images;
}

// Función para cerrar modal de detalles
function closeResourceDetailsModal(modalOverlay) {
    modalOverlay.style.animation = 'fadeOut 0.3s ease';
    setTimeout(() => {
        if (modalOverlay.parentElement) {
            modalOverlay.remove();
        }
    }, 300);
}

// Función para mostrar mensajes de error
function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #fed7d7;
        color: #e53e3e;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        z-index: 10001;
        animation: slideInRight 0.3s ease;
    `;
    toast.textContent = `❌ ${message}`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// ===== INICIALIZACIÓN =====

// Función para inicializar todas las correcciones
function initializeBibliotecaFixes() {
    console.log('🔧 Inicializando correcciones de Biblioteca...');
    
    // Agregar CSS para transportes
    document.head.insertAdjacentHTML('beforeend', transportCardCSS);
    
    // Configurar autocompletado avanzado
    setupAdvancedLocationAutocomplete();
    
    // Agregar estilos para animaciones
    const animationCSS = `
        <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: scale(0.9) translateY(20px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', animationCSS);
    
    console.log('✅ Correcciones de Biblioteca inicializadas');
}

// Sobrescribir la función createResourceCard para usar las nuevas funciones
const originalCreateResourceCard = window.createResourceCard;
window.createResourceCard = function(item) {
    if (currentTab === 'transportes') {
        return createTransportCard(item);
    }
    
    // Para otros tipos, usar la función original pero con click mejorado
    const card = originalCreateResourceCard ? originalCreateResourceCard(item) : '';
    return card.replace(
        'onclick="viewResource(',
        'onclick="viewResourceDetails('
    ).replace(
        '"viewResource(',
        '"viewResourceDetails('
    );
};

// Sobrescribir viewResource para usar la nueva función
window.viewResource = function(id) {
    viewResourceDetails(id, currentTab);
};

// Inicializar al cargar el DOM
document.addEventListener('DOMContentLoaded', initializeBibliotecaFixes);

// También inicializar si ya está cargado
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBibliotecaFixes);
} else {
    initializeBibliotecaFixes();
}
const style = document.createElement('style');
style.textContent = `
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
`;
document.head.appendChild(style);
</script>
<!-- Agregar antes del cierre de </body> -->
<a href="<?= APP_URL ?>/itinerarios" class="floating-itinerarios-btn" title="Ir a Itinerarios">
    <span class="btn-icon">🗺️</span>
    <span class="btn-text">ITINERARIOS</span>
</a>
</body>
</html>