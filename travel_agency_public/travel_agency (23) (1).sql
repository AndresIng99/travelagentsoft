-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2025 a las 03:05:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `travel_agency`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_actividades`
--

CREATE TABLE `biblioteca_actividades` (
  `id` int(11) NOT NULL,
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `imagen1` varchar(255) DEFAULT NULL,
  `imagen2` varchar(255) DEFAULT NULL,
  `imagen3` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `biblioteca_actividades`
--

INSERT INTO `biblioteca_actividades` (`id`, `idioma`, `nombre`, `descripcion`, `ubicacion`, `latitud`, `longitud`, `imagen1`, `imagen2`, `imagen3`, `activo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'es', 'Tour Eiffel', 'Visita guiada a la Torre Eiffel con subida incluida', 'París, Francia', 48.85840000, 2.29450000, NULL, NULL, NULL, 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:38:12'),
(2, 'es', 'Coliseo Romano', 'Entrada y visita guiada al Coliseo de Roma', 'Roma, Italia', 41.89020000, 12.49220000, NULL, NULL, NULL, 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:38:14'),
(3, 'es', 'Visita Guiada al Museo del Louvre', 'Visita de 3 horas al museo más visitado del mundo. Recorrido por las obras maestras: Mona Lisa, Venus de Milo, Victoria de Samotracia. Guía experto en arte e historia, grupos reducidos de máximo 15 personas.', 'Rue de Rivoli, Quartier Saint-Merri, 4th Arrondissement, París, Francia metropolitana, 75004, Francia', 48.85721600, 2.35264400, 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_3_imagen1_1750801971.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_3_imagen2_1750801971.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_3_imagen3_1750801971.jpg', 1, 1, '2025-06-24 21:52:51', '2025-06-24 21:52:51'),
(4, 'es', 'Centro de Vida Marina Santa Marta - Delfines SM', 'El Acuario y museo del mar del Rodadero es un acuario público y un museo marítimo situado en la ensenada Inca Inca frente a la playa El Rodadero en Santa Marta. Fue inaugurado en 1965 por el capitán Francisco Ospina Navia', 'Santa Marta, Magdalena, Colombia', 11.08665600, -73.88035200, 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_4_imagen1_1751069504.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_4_imagen2_1751069504.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/actividades/2025/06/actividades_4_imagen3_1751069504.jpg', 1, 1, '2025-06-28 00:11:44', '2025-06-28 00:12:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_alojamientos`
--

CREATE TABLE `biblioteca_alojamientos` (
  `id` int(11) NOT NULL,
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `tipo` enum('hotel','camping','casa_huespedes','crucero','lodge','atipico','campamento','camping_car','tren') NOT NULL,
  `categoria` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `biblioteca_alojamientos`
--

INSERT INTO `biblioteca_alojamientos` (`id`, `idioma`, `nombre`, `descripcion`, `ubicacion`, `latitud`, `longitud`, `tipo`, `categoria`, `imagen`, `sitio_web`, `activo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'es', 'Hotel París Centro', 'Hotel 4 estrellas en el centro de París', 'París, Francia', 48.85660000, 2.35220000, 'hotel', 4, NULL, NULL, 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:38:03'),
(2, 'es', 'Camping Costa Brava', 'Camping familiar cerca de la playa', 'Costa Brava, España', 41.97940000, 3.04410000, 'camping', 3, NULL, NULL, 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:38:08'),
(3, 'es', 'Hotel Ritz París Plaza Vendôme', 'Hotel icónico de 5 estrellas en el corazón de París, frente a la Place Vendôme. Habitaciones elegantes con vista a los jardines de las Tullerías, spa de lujo, restaurante estrella Michelin.', 'Le Ritz, Place Vendôme, Quartier Vendôme, 1st Arrondissement, París, Francia metropolitana, 75001, Francia', 48.86796900, 2.32884500, 'hotel', 5, 'http://localhost/travel_agency/assets/uploads/biblioteca/alojamientos/2025/06/alojamientos_3_imagen_1750801789.jpg', 'https://www.ritzparis.com', 1, 1, '2025-06-24 21:49:49', '2025-06-24 21:49:49'),
(4, 'es', 'Hotel Hilton Santa Marta, Colombia', 'Nuestro hotel se encuentra en National Route 90, con vista al mar Caribe y a unos pasos de la playa. Santa Marta está a 13 km de distancia, rodeado de hermosas playas, donde se encuentra el Parque de los Novios. Estamos cerca de las montañas de Sierra Nevada y de las antiguas ruinas del Parque Nacional Tayrona. Disfrute de la cocina colombiana contemporánea en el hotel y de nuestro versátil espacio de reuniones.', 'Hilton Garden Inn Santa Marta, Bastidas, Localidad 2 Histórica - Rodrigo de Bastidas, Perímetro Urbano Santa Marta, Santa Marta, Magdalena, 470002, Colombia', 11.23907500, -74.21635900, 'hotel', 5, 'http://localhost/travel_agency/assets/uploads/biblioteca/alojamientos/2025/06/alojamientos_4_imagen_1751069405.jpg', 'https://www.hilton.com/es/hotels/smrcnhh-hilton-santa-marta/', 1, 1, '2025-06-28 00:10:05', '2025-06-28 00:10:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_dias`
--

CREATE TABLE `biblioteca_dias` (
  `id` int(11) NOT NULL,
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `imagen1` varchar(255) DEFAULT NULL,
  `imagen2` varchar(255) DEFAULT NULL,
  `imagen3` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `biblioteca_dias`
--

INSERT INTO `biblioteca_dias` (`id`, `idioma`, `titulo`, `descripcion`, `ubicacion`, `latitud`, `longitud`, `imagen1`, `imagen2`, `imagen3`, `activo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'es', 'Día en París', 'Recorrido completo por los principales monumentos de París', 'París, Francia', 48.85660000, 2.35220000, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_1_dia-en-paris_imagen1_20250624161620.png', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_1_dia-en-paris_imagen2_20250624161620.png', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_1_dia-en-paris_imagen3_20250624161620.png', 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:37:55'),
(2, 'es', 'Día en Roma', 'Visita al Coliseo, Foro Romano y Vaticano', 'Roma, Italia', 41.90280000, 12.49640000, NULL, NULL, NULL, 0, 1, '2025-06-24 16:12:22', '2025-06-24 21:37:57'),
(3, 'es', 'Día en París', 'paris', 'París, Francia', 48.85660000, 2.35220000, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_3_imagen1_1750801038.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_3_imagen2_1750801038.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_3_imagen3_1750801038.jpg', 0, 1, '2025-06-24 21:05:38', '2025-06-24 21:37:51'),
(4, 'es', 'Día Completo en París - Monumentos Históricos', 'Recorrido por los principales monumentos de París: Torre Eiffel, Arco del Triunfo, Champs-Élysées y crucero por el Sena. Incluye almuerzo en restaurante típico francés y tiempo libre para compras', 'París, Francia metropolitana, Francia', 48.85888970, 2.32004100, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen1_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen2_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen3_1750801432.jpg', 1, 1, '2025-06-24 21:43:09', '2025-07-02 01:23:13'),
(5, 'es', 'Día en España - EUROPA', 'Día completo en españa esta es una descripcion alternativa para los días.', 'España', 39.32606800, -4.83797900, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_5_imagen1_1751067617.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_5_imagen2_1751067617.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_5_imagen3_1751067617.jpg', 0, 1, '2025-06-27 23:40:17', '2025-07-03 01:17:45'),
(6, 'en', 'Día en Santa Marta - Colombia', 'Santa Marta es una ciudad ubicada en el mar Caribe, en el departamento de Magdalena en el norte de Colombia. Es un puerto ajetreado que también fue el primer asentamiento español en Colombia. Es la vía de acceso para las excursiones en el Parque nacional natural Tayrona y para los recorridos guiados de varios días por el sitio arqueológico de la Ciudad Perdida (Teyuna) en la Sierra Nevada de las montañas de Santa Marta', 'Santa Marta, Magdalena, Colombia', 11.08665570, -73.88035200, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen1_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen2_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen3_1751069026.jpg', 1, 1, '2025-06-28 00:03:46', '2025-07-03 01:21:00'),
(7, 'es', 'Día en Napoles', 'Italia, país europeo con una larga costa mediterránea, influyó considerablemente en la cultura y la cocina occidental. Su capital, Roma, es hogar del Vaticano, de ruinas antiguas y de obras de arte emblemáticas. Otras ciudades importantes son Florencia, con obras maestras del renacimiento, como el \"David\" de Miguel Ángel y el Domo de Brunelleschi; Venecia, la ciudad de los canales; y Milán, la capital italiana de la moda.', 'Strada Comunale Selva Cafaro, Stadera, Municipalità 7, Botteghelle, Nápoles, Campania, 80026, Italia', 40.87899690, 14.29870605, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen1_1751573697.webp', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen2_1751573697.webp', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen3_1751573697.jpg', 1, 1, '2025-07-03 01:20:03', '2025-07-03 20:14:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biblioteca_transportes`
--

CREATE TABLE `biblioteca_transportes` (
  `id` int(11) NOT NULL,
  `idioma` varchar(5) NOT NULL DEFAULT 'es',
  `medio` enum('bus','avion','coche','barco','tren') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `lugar_salida` varchar(255) DEFAULT NULL,
  `lugar_llegada` varchar(255) DEFAULT NULL,
  `lat_salida` decimal(10,8) DEFAULT NULL,
  `lng_salida` decimal(11,8) DEFAULT NULL,
  `lat_llegada` decimal(10,8) DEFAULT NULL,
  `lng_llegada` decimal(11,8) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `distancia_km` decimal(8,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `biblioteca_transportes`
--

INSERT INTO `biblioteca_transportes` (`id`, `idioma`, `medio`, `titulo`, `descripcion`, `lugar_salida`, `lugar_llegada`, `lat_salida`, `lng_salida`, `lat_llegada`, `lng_llegada`, `duracion`, `distancia_km`, `activo`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 'es', 'avion', 'Vuelo de Bogotá D.C a Santa Marta.', 'Un vuelo de Bogotá a Santa Marta dura aproximadamente 1 hora y 30 minutos en un vuelo directo. La distancia entre las dos ciudades es de alrededor de 713 kilómetros. Varios aerolíneas como LATAM, Avianca, Wingo y JetSMART ofrecen vuelos a Santa Marta desde Bogotá', 'Aeropuerto Internacional El Dorado, Calle 70B, El Carmelo, UPZs Localidad Engativá, Localidad Engativá, Bogotá, Bogotá, Distrito Capital, RAP (Especial) Central, 111041, Colombia', 'Aeropuerto Internacional Simón Bolívar, Calle 149, Localidad 3 Turística - Perla del Caribe, Perímetro Urbano Santa Marta, Santa Marta, Magdalena, 470006, Colombia', 4.70209500, -74.14771300, 11.11889200, -74.23118900, '2 horas y 30 minutos', 999.97, 1, 1, '2025-06-28 00:14:37', '2025-06-28 00:16:42'),
(4, 'es', 'avion', 'Día Completo en París - Monumentos Históricos', 'sdgfdgfd', 'Aeropuerto Internacional El Dorado, Calle 70B, El Carmelo, UPZs Localidad Engativá, Localidad Engativá, Bogotá, Bogotá, Distrito Capital, RAP (Especial) Central, 111041, Colombia', 'Aeropuerto El Dorado, Salidas, El Refugio, UPZs Localidad Fontibón, Localidad Fontibón, Bogotá, Bogotá, Distrito Capital, RAP (Especial) Central, 110911, Colombia', NULL, NULL, NULL, NULL, '2 horas y 30 minutos', 45.00, 1, 1, '2025-07-02 01:11:52', '2025-07-02 01:12:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL DEFAULT 'Travel Agency',
  `logo_url` varchar(255) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `admin_primary_color` varchar(7) DEFAULT '#e53e3e',
  `admin_secondary_color` varchar(7) DEFAULT '#fd746c',
  `agent_primary_color` varchar(7) DEFAULT '#667eea',
  `agent_secondary_color` varchar(7) DEFAULT '#764ba2',
  `login_bg_color` varchar(7) DEFAULT '#667eea',
  `login_secondary_color` varchar(7) DEFAULT '#764ba2',
  `default_language` varchar(5) DEFAULT 'es',
  `session_timeout` int(11) DEFAULT 60,
  `max_file_size` int(11) DEFAULT 10,
  `backup_frequency` enum('daily','weekly','monthly','never') DEFAULT 'weekly',
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `logo_url`, `background_image`, `admin_primary_color`, `admin_secondary_color`, `agent_primary_color`, `agent_secondary_color`, `login_bg_color`, `login_secondary_color`, `default_language`, `session_timeout`, `max_file_size`, `backup_frequency`, `maintenance_mode`, `created_at`, `updated_at`) VALUES
(1, 'JAVIER TRAVELS', 'http://localhost/travel_agency/assets/uploads/config/logo_686881dc38e0c.jpg', 'http://localhost/travel_agency/assets/uploads/config/background_686881a998dd5.jpeg', '#3cd370', '#3cd370', '#cf2678', '#eaaed4', '#28e2cc', '#0fe6f5', 'es', 120, 10, 'weekly', 0, '2025-06-24 19:30:40', '2025-07-05 01:39:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_uploads`
--

CREATE TABLE `config_uploads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `upload_type` enum('logo','background','general') NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config_uploads`
--

INSERT INTO `config_uploads` (`id`, `filename`, `original_name`, `file_path`, `file_size`, `mime_type`, `upload_type`, `uploaded_by`, `created_at`) VALUES
(1, 'logo_685afea058e4c_1750793888.png', 'Captura de pantalla 2025-06-20 133948.png', '/assets/uploads/config/logo_685afea058e4c_1750793888.png', 467712, 'image/png', 'logo', 1, '2025-06-24 19:38:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_dias`
--

CREATE TABLE `programa_dias` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `dia_numero` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `fecha_dia` date DEFAULT NULL,
  `imagen1` varchar(500) DEFAULT NULL,
  `imagen2` varchar(500) DEFAULT NULL,
  `imagen3` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comidas_incluidas` tinyint(1) DEFAULT 0,
  `desayuno` tinyint(1) DEFAULT 0,
  `almuerzo` tinyint(1) DEFAULT 0,
  `cena` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_dias`
--

INSERT INTO `programa_dias` (`id`, `solicitud_id`, `dia_numero`, `titulo`, `descripcion`, `ubicacion`, `fecha_dia`, `imagen1`, `imagen2`, `imagen3`, `created_at`, `updated_at`, `comidas_incluidas`, `desayuno`, `almuerzo`, `cena`) VALUES
(2, 24, 1, 'Día Completo en París - Monumentos Históricos', 'Recorrido por los principales monumentos de París: Torre Eiffel, Arco del Triunfo, Champs-Élysées y crucero por el Sena. Incluye almuerzo en restaurante típico francés y tiempo libre para compras.', 'Paris, Jeu-les-Bois, Châteauroux, Indre, Centro, Francia metropolitana, 36120, Francia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen1_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen2_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen3_1750801432.jpg', '2025-06-26 23:23:31', '2025-06-26 23:23:31', 0, 0, 0, 0),
(6, 24, 2, 'Día Completo en París - Monumentos Históricos', 'Recorrido por los principales monumentos de París: Torre Eiffel, Arco del Triunfo, Champs-Élysées y crucero por el Sena. Incluye almuerzo en restaurante típico francés y tiempo libre para compras.', 'Paris, Jeu-les-Bois, Châteauroux, Indre, Centro, Francia metropolitana, 36120, Francia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen1_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen2_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen3_1750801432.jpg', '2025-06-27 00:56:50', '2025-06-27 00:56:50', 0, 0, 0, 0),
(7, 24, 3, 'Día Completo en París - Monumentos Históricos', 'Recorrido por los principales monumentos de París: Torre Eiffel, Arco del Triunfo, Champs-Élysées y crucero por el Sena. Incluye almuerzo en restaurante típico francés y tiempo libre para compras.', 'Paris, Jeu-les-Bois, Châteauroux, Indre, Centro, Francia metropolitana, 36120, Francia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen1_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen2_1750801432.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_4_imagen3_1750801432.jpg', '2025-06-27 00:58:22', '2025-06-27 00:58:22', 0, 0, 0, 0),
(8, 27, 1, 'Día en Santa Marta, Colombia', 'Santa Marta es una ciudad ubicada en el mar Caribe, en el departamento de Magdalena en el norte de Colombia. Es un puerto ajetreado que también fue el primer asentamiento español en Colombia. Es la vía de acceso para las excursiones en el Parque nacional natural Tayrona y para los recorridos guiados de varios días por el sitio arqueológico de la Ciudad Perdida (Teyuna) en la Sierra Nevada de las montañas de Santa Marta.', 'Santa Marta, Magdalena, Colombia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen1_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen2_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen3_1751069026.jpg', '2025-06-28 00:39:42', '2025-07-02 20:27:27', 1, 0, 0, 0),
(18, 34, 1, 'Día en Santa Marta, Colombia', 'Santa Marta es una ciudad ubicada en el mar Caribe, en el departamento de Magdalena en el norte de Colombia. Es un puerto ajetreado que también fue el primer asentamiento español en Colombia. Es la vía de acceso para las excursiones en el Parque nacional natural Tayrona y para los recorridos guiados de varios días por el sitio arqueológico de la Ciudad Perdida (Teyuna) en la Sierra Nevada de las montañas de Santa Marta.', 'Santa Marta, Magdalena, Colombia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen1_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen2_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen3_1751069026.jpg', '2025-07-03 01:29:13', '2025-07-03 01:29:13', 0, 0, 0, 0),
(21, 33, 1, 'Día en Napoles', 'Italia, país europeo con una larga costa mediterránea, influyó considerablemente en la cultura y la cocina occidental. Su capital, Roma, es hogar del Vaticano, de ruinas antiguas y de obras de arte emblemáticas. Otras ciudades importantes son Florencia, con obras maestras del renacimiento, como el \"David\" de Miguel Ángel y el Domo de Brunelleschi; Venecia, la ciudad de los canales; y Milán, la capital italiana de la moda.', 'Strada Comunale Selva Cafaro, Stadera, Municipalità 7, Botteghelle, Nápoles, Campania, 80026, Italia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen1_1751573697.webp', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen2_1751573697.webp', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/07/dias_7_imagen3_1751573697.jpg', '2025-07-03 20:20:21', '2025-07-03 20:20:21', 0, 0, 0, 0),
(22, 33, 2, 'Día en Santa Marta - Colombia', 'Santa Marta es una ciudad ubicada en el mar Caribe, en el departamento de Magdalena en el norte de Colombia. Es un puerto ajetreado que también fue el primer asentamiento español en Colombia. Es la vía de acceso para las excursiones en el Parque nacional natural Tayrona y para los recorridos guiados de varios días por el sitio arqueológico de la Ciudad Perdida (Teyuna) en la Sierra Nevada de las montañas de Santa Marta', 'Santa Marta, Magdalena, Colombia', NULL, 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen1_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen2_1751069026.jpg', 'http://localhost/travel_agency/assets/uploads/biblioteca/dias/2025/06/dias_6_imagen3_1751069026.jpg', '2025-07-03 20:20:44', '2025-07-03 20:20:44', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_dias_servicios`
--

CREATE TABLE `programa_dias_servicios` (
  `id` int(11) NOT NULL,
  `programa_dia_id` int(11) NOT NULL,
  `tipo_servicio` enum('actividad','transporte','alojamiento') NOT NULL,
  `biblioteca_item_id` int(11) NOT NULL,
  `orden` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `servicio_principal_id` int(11) DEFAULT NULL COMMENT 'ID del servicio principal (NULL si es principal)',
  `es_alternativa` tinyint(1) DEFAULT 0 COMMENT '0=Principal, 1=Alternativa',
  `orden_alternativa` int(11) DEFAULT 0 COMMENT 'Orden dentro de las alternativas (0 para principal)',
  `notas_alternativa` text DEFAULT NULL COMMENT 'Notas específicas de esta alternativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_dias_servicios`
--

INSERT INTO `programa_dias_servicios` (`id`, `programa_dia_id`, `tipo_servicio`, `biblioteca_item_id`, `orden`, `created_at`, `servicio_principal_id`, `es_alternativa`, `orden_alternativa`, `notas_alternativa`) VALUES
(33, 8, 'actividad', 4, 1, '2025-07-02 00:50:18', NULL, 0, 0, NULL),
(34, 8, 'transporte', 3, 2, '2025-07-02 00:50:23', NULL, 0, 0, NULL),
(35, 8, 'alojamiento', 4, 3, '2025-07-02 00:50:27', NULL, 0, 0, NULL),
(38, 8, 'alojamiento', 3, 3, '2025-07-02 00:50:49', 35, 1, 1, NULL),
(48, 18, 'actividad', 4, 1, '2025-07-03 01:29:13', NULL, 0, 0, NULL),
(49, 18, 'transporte', 3, 2, '2025-07-03 01:29:13', NULL, 0, 0, NULL),
(50, 18, 'alojamiento', 4, 3, '2025-07-03 01:29:13', NULL, 0, 0, NULL),
(51, 18, 'alojamiento', 3, 3, '2025-07-03 01:29:14', NULL, 0, 0, NULL),
(56, 21, 'actividad', 3, 1, '2025-07-03 20:22:13', NULL, 0, 0, NULL),
(57, 21, 'transporte', 4, 2, '2025-07-03 20:22:16', NULL, 0, 0, NULL),
(58, 21, 'alojamiento', 3, 3, '2025-07-03 20:22:19', NULL, 0, 0, NULL),
(59, 21, 'actividad', 4, 1, '2025-07-03 20:22:25', 56, 1, 1, NULL),
(60, 21, 'transporte', 3, 2, '2025-07-03 20:22:28', 57, 1, 1, NULL),
(61, 21, 'alojamiento', 4, 3, '2025-07-03 20:22:32', 58, 1, 1, NULL),
(62, 22, 'actividad', 4, 1, '2025-07-03 20:22:45', NULL, 0, 0, NULL),
(63, 22, 'transporte', 3, 2, '2025-07-03 20:22:48', NULL, 0, 0, NULL),
(64, 22, 'alojamiento', 4, 3, '2025-07-03 20:22:50', NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_personalizacion`
--

CREATE TABLE `programa_personalizacion` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `titulo_programa` varchar(200) DEFAULT NULL,
  `idioma_predeterminado` varchar(5) DEFAULT 'es',
  `foto_portada` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_personalizacion`
--

INSERT INTO `programa_personalizacion` (`id`, `solicitud_id`, `titulo_programa`, `idioma_predeterminado`, `foto_portada`, `created_at`, `updated_at`) VALUES
(24, 24, 'Conociendo París si si si', 'es', 'http://localhost/travel_agency/assets/uploads/programa/2025/06/programa_24_cover_1750980909.jpg', '2025-06-26 20:58:26', '2025-06-26 23:35:09'),
(27, 27, 'Conociendo Santa Marta si', 'es', 'http://localhost/travel_agency/assets/uploads/programa/2025/06/programa_27_cover_1751071579.jpeg', '2025-06-28 00:36:52', '2025-07-01 22:37:19'),
(33, 33, 'Conociendo Italia', 'es', 'http://localhost/travel_agency/assets/uploads/programa/2025/07/programa_33_cover_1751508106.webp', '2025-07-03 01:23:36', '2025-07-03 02:01:46'),
(34, 34, 'Copia de Conociendo Santa Marta si', 'es', 'http://localhost/travel_agency/assets/uploads/programa/2025/07/programa_34_cover_1751506209.jpg', '2025-07-03 01:29:13', '2025-07-03 01:30:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_precios`
--

CREATE TABLE `programa_precios` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `moneda` varchar(3) DEFAULT 'USD',
  `precio_por_persona` decimal(10,2) DEFAULT NULL,
  `precio_total` decimal(10,2) DEFAULT NULL,
  `noches_incluidas` int(11) DEFAULT 0,
  `precio_incluye` text DEFAULT NULL,
  `precio_no_incluye` text DEFAULT NULL,
  `condiciones_generales` text DEFAULT NULL,
  `movilidad_reducida` tinyint(1) DEFAULT 0,
  `info_pasaporte` text DEFAULT NULL,
  `info_seguros` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_precios`
--

INSERT INTO `programa_precios` (`id`, `solicitud_id`, `moneda`, `precio_por_persona`, `precio_total`, `noches_incluidas`, `precio_incluye`, `precio_no_incluye`, `condiciones_generales`, `movilidad_reducida`, `info_pasaporte`, `info_seguros`, `created_at`, `updated_at`) VALUES
(1, 24, 'COP', 2500000.00, 2500000.00, 3, 'NADA ¿Qué incluye el precio?', 'NADA ¿Qué NO incluye?', 'NADA Condiciones generales', 0, 'NADA Información de pasaporte', 'NADA Información de SEGUROS', '2025-06-27 01:19:51', '2025-06-27 01:19:51'),
(2, 27, 'COP', 1500000.00, 1500000.00, 1, '✓ 4 noches en hotel 4* en zona céntrica (Le Marais)\r\n✓ Desayuno continental diario\r\n✓ Traslados aeropuerto-hotel-aeropuerto\r\n✓ Tour guiado por Montmartre (2 horas)\r\n✓ Crucero por el Sena al atardecer\r\n✓ Entrada a la Torre Eiffel (2do piso)\r\n✓ Entrada al Museo del Louvre con audioguía\r\n✓ Pase de transporte público 5 días (Metro/Bus)\r\n✓ Guía turística digital de París\r\n✓ Seguro de viaje básico\r\n✓ Impuestos hoteleros\r\n✓ Asistencia telefónica 24/7 en español', '✗ Vuelos internacionales (origen-París-origen)\r\n✗ Almuerzos y cenas (excepto desayunos)\r\n✗ Bebidas alcohólicas en hotel\r\n✗ Entradas a espectáculos (Moulin Rouge, ópera, etc.)\r\n✗ Compras personales y souvenirs\r\n✗ Propinas para guías y personal de servicio\r\n✗ Seguro de cancelación de viaje\r\n✗ Actividades opcionales no mencionadas\r\n✗ Gastos por servicios de habitación (minibar, llamadas)\r\n✗ Documentación (visa si aplica)', '📅 RESERVAS Y PAGOS:\r\n- Reserva: 40% del valor total al confirmar\r\n- Saldo: 60% restante 21 días antes del viaje\r\n- Formas de pago: Transferencia, tarjeta de crédito, PayPal\r\n\r\n❌ CANCELACIONES:\r\n- Más de 45 días: Sin penalidad (se retiene 5% gastos administrativos)\r\n- 30-44 días: 25% del valor total\r\n- 15-29 días: 50% del valor total\r\n- Menos de 15 días: 100% del valor total\r\n\r\n🔄 CAMBIOS:\r\n- Cambios de fecha: €50 por persona (sujeto a disponibilidad)\r\n- Cambios de hotel: Diferencia de tarifa + €30 gastos administrativos\r\n- Cambios menores de 72h: No permitidos\r\n\r\n👥 GRUPOS:\r\n- Mínimo 2 personas para confirmar tour\r\n- Máximo 8 personas por grupo\r\n- Grupos corporativos: Tarifas especiales disponibles\r\n\r\n⚠️ FUERZA MAYOR:\r\n- Cancelaciones por causas externas: Reembolso del 90%\r\n- Cambios climáticos extremos: Itinerario alternativo sin costo', 0, '📋 DOCUMENTOS REQUERIDOS:\r\n\r\n🇪🇺 CIUDADANOS EUROPEOS:\r\n- DNI o pasaporte vigente\r\n\r\n🌎 CIUDADANOS EXTRACOMUNITARIOS:\r\n- Pasaporte vigente (mínimo 3 meses de validez)\r\n- Visa Schengen (si aplica según nacionalidad)\r\n- Boleto de salida confirmado\r\n\r\n👶 MENORES DE EDAD:\r\n- Pasaporte individual vigente\r\n- Autorización notariada de padres/tutores (si viaja solo o con un adulto)\r\n- Documentos que acrediten relación familiar\r\n\r\n💉 SALUD:\r\n- No se requieren vacunas específicas\r\n- Tarjeta Sanitaria Europea (ciudadanos UE)\r\n- Seguro médico internacional (recomendado para extracomunitarios)\r\n\r\n📧 ENVÍO DE DOCUMENTOS:\r\n- Copia de documentos requerida 15 días antes del viaje\r\n- Envío por email a: documentos@turagencia.com', '🔵 SEGURO BÁSICO INCLUIDO:\r\n- Asistencia médica: €30,000\r\n- Gastos odontológicos urgentes: €300\r\n- Repatriación sanitaria: Incluida\r\n- Gastos de regreso anticipado: €1,500\r\n- Responsabilidad civil: €60,000\r\n\r\n🟡 SEGURO PREMIUM (Opcional - €45/persona):\r\n- Asistencia médica: €100,000\r\n- Cancelación de viaje: Hasta €2,500\r\n- Equipaje y efectos personales: €1,500\r\n- Pérdida de conexiones: €300\r\n- Gastos por demora de vuelos: €150\r\n- Actividades deportivas y de aventura: Incluidas\r\n\r\n🔴 SEGURO TOTAL (Opcional - €85/persona):\r\n- Todo lo del Premium +\r\n- Cancelación por cualquier motivo: 75% del valor\r\n- Gastos por cuarentena COVID: €2,000\r\n- Equipaje de alta gama: Hasta €3,000\r\n- Asistencia legal: €3,000\r\n- Interrupción de viaje: Costo total\r\n\r\n📞 ASISTENCIA 24/7:\r\n- Teléfono: +33 1 23 45 67 89\r\n- Email: emergencias@segurosviaje.com\r\n- App móvil: \"Mi Seguro Viaje\"\r\n- WhatsApp: +33 6 12 34 56 78\r\n\r\n⚠️ IMPORTANTE:\r\n- Declarar condiciones médicas preexistentes\r\n- Seguro debe contratarse dentro de 48h de la reserva\r\n- Cobertura COVID incluida en seguros Premium y Total', '2025-06-28 00:42:30', '2025-06-28 00:43:07'),
(5, 33, 'COP', 2500000.00, 5000000.00, 2, '✓ 4 noches en hotel 4* en zona céntrica (Le Marais)\r\n✓ Desayuno continental diario\r\n✓ Traslados aeropuerto-hotel-aeropuerto\r\n✓ Tour guiado por Montmartre (2 horas)\r\n✓ Crucero por el Sena al atardecer\r\n✓ Entrada a la Torre Eiffel (2do piso)\r\n✓ Entrada al Museo del Louvre con audioguía\r\n✓ Pase de transporte público 5 días (Metro/Bus)\r\n✓ Guía turística digital de París\r\n✓ Seguro de viaje básico\r\n✓ Impuestos hoteleros\r\n✓ Asistencia telefónica 24/7 en español', '✗ Vuelos internacionales (origen-París-origen)\r\n✗ Almuerzos y cenas (excepto desayunos)\r\n✗ Bebidas alcohólicas en hotel\r\n✗ Entradas a espectáculos (Moulin Rouge, ópera, etc.)\r\n✗ Compras personales y souvenirs\r\n✗ Propinas para guías y personal de servicio\r\n✗ Seguro de cancelación de viaje\r\n✗ Actividades opcionales no mencionadas\r\n✗ Gastos por servicios de habitación (minibar, llamadas)\r\n✗ Documentación (visa si aplica)', '📅 RESERVAS Y PAGOS:\r\n- Reserva: 40% del valor total al confirmar\r\n- Saldo: 60% restante 21 días antes del viaje\r\n- Formas de pago: Transferencia, tarjeta de crédito, PayPal\r\n\r\n❌ CANCELACIONES:\r\n- Más de 45 días: Sin penalidad (se retiene 5% gastos administrativos)\r\n- 30-44 días: 25% del valor total\r\n- 15-29 días: 50% del valor total\r\n- Menos de 15 días: 100% del valor total\r\n\r\n🔄 CAMBIOS:\r\n- Cambios de fecha: €50 por persona (sujeto a disponibilidad)\r\n- Cambios de hotel: Diferencia de tarifa + €30 gastos administrativos\r\n- Cambios menores de 72h: No permitidos\r\n\r\n👥 GRUPOS:\r\n- Mínimo 2 personas para confirmar tour\r\n- Máximo 8 personas por grupo\r\n- Grupos corporativos: Tarifas especiales disponibles\r\n\r\n⚠️ FUERZA MAYOR:\r\n- Cancelaciones por causas externas: Reembolso del 90%\r\n- Cambios climáticos extremos: Itinerario alternativo sin costo', 0, '📋 DOCUMENTOS REQUERIDOS:\r\n\r\n🇪🇺 CIUDADANOS EUROPEOS:\r\n- DNI o pasaporte vigente\r\n\r\n🌎 CIUDADANOS EXTRACOMUNITARIOS:\r\n- Pasaporte vigente (mínimo 3 meses de validez)\r\n- Visa Schengen (si aplica según nacionalidad)\r\n- Boleto de salida confirmado\r\n\r\n👶 MENORES DE EDAD:\r\n- Pasaporte individual vigente\r\n- Autorización notariada de padres/tutores (si viaja solo o con un adulto)\r\n- Documentos que acrediten relación familiar\r\n\r\n💉 SALUD:\r\n- No se requieren vacunas específicas\r\n- Tarjeta Sanitaria Europea (ciudadanos UE)\r\n- Seguro médico internacional (recomendado para extracomunitarios)\r\n\r\n📧 ENVÍO DE DOCUMENTOS:\r\n- Copia de documentos requerida 15 días antes del viaje\r\n- Envío por email a: documentos@turagencia.com', '🔵 SEGURO BÁSICO INCLUIDO:\r\n- Asistencia médica: €30,000\r\n- Gastos odontológicos urgentes: €300\r\n- Repatriación sanitaria: Incluida\r\n- Gastos de regreso anticipado: €1,500\r\n- Responsabilidad civil: €60,000\r\n\r\n🟡 SEGURO PREMIUM (Opcional - €45/persona):\r\n- Asistencia médica: €100,000\r\n- Cancelación de viaje: Hasta €2,500\r\n- Equipaje y efectos personales: €1,500\r\n- Pérdida de conexiones: €300\r\n- Gastos por demora de vuelos: €150\r\n- Actividades deportivas y de aventura: Incluidas\r\n\r\n🔴 SEGURO TOTAL (Opcional - €85/persona):\r\n- Todo lo del Premium +\r\n- Cancelación por cualquier motivo: 75% del valor\r\n- Gastos por cuarentena COVID: €2,000\r\n- Equipaje de alta gama: Hasta €3,000\r\n- Asistencia legal: €3,000\r\n- Interrupción de viaje: Costo total\r\n\r\n📞 ASISTENCIA 24/7:\r\n- Teléfono: +33 1 23 45 67 89\r\n- Email: emergencias@segurosviaje.com\r\n- App móvil: \"Mi Seguro Viaje\"\r\n- WhatsApp: +33 6 12 34 56 78\r\n\r\n⚠️ IMPORTANTE:\r\n- Declarar condiciones médicas preexistentes\r\n- Seguro debe contratarse dentro de 48h de la reserva\r\n- Cobertura COVID incluida en seguros Premium y Total', '2025-07-03 01:26:51', '2025-07-03 01:26:51'),
(6, 34, 'COP', 1500000.00, 1500000.00, 1, '✓ 4 noches en hotel 4* en zona céntrica (Le Marais)\r\n✓ Desayuno continental diario\r\n✓ Traslados aeropuerto-hotel-aeropuerto\r\n✓ Tour guiado por Montmartre (2 horas)\r\n✓ Crucero por el Sena al atardecer\r\n✓ Entrada a la Torre Eiffel (2do piso)\r\n✓ Entrada al Museo del Louvre con audioguía\r\n✓ Pase de transporte público 5 días (Metro/Bus)\r\n✓ Guía turística digital de París\r\n✓ Seguro de viaje básico\r\n✓ Impuestos hoteleros\r\n✓ Asistencia telefónica 24/7 en español', '✗ Vuelos internacionales (origen-París-origen)\r\n✗ Almuerzos y cenas (excepto desayunos)\r\n✗ Bebidas alcohólicas en hotel\r\n✗ Entradas a espectáculos (Moulin Rouge, ópera, etc.)\r\n✗ Compras personales y souvenirs\r\n✗ Propinas para guías y personal de servicio\r\n✗ Seguro de cancelación de viaje\r\n✗ Actividades opcionales no mencionadas\r\n✗ Gastos por servicios de habitación (minibar, llamadas)\r\n✗ Documentación (visa si aplica)', '📅 RESERVAS Y PAGOS:\r\n- Reserva: 40% del valor total al confirmar\r\n- Saldo: 60% restante 21 días antes del viaje\r\n- Formas de pago: Transferencia, tarjeta de crédito, PayPal\r\n\r\n❌ CANCELACIONES:\r\n- Más de 45 días: Sin penalidad (se retiene 5% gastos administrativos)\r\n- 30-44 días: 25% del valor total\r\n- 15-29 días: 50% del valor total\r\n- Menos de 15 días: 100% del valor total\r\n\r\n🔄 CAMBIOS:\r\n- Cambios de fecha: €50 por persona (sujeto a disponibilidad)\r\n- Cambios de hotel: Diferencia de tarifa + €30 gastos administrativos\r\n- Cambios menores de 72h: No permitidos\r\n\r\n👥 GRUPOS:\r\n- Mínimo 2 personas para confirmar tour\r\n- Máximo 8 personas por grupo\r\n- Grupos corporativos: Tarifas especiales disponibles\r\n\r\n⚠️ FUERZA MAYOR:\r\n- Cancelaciones por causas externas: Reembolso del 90%\r\n- Cambios climáticos extremos: Itinerario alternativo sin costo', 0, '📋 DOCUMENTOS REQUERIDOS:\r\n\r\n🇪🇺 CIUDADANOS EUROPEOS:\r\n- DNI o pasaporte vigente\r\n\r\n🌎 CIUDADANOS EXTRACOMUNITARIOS:\r\n- Pasaporte vigente (mínimo 3 meses de validez)\r\n- Visa Schengen (si aplica según nacionalidad)\r\n- Boleto de salida confirmado\r\n\r\n👶 MENORES DE EDAD:\r\n- Pasaporte individual vigente\r\n- Autorización notariada de padres/tutores (si viaja solo o con un adulto)\r\n- Documentos que acrediten relación familiar\r\n\r\n💉 SALUD:\r\n- No se requieren vacunas específicas\r\n- Tarjeta Sanitaria Europea (ciudadanos UE)\r\n- Seguro médico internacional (recomendado para extracomunitarios)\r\n\r\n📧 ENVÍO DE DOCUMENTOS:\r\n- Copia de documentos requerida 15 días antes del viaje\r\n- Envío por email a: documentos@turagencia.com', '🔵 SEGURO BÁSICO INCLUIDO:\r\n- Asistencia médica: €30,000\r\n- Gastos odontológicos urgentes: €300\r\n- Repatriación sanitaria: Incluida\r\n- Gastos de regreso anticipado: €1,500\r\n- Responsabilidad civil: €60,000\r\n\r\n🟡 SEGURO PREMIUM (Opcional - €45/persona):\r\n- Asistencia médica: €100,000\r\n- Cancelación de viaje: Hasta €2,500\r\n- Equipaje y efectos personales: €1,500\r\n- Pérdida de conexiones: €300\r\n- Gastos por demora de vuelos: €150\r\n- Actividades deportivas y de aventura: Incluidas\r\n\r\n🔴 SEGURO TOTAL (Opcional - €85/persona):\r\n- Todo lo del Premium +\r\n- Cancelación por cualquier motivo: 75% del valor\r\n- Gastos por cuarentena COVID: €2,000\r\n- Equipaje de alta gama: Hasta €3,000\r\n- Asistencia legal: €3,000\r\n- Interrupción de viaje: Costo total\r\n\r\n📞 ASISTENCIA 24/7:\r\n- Teléfono: +33 1 23 45 67 89\r\n- Email: emergencias@segurosviaje.com\r\n- App móvil: \"Mi Seguro Viaje\"\r\n- WhatsApp: +33 6 12 34 56 78\r\n\r\n⚠️ IMPORTANTE:\r\n- Declarar condiciones médicas preexistentes\r\n- Seguro debe contratarse dentro de 48h de la reserva\r\n- Cobertura COVID incluida en seguros Premium y Total', '2025-07-03 01:29:14', '2025-07-03 01:29:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa_solicitudes`
--

CREATE TABLE `programa_solicitudes` (
  `id` int(11) NOT NULL,
  `id_solicitud` varchar(50) DEFAULT NULL,
  `nombre_viajero` varchar(100) NOT NULL,
  `apellido_viajero` varchar(100) NOT NULL,
  `destino` varchar(200) NOT NULL,
  `fecha_llegada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `numero_pasajeros` int(11) NOT NULL DEFAULT 1,
  `acompanamiento` varchar(50) DEFAULT 'sin-acompanamiento',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programa_solicitudes`
--

INSERT INTO `programa_solicitudes` (`id`, `id_solicitud`, `nombre_viajero`, `apellido_viajero`, `destino`, `fecha_llegada`, `fecha_salida`, `numero_pasajeros`, `acompanamiento`, `user_id`, `created_at`, `updated_at`) VALUES
(24, 'SOL2025001', 'Andres Fernando', 'Pineda Guerra', 'París', '2025-06-28', '2025-07-10', 1, 'guide', 1, '2025-06-26 20:58:26', '2025-06-26 23:21:47'),
(27, 'SOL2025004', 'Karen', 'Retallak', 'Santa Marta', '2025-07-01', '2025-07-12', 2, 'guide', 1, '2025-06-28 00:36:52', '2025-07-02 01:48:58'),
(33, 'SOL2025005', 'Jheshua', 'Dannand 2', 'Italia', '2025-07-26', '2025-07-27', 2, 'guide', 1, '2025-07-03 01:23:36', '2025-07-07 22:50:35'),
(34, 'SOL2025006', 'Karen', 'Retallak', 'Santa Marta', '2025-07-31', '2025-08-09', 2, 'guide', 8, '2025-07-03 01:29:13', '2025-07-03 01:30:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','agent') NOT NULL DEFAULT 'agent',
  `active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@travelagency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin', 1, '2025-07-07 19:58:21', '2025-06-24 15:58:30', '2025-07-08 00:58:21'),
(2, 'agente1', 'agente@travelagency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Agente de Viajes', 'agent', 1, '2025-07-02 21:03:15', '2025-06-24 15:58:30', '2025-07-03 02:03:15'),
(3, 'andres.pineda.guerra', 'andrespineda@travelagency.com', '$2y$10$2jrKrjfD1hR/AWn2sDvgJOofwwqgirEAr8zWyfFPaCqfqWcA570GG', 'Andres Pineda Guerra', 'admin', 0, NULL, '2025-06-24 17:38:53', '2025-06-24 18:41:29'),
(4, 'jheshua.dannand', 'jheshua.dannand@travelagency.com', '$2y$10$nObJPm540OLjjweRmm0.quNndbn0pBfcuKjNHXwEmpOFEQYKiKnNC', 'jheshua dannand', 'agent', 1, NULL, '2025-06-24 18:35:17', '2025-07-01 01:33:55'),
(5, 'test', 'test@travelagency.com', '$2y$10$I0e01Z4j1FJi7CxB5Dk5SOOBFVLoOyu8.FIq621F2tFP1V6yle5GW', 'test user', 'admin', 1, '2025-06-30 20:34:39', '2025-07-01 01:34:19', '2025-07-01 01:34:39'),
(6, 'manuel', 'manuel@travels.com', '$2y$10$NZq2S7dC/f5DHZ0Rg.rKge1cXLbGutscmKMSMMHuNQk7Pj7vaLpQq', 'Manuel Ricardo S', 'agent', 1, '2025-07-02 20:05:55', '2025-07-01 16:59:16', '2025-07-03 01:05:55'),
(7, 'juan', 'juan@travel.com', '$2y$10$R7LCoXJeOJPi1HXr0WAVjuF8aUlTnio0yGr3y1nbwNzfUrVarC9Qm', 'juan cosito 3', 'admin', 1, '2025-07-01 15:01:14', '2025-07-01 19:59:20', '2025-07-02 01:47:21'),
(8, 'jheshua', 'jheshua@travelagency.com', '$2y$10$FYlIpj.1qZH4hFGAx1pizuTTHOZ3Vn/SvwqlW/ee2NQPzp/uyWc1q', 'jheshua dannand', 'agent', 1, '2025-07-04 20:27:24', '2025-07-03 01:07:33', '2025-07-05 01:27:24'),
(9, 'javier', 'javierfernandez@travelagency.com', '$2y$10$8/.q9Y93BhegOZo8eFRCQ.VmirIj.4c10h2FrYf2SQSOntBOwGGIa', 'Javier Fernandez', 'admin', 1, '2025-07-04 20:30:23', '2025-07-05 01:29:18', '2025-07-05 01:30:23');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `biblioteca_actividades`
--
ALTER TABLE `biblioteca_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `biblioteca_alojamientos`
--
ALTER TABLE `biblioteca_alojamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `biblioteca_dias`
--
ALTER TABLE `biblioteca_dias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `biblioteca_transportes`
--
ALTER TABLE `biblioteca_transportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `config_uploads`
--
ALTER TABLE `config_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indices de la tabla `programa_dias`
--
ALTER TABLE `programa_dias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_solicitud_dia` (`solicitud_id`,`dia_numero`);

--
-- Indices de la tabla `programa_dias_servicios`
--
ALTER TABLE `programa_dias_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_servicio_principal` (`servicio_principal_id`),
  ADD KEY `idx_es_alternativa` (`es_alternativa`),
  ADD KEY `idx_orden_alternativa` (`orden_alternativa`);

--
-- Indices de la tabla `programa_personalizacion`
--
ALTER TABLE `programa_personalizacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_solicitud` (`solicitud_id`);

--
-- Indices de la tabla `programa_precios`
--
ALTER TABLE `programa_precios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_solicitud_precio` (`solicitud_id`);

--
-- Indices de la tabla `programa_solicitudes`
--
ALTER TABLE `programa_solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `biblioteca_actividades`
--
ALTER TABLE `biblioteca_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `biblioteca_alojamientos`
--
ALTER TABLE `biblioteca_alojamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `biblioteca_dias`
--
ALTER TABLE `biblioteca_dias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `biblioteca_transportes`
--
ALTER TABLE `biblioteca_transportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `config_uploads`
--
ALTER TABLE `config_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `programa_dias`
--
ALTER TABLE `programa_dias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `programa_dias_servicios`
--
ALTER TABLE `programa_dias_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `programa_personalizacion`
--
ALTER TABLE `programa_personalizacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `programa_precios`
--
ALTER TABLE `programa_precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `programa_solicitudes`
--
ALTER TABLE `programa_solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `biblioteca_actividades`
--
ALTER TABLE `biblioteca_actividades`
  ADD CONSTRAINT `biblioteca_actividades_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `biblioteca_alojamientos`
--
ALTER TABLE `biblioteca_alojamientos`
  ADD CONSTRAINT `biblioteca_alojamientos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `biblioteca_dias`
--
ALTER TABLE `biblioteca_dias`
  ADD CONSTRAINT `biblioteca_dias_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `biblioteca_transportes`
--
ALTER TABLE `biblioteca_transportes`
  ADD CONSTRAINT `biblioteca_transportes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `config_uploads`
--
ALTER TABLE `config_uploads`
  ADD CONSTRAINT `config_uploads_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
