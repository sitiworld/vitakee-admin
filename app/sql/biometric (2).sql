-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-09-2025 a las 00:41:34
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
-- Base de datos: `biometric`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `escape_json` (`val` TEXT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    IF val IS NULL THEN
        RETURN '';
    END IF;

    RETURN REPLACE(val, '"', '\\\"');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrators`
--

CREATE TABLE `administrators` (
  `administrator_id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `system_type` varchar(255) NOT NULL,
  `timezone` varchar(255) DEFAULT 'America/Los_Angeles',
  `status` int(255) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrators`
--

INSERT INTO `administrators` (`administrator_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `system_type`, `timezone`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('1ec9501f-047f-469c-af5d-a71ce4a121bb', 'Jesús', 'del Barrio', 'jesusnbz22@gmail.com', '(+58) 4249541159', '$2y$12$IIVcbp4.cklMIyyE.ZsdGe57sgvfb/hy7/AQnk5MIy1DYDcwnjB5m', 'US', 'America/Los_Angeles', 1, '2025-09-12 15:14:54', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-09-12 15:16:09', '1ec9501f-047f-469c-af5d-a71ce4a121bb', NULL, NULL),
('2', 'fsafa', 'fsa', 'fsafa@gmail.com', '5252525252', '$2y$10$kdAWC5TVgJdxFUbbl3eO4Omhvba3gOF9lQGdApz3KEp/aupzYiOqS', 'US', 'America/Los_Angeles', 1, '2025-07-05 08:34:21', '1', '2025-07-05 08:34:26', '1', '2025-07-05 08:34:30', '1'),
('3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises', 'Celis', 'moisescelis21@gmail.com', '(+58) 4249173469', '$2y$12$O5TwNlqRlERSfM/4IfqHlO2.a6NQn6Sbl4WNQk1A6XQ4xpI4jrqP2', 'US', 'America/Los_Angeles', 1, NULL, NULL, '2025-09-12 15:22:02', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('5c3a33da-4080-453c-a206-a2fa58f5d140', 'fsafa', 'fsfsfs', 'fsaf2f22@gmail.com', '(+47) 4564564646', '$2y$12$x81q4XsYzYPsuarLFYCGoeU.kCLTeLXNiFwtwJg3X2Jc84pj9P6oy', 'US', 'America/Los_Angeles', 1, '2025-07-16 14:28:50', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL, '2025-07-16 14:28:57', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('7677d016-ff0a-4562-a86f-8a9ce3d32ecf', 'fsafa', 'fsafsss', 'fsafafwf2@gmail.com', '(+58) 4545645645', '$2y$10$eySRzDS23iQfupGLt31gzeLV1ilSTFFBD4ebDQX87s/IlgKSjC0hy', 'US', 'America/Los_Angeles', 1, '2025-07-08 14:14:18', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-09 11:11:04', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-16 10:53:12', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('99fa9129-0dcb-41d4-a7cc-fa2dc67369c6', 'fsafa', 'fsafa', 'fsafafsa@gmail.com', '(+58) 4564645645', '$2y$10$4rVnILxC88jzb2scDc8EcOGuudEWs2qiCb/88zxiMwiPoTD8YZxHq', 'US', 'America/Los_Angeles', 1, '2025-07-07 16:58:10', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-07 16:58:17', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-08 05:59:52', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('d08a28f7-95f4-468b-981c-9b988d7d5df9', 'fsafsaf', 'fsafafss', 'fsafassfafa@gmail.com', '(+58) 4564654654', '$2y$12$0fbtiGhKwBzC774IMvKr2uyQE7km16xw5DTPOpEHLyaD.sUCOT50S', 'US', 'America/Los_Angeles', 1, '2025-07-16 12:40:18', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-16 16:55:20', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL);

--
-- Disparadores `administrators`
--
DELIMITER $$
CREATE TRIGGER `trg_administrators_delete` BEFORE DELETE ON `administrators` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  DECLARE v_client_ip VARCHAR(64)     DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT           DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)     DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  DECLARE v_domain_name VARCHAR(255)  DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)  DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32) DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');
  DECLARE v_geo_ip_timestamp DATETIME DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'administrators', OLD.administrator_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'administrator_id', OLD.administrator_id,
      'first_name', OLD.first_name,
      'last_name', OLD.last_name,
      'email', OLD.email,
      'phone', OLD.phone,
      'password', OLD.password,
      'system_type', OLD.system_type,
      'timezone', OLD.timezone,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_administrators_delete_logical` AFTER UPDATE ON `administrators` FOR EACH ROW BEGIN
  -- Declaraciones
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  DECLARE v_client_ip VARCHAR(64)     DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT           DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)     DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  DECLARE v_domain_name VARCHAR(255)  DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)  DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32) DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');
  DECLARE v_geo_ip_timestamp DATETIME DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'administrators', OLD.administrator_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'administrator_id', OLD.administrator_id,
        'first_name', OLD.first_name,
        'last_name', OLD.last_name,
        'email', OLD.email,
        'phone', OLD.phone,
        'password', OLD.password,
        'system_type', OLD.system_type,
        'timezone', OLD.timezone,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_administrators_insert` AFTER INSERT ON `administrators` FOR EACH ROW BEGIN
  -- Declaraciones (igual que en delete)
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  DECLARE v_client_ip VARCHAR(64)     DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT           DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)     DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  DECLARE v_domain_name VARCHAR(255)  DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)  DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32) DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');
  DECLARE v_geo_ip_timestamp DATETIME DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'administrators', NEW.administrator_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'administrator_id', NEW.administrator_id,
      'first_name', NEW.first_name,
      'last_name', NEW.last_name,
      'email', NEW.email,
      'phone', NEW.phone,
      'password', NEW.password,
      'system_type', NEW.system_type,
      'timezone', NEW.timezone,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_administrators_update` AFTER UPDATE ON `administrators` FOR EACH ROW BEGIN
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  DECLARE v_client_ip VARCHAR(64)     DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT           DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)     DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  DECLARE v_domain_name VARCHAR(255)  DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)  DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32) DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');
  DECLARE v_geo_ip_timestamp DATETIME DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.first_name <> NEW.first_name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"first_name":{"old":"', escape_json(OLD.first_name), '","new":"', escape_json(NEW.first_name), '"}');
  END IF;
  IF OLD.last_name <> NEW.last_name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"last_name":{"old":"', escape_json(OLD.last_name), '","new":"', escape_json(NEW.last_name), '"}');
  END IF;
  IF OLD.email <> NEW.email THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"email":{"old":"', escape_json(OLD.email), '","new":"', escape_json(NEW.email), '"}');
  END IF;
  IF OLD.phone <> NEW.phone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"phone":{"old":"', escape_json(OLD.phone), '","new":"', escape_json(NEW.phone), '"}');
  END IF;
  IF OLD.password <> NEW.password THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"password":{"old":"', escape_json(OLD.password), '","new":"', escape_json(NEW.password), '"}');
  END IF;
  IF OLD.system_type <> NEW.system_type THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"system_type":{"old":"', escape_json(OLD.system_type), '","new":"', escape_json(NEW.system_type), '"}');
  END IF;
  IF OLD.timezone <> NEW.timezone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"timezone":{"old":"', escape_json(OLD.timezone), '","new":"', escape_json(NEW.timezone), '"}');
  END IF;
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'administrators', OLD.administrator_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_commissions`
--

CREATE TABLE `admin_commissions` (
  `admin_commission_id` char(36) NOT NULL,
  `transaction_id` char(36) NOT NULL,
  `commission_amount` decimal(10,2) DEFAULT NULL,
  `transaction_type` enum('VERIFICATION','CONSULTATION','SUBSCRIPTION') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Disparadores `admin_commissions`
--
DELIMITER $$
CREATE TRIGGER `trg_admin_commissions_delete` BEFORE DELETE ON `admin_commissions` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)       DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)       DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)  DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)    DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)    DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)  DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)  DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)  DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)  DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME   DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'admin_commissions', OLD.admin_commission_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'admin_commission_id', OLD.admin_commission_id,
      'transaction_id', OLD.transaction_id,
      'commission_amount', OLD.commission_amount,
      'transaction_type', OLD.transaction_type,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admin_commissions_delete_logical` AFTER UPDATE ON `admin_commissions` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)       DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)       DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)  DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)    DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)    DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)  DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)  DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)  DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)  DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME   DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'admin_commissions', OLD.admin_commission_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'admin_commission_id', OLD.admin_commission_id,
        'transaction_id', OLD.transaction_id,
        'commission_amount', OLD.commission_amount,
        'transaction_type', OLD.transaction_type,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admin_commissions_insert` AFTER INSERT ON `admin_commissions` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)       DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)       DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)  DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)    DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)    DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)  DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)  DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)  DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)  DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME   DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'admin_commissions', NEW.admin_commission_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'admin_commission_id', NEW.admin_commission_id,
      'transaction_id', NEW.transaction_id,
      'commission_amount', NEW.commission_amount,
      'transaction_type', NEW.transaction_type,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by,
      'updated_at', NEW.updated_at,
      'updated_by', NEW.updated_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_admin_commissions_update` AFTER UPDATE ON `admin_commissions` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)        DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)    DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)     DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)       DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)       DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)  DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)    DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)    DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)  DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)  DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)  DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)  DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME   DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.transaction_id <> NEW.transaction_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"transaction_id":{"old":"', escape_json(OLD.transaction_id), '","new":"', escape_json(NEW.transaction_id), '"}'
    );
  END IF;

  IF OLD.commission_amount <> NEW.commission_amount THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"commission_amount":{"old":"', escape_json(OLD.commission_amount), '","new":"', escape_json(NEW.commission_amount), '"}'
    );
  END IF;

  IF OLD.transaction_type <> NEW.transaction_type THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"transaction_type":{"old":"', escape_json(OLD.transaction_type), '","new":"', escape_json(NEW.transaction_type), '"}'
    );
  END IF;

  IF OLD.created_at <> NEW.created_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"created_at":{"old":"', escape_json(OLD.created_at), '","new":"', escape_json(NEW.created_at), '"}'
    );
  END IF;

  IF OLD.created_by <> NEW.created_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"created_by":{"old":"', escape_json(OLD.created_by), '","new":"', escape_json(NEW.created_by), '"}'
    );
  END IF;

  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  IF OLD.deleted_at <> NEW.deleted_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"deleted_at":{"old":"', escape_json(OLD.deleted_at), '","new":"', escape_json(NEW.deleted_at), '"}'
    );
  END IF;

  IF OLD.deleted_by <> NEW.deleted_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"deleted_by":{"old":"', escape_json(OLD.deleted_by), '","new":"', escape_json(NEW.deleted_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'admin_commissions', OLD.admin_commission_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` bigint(20) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` varchar(100) NOT NULL,
  `action_type` enum('UPDATE','DELETE_LOGICAL','DELETE_PHYSICAL','INSERT') NOT NULL,
  `action_by` char(36) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `action_timestamp` datetime DEFAULT current_timestamp(),
  `action_timezone` varchar(255) DEFAULT NULL,
  `changes` text DEFAULT NULL,
  `full_row` longtext DEFAULT NULL,
  `client_ip` varchar(45) DEFAULT NULL,
  `client_hostname` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `client_os` varchar(50) DEFAULT NULL,
  `client_browser` varchar(50) DEFAULT NULL,
  `domain_name` varchar(100) DEFAULT NULL,
  `request_uri` varchar(200) DEFAULT NULL,
  `server_hostname` varchar(100) DEFAULT NULL,
  `client_country` varchar(255) NOT NULL,
  `client_region` varchar(255) NOT NULL,
  `client_city` varchar(255) NOT NULL,
  `client_zipcode` varchar(255) NOT NULL,
  `client_coordinates` varchar(255) NOT NULL,
  `geo_ip_timestamp` datetime DEFAULT NULL,
  `geo_ip_timezone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`audit_id`, `table_name`, `record_id`, `action_type`, `action_by`, `full_name`, `user_type`, `action_timestamp`, `action_timezone`, `changes`, `full_row`, `client_ip`, `client_hostname`, `user_agent`, `client_os`, `client_browser`, `domain_name`, `request_uri`, `server_hostname`, `client_country`, `client_region`, `client_city`, `client_zipcode`, `client_coordinates`, `geo_ip_timestamp`, `geo_ip_timezone`) VALUES
(1, 'backups', 'c8824b07-5e6e-495c-abdb-b6e51533d686', 'INSERT', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-16 18:28:09', 'America/Lima', NULL, '{\"name\": \"bd_vitakee_developer-2025-07-16_23-28-08.sql\", \"date\": \"2025-07-16\", \"created_at\": \"2025-07-16 18:28:09\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}', '108.174.60.85', '108-174-60-85-host.colocrossing.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/backups', 'spacesector.es', 'United States', 'Georgia', 'Marietta', '30006', '33.9521,-84.5475', NULL, NULL),
(2, 'specialty', '1656ad34-0db6-4c29-8c90-d029c3182bb9', 'INSERT', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-16 19:28:03', 'America/Lima', NULL, '{\"name_en\": \"abcxyz1\", \"name_es\": \"abcxyz1\", \"created_at\": \"2025-07-16 19:28:03\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Windows 10', 'Mozilla Firefox', 'vitakee.siti.tech', '/specialties', 'spacesector.es', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', NULL, NULL),
(3, 'specialty', '1656ad34-0db6-4c29-8c90-d029c3182bb9', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-16 19:33:02', 'America/Lima', '{\"name_en\":{\"old\":\"abcxyz1\",\"new\":\"abcxyz123\"},\"name_es\":{\"old\":\"abcxyz1\",\"new\":\"abcxyz123\"}}', NULL, '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Windows 10', 'Mozilla Firefox', 'vitakee.siti.tech', '/specialties/1656ad34-0db6-4c29-8c90-d029c3182bb9', 'spacesector.es', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', NULL, NULL),
(4, 'specialty', '1656ad34-0db6-4c29-8c90-d029c3182bb9', 'DELETE_LOGICAL', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-16 19:35:25', 'America/Lima', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-07-16 19:35:25\"}}', '{\"specialty_id\": \"1656ad34-0db6-4c29-8c90-d029c3182bb9\", \"name_en\": \"abcxyz123\", \"name_es\": \"abcxyz123\", \"created_at\": \"2025-07-16 19:28:03\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"updated_at\": \"2025-07-16 19:33:02\", \"updated_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"deleted_at\": \"2025-07-16 19:35:25\", \"deleted_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'Windows 10', 'Mozilla Firefox', 'vitakee.siti.tech', '/specialties/1656ad34-0db6-4c29-8c90-d029c3182bb9', 'spacesector.es', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', NULL, NULL),
(5, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-17 08:14:04', 'America/Lima', '{\"specialty_id\":{\"old\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\",\"new\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\"}}', NULL, '::1', 'DESKTOP-BRTU0R4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'localhost', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'DESKTOP-BRTU0R4', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', NULL, NULL),
(6, 'security_questions', 'cb02618c-d46f-49a5-b879-15d2f70fd6db', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-07-17 06:54:37', 'America/Los_Angeles', NULL, '{\"user_id_user\": null, \"user_id_admin\": null, \"user_id_specialist\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"user_type\": \"Specialist\", \"question1\": \"Color favorito\", \"answer1\": \"Azul\", \"question2\": \"Numero Favorito\", \"answer2\": \"07\", \"created_at\": \"2025-07-17 06:54:37\", \"created_by\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\"}', '::1', 'DESKTOP-BRTU0R4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'localhost', '/security-questions', 'DESKTOP-BRTU0R4', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', NULL, NULL),
(7, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-17 10:38:37', 'America/Lima', '{\"specialty_id\":{\"old\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\",\"new\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\"}}', NULL, '::1', 'DESKTOP-BRTU0R4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'localhost', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'DESKTOP-BRTU0R4', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-07-17 15:38:37', 'UTC'),
(8, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-17 10:42:22', 'America/Lima', '{\"specialty_id\":{\"old\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\",\"new\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\"}}', NULL, '::1', 'DESKTOP-BRTU0R4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'localhost', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'DESKTOP-BRTU0R4', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-07-17 15:42:22', 'UTC'),
(9, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-17 12:24:03', 'America/Lima', '{\"specialty_id\":{\"old\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\",\"new\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\"}}', NULL, '::1', 'DESKTOP-BRTU0R4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'localhost', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'DESKTOP-BRTU0R4', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-07-17 17:24:03', 'UTC'),
(10, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-19 10:36:24', 'America/Lima', '{\"specialty_id\":{\"old\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\",\"new\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\"}}', NULL, '198.46.249.59', '198-46-249-59-host.colocrossing.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', '271F5AA', 'United States', 'Georgia', 'Marietta', '30006', '33.9521,-84.5475', '2025-07-19 15:36:24', 'UTC'),
(11, 'specialists', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-07-19 10:37:02', 'America/Lima', '{\"specialty_id\":{\"old\":\"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\",\"new\":\"0bff4eaa-ed0d-44b6-9dd2-86de60e47e34\"}}', NULL, '200.8.108.199', '200.8.108.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/specialist/update/c033baab-84d3-4bfa-bbf0-f07bf73476ae', '271F5AA', 'Venezuela', 'Bolívar', 'Ciudad Bolívar', 'Unknown', '8.1187,-63.5517', '2025-07-19 11:37:02', 'America/Caracas'),
(12, 'energy_metabolism', '701f7857-b578-49af-9e21-f524f541bcb7', 'INSERT', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'Jesus Zapatin', 'User', '2025-09-11 17:40:45', 'America/Los_Angeles', NULL, '{\"energy_metabolism_id\": \"701f7857-b578-49af-9e21-f524f541bcb7\", \"user_id\": \"2ea94ca9-90b0-40b4-a119-a1dd60154828\", \"energy_date\": \"2025-09-11\", \"energy_time\": \"20:43:46\", \"glucose\": 12.00, \"ketone\": 12.00, \"created_at\": \"2025-09-11 17:40:45\", \"created_by\": \"2ea94ca9-90b0-40b4-a119-a1dd60154828\"}', '200.8.108.206', '200.8.108.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/energy_metabolism', '271F5AA', 'Venezuela', 'Bolívar', 'Ciudad Bolívar', 'Unknown', '8.1187,-63.5517', '2025-09-11 20:40:45', 'America/Caracas'),
(13, 'countries', '00515e61-97a8-425b-a2cb-421258dce0a4', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:11', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-07 17:15:04\",\"new\":\"2025-09-12 17:07:11\"},\"updated_by\":{\"old\":\"3\",\"new\":\"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/countries/00515e61-97a8-425b-a2cb-421258dce0a4', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:11', 'America/New_York'),
(14, 'specialists_titles', '0153d168-9348-45ea-bf17-f8026d2751d3', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:17', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-07 17:15:14\",\"new\":\"2025-09-12 17:07:17\"},\"updated_by\":{\"old\":\"3\",\"new\":\"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/titles/0153d168-9348-45ea-bf17-f8026d2751d3', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:17', 'America/New_York'),
(15, 'specialty', '0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:21', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-15 13:15:46\",\"new\":\"2025-09-12 17:07:21\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/specialties/0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:21', 'America/New_York'),
(16, 'administrators', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:28', 'America/Lima', '{\"timezone\":{\"old\":\"America/Lima\",\"new\":\"America/Los_Angeles\"},\"updated_at\":{\"old\":\"2025-07-15 15:29:01\",\"new\":\"2025-09-12 17:07:28\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/administrator/update/3a3963c7-a08e-44b9-9a89-7081a04b2c42', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:28', 'America/New_York'),
(17, 'specialists', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:34', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-16 16:52:13\",\"new\":\"2025-09-12 17:07:34\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/specialist/update/fdf23cb0-86f1-4902-85e3-c20a1f481835', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:34', 'America/New_York'),
(18, 'test_panels', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:39', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-08 05:56:37\",\"new\":\"2025-09-12 17:07:39\"},\"updated_by\":{\"old\":\"3\",\"new\":\"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/test-panels/81054d57-92c9-4df8-a6dc-51334c1d82c4', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:39', 'America/New_York'),
(19, 'biomarkers', '1f1cc5a8-1fc5-4d65-ab35-db0f1f51b868', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:46', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-07-16 14:14:05\",\"new\":\"2025-09-12 17:07:46\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/biomarkers/1f1cc5a8-1fc5-4d65-ab35-db0f1f51b868', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:46', 'America/New_York'),
(20, 'administrators', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 17:07:57', 'America/Lima', '{\"updated_at\":{\"old\":\"2025-09-12 17:07:28\",\"new\":\"2025-09-12 17:07:57\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/administrator/update-profile/3a3963c7-a08e-44b9-9a89-7081a04b2c42', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:07:57', 'America/New_York'),
(21, 'administrators', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 15:08:10', 'America/Los_Angeles', '{\"updated_at\":{\"old\":\"2025-09-12 17:07:57\",\"new\":\"2025-09-12 15:08:10\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/administrator/update-profile/3a3963c7-a08e-44b9-9a89-7081a04b2c42', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:08:10', 'America/New_York'),
(22, 'security_questions', 'e0de0c45-8a07-40e2-bc5d-b220b9e64cc1', 'INSERT', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 15:08:41', 'America/Los_Angeles', NULL, '{\"security_question_id\": \"e0de0c45-8a07-40e2-bc5d-b220b9e64cc1\", \"user_id_user\": null, \"user_id_admin\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"user_id_specialist\": null, \"user_type\": \"Administrator\", \"question1\": \"holi\", \"answer1\": \"123456\", \"question2\": \"holi\", \"answer2\": \"123456\", \"created_at\": \"2025-09-12 15:08:41\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}', '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/security-questions', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:08:41', 'America/New_York'),
(23, 'administrators', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'INSERT', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-12 15:14:54', 'America/Los_Angeles', NULL, '{\"administrator_id\": \"1ec9501f-047f-469c-af5d-a71ce4a121bb\", \"first_name\": \"Jesús\", \"last_name\": \"del Barrio\", \"email\": \"jesusnbz22@gmail.com\", \"phone\": \"(+58) 9541159___\", \"password\": \"$2y$12$IIVcbp4.cklMIyyE.ZsdGe57sgvfb/hy7/AQnk5MIy1DYDcwnjB5m\", \"system_type\": \"US\", \"timezone\": \"America/Los_Angeles\", \"created_at\": \"2025-09-12 15:14:54\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}', '200.8.108.206', '200.8.108.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/administrator/create', '271F5AA', 'Venezuela', 'Bolívar', 'Ciudad Bolívar', 'Unknown', '8.1187,-63.5517', '2025-09-12 18:14:54', 'America/Caracas'),
(24, 'administrators', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'UPDATE', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'Jesús del Barrio', 'Administrator', '2025-09-12 15:16:09', 'America/Los_Angeles', '{\"phone\":{\"old\":\"(+58) 9541159___\",\"new\":\"(+58) 4249541159\"}}', NULL, '200.8.108.206', '200.8.108.206', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/administrator/update-profile/1ec9501f-047f-469c-af5d-a71ce4a121bb', '271F5AA', 'Venezuela', 'Bolívar', 'Ciudad Bolívar', 'Unknown', '8.1187,-63.5517', '2025-09-12 18:16:09', 'America/Caracas'),
(25, 'administrators', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Unknown', 'Unknown', '2025-09-12 15:22:03', 'America/Los_Angeles', '{\"password\":{\"old\":\"$2y$12$0A9HMckKrNYig5S34AGR4OJHPldhoWdFMSJKbLMWqCNBK2jy2XdZy\",\"new\":\"$2y$12$O5TwNlqRlERSfM/4IfqHlO2.a6NQn6Sbl4WNQk1A6XQ4xpI4jrqP2\"},\"updated_at\":{\"old\":\"2025-09-12 15:08:10\",\"new\":\"2025-09-12 15:22:02\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', 'Linux', 'Google Chrome', 'vitakee.siti.tech', '/password-recovery/update-password', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:22:02', 'America/New_York'),
(26, 'security_questions', 'e0de0c45-8a07-40e2-bc5d-b220b9e64cc1', 'DELETE_PHYSICAL', '0', 'phpMyAdmin', 'system', '2025-09-12 22:30:13', 'SYSTEM', NULL, '{\"security_question_id\": \"e0de0c45-8a07-40e2-bc5d-b220b9e64cc1\", \"user_id_user\": null, \"user_id_admin\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"user_id_specialist\": null, \"user_type\": \"Administrator\", \"question1\": \"holi\", \"answer1\": \"123456\", \"question2\": \"holi\", \"answer2\": \"123456\", \"created_at\": \"2025-09-12 15:08:41\", \"created_by\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"updated_at\": null, \"updated_by\": null, \"deleted_at\": null, \"deleted_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', '271F5AA', '', '', '', '', '', '2025-09-12 22:30:13', 'SYSTEM'),
(27, 'users', '3072b979-43a9-4640-a473-5650c4a82d54', 'DELETE_PHYSICAL', '0', 'phpMyAdmin', 'system', '2025-09-12 22:34:19', 'SYSTEM', NULL, '{\"user_id\": \"3072b979-43a9-4640-a473-5650c4a82d54\", \"first_name\": \"Moises\", \"last_name\": \"Celis\", \"sex\": \"m\", \"birthday\": \"2000-02-07\", \"height\": \"5\'07\\\"\", \"email\": \"moisescelis21@gmail.com\", \"password\": \"$2y$12$jLElDEEtOPGuLZCw3lMy/u00pFkEb/MmBkuJXYODe9R.2WqN375HK\", \"telephone\": \"(+1) (626)423-8692\", \"system_type\": \"US\", \"timezone\": \"America/Los_Angeles\", \"created_at\": null, \"created_by\": null, \"updated_at\": \"2025-09-03 17:59:35\", \"updated_by\": \"3072b979-43a9-4640-a473-5650c4a82d54\", \"deleted_at\": null, \"deleted_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', '271F5AA', '', '', '', '', '', '2025-09-12 22:34:19', 'SYSTEM'),
(28, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'INSERT', '0', 'Unknown', 'Unknown', '2025-09-12 15:34:45', 'America/Los_Angeles', NULL, '{\"user_id\": \"e3357e12-7a73-49c3-b51f-6dfe34151fb5\", \"first_name\": \"Moises Francisco\", \"last_name\": \"Celis Salazar\", \"sex\": \"m\", \"birthday\": \"2000-02-07\", \"height\": \"0\", \"email\": \"moisescelis21@gmail.com\", \"password\": \"$2y$12$82uhupzQEM5874.WgWSYr.JuRDjn05WdDNgpoeP0wQ1NQlPWkmTcS\", \"telephone\": \"(+58) 4249173469\", \"system_type\": \"US\", \"timezone\": \"America/Los_Angeles\", \"created_at\": \"2025-09-12 15:34:45\", \"created_by\": \"e3357e12-7a73-49c3-b51f-6dfe34151fb5\"}', '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/register', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:34:45', 'America/New_York'),
(29, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco Celis Salazar', 'User', '2025-09-12 15:37:00', 'America/Los_Angeles', '{\"height\":{\"old\":\"0\",\"new\":\"5\'08\\\"\"},\"system_type\":{\"old\":\"US\",\"new\":\"EU\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users_profile/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:37:00', 'America/New_York'),
(30, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco Celis Salazar', 'User', '2025-09-12 15:37:07', 'America/Los_Angeles', '{\"system_type\":{\"old\":\"EU\",\"new\":\"US\"},\"updated_at\":{\"old\":\"2025-09-12 15:37:00\",\"new\":\"2025-09-12 15:37:07\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users_profile/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:37:07', 'America/New_York'),
(31, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco Celis Salazar', 'User', '2025-09-12 15:37:34', 'America/Los_Angeles', '{\"system_type\":{\"old\":\"US\",\"new\":\"EU\"},\"updated_at\":{\"old\":\"2025-09-12 15:37:07\",\"new\":\"2025-09-12 15:37:34\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users_profile/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:37:34', 'America/New_York'),
(32, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco Celis Salazar', 'User', '2025-09-12 15:39:49', 'America/Los_Angeles', '{\"system_type\":{\"old\":\"EU\",\"new\":\"US\"},\"updated_at\":{\"old\":\"2025-09-12 15:37:34\",\"new\":\"2025-09-12 15:39:49\"}}', NULL, '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', 'Linux', 'Google Chrome', 'vitakee.siti.tech', '/user/system_type/update/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:39:49', 'America/New_York'),
(33, 'lipid_profile_record', '89199e2a-1b2a-4fdc-9309-297b66bf70cc', 'INSERT', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco Celis Salazar', 'User', '2025-09-12 15:49:21', 'America/Los_Angeles', NULL, '{\"lipid_profile_record_id\": \"89199e2a-1b2a-4fdc-9309-297b66bf70cc\", \"user_id\": \"e3357e12-7a73-49c3-b51f-6dfe34151fb5\", \"lipid_profile_date\": \"2025-09-12\", \"lipid_profile_time\": \"18:49:21\", \"ldl\": 120.00, \"hdl\": 100.00, \"total_cholesterol\": 230.00, \"triglycerides\": 50.00, \"non_hdl\": 130.00, \"created_at\": \"2025-09-12 15:49:21\", \"created_by\": \"e3357e12-7a73-49c3-b51f-6dfe34151fb5\"}', '149.102.226.104', 'unn-149-102-226-104.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/lipid-profile', '271F5AA', 'United States', 'New York', 'New York', '10118', '40.7126,-74.0066', '2025-09-12 18:49:21', 'America/New_York'),
(34, 'users', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'UPDATE', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'Jesús del Barrio', 'Administrator', '2025-09-15 09:43:01', 'America/Los_Angeles', '{\"email\":{\"old\":\"jesusmadafaka13@gmail.com\",\"new\":\"jesusnbz22@gmail.com\"},\"updated_at\":{\"old\":\"2025-06-25 10:40:34\",\"new\":\"2025-09-15 09:43:01\"},\"updated_by\":{\"old\":\"211\",\"new\":\"1ec9501f-047f-469c-af5d-a71ce4a121bb\"}}', NULL, '149.50.211.135', 'unn-149-50-211-135.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users/2ea94ca9-90b0-40b4-a119-a1dd60154828', '271F5AA', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', '2025-09-15 16:43:01', 'UTC'),
(35, 'users', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'UPDATE', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'Jesús del Barrio', 'Administrator', '2025-09-15 09:43:13', 'America/Los_Angeles', '{\"telephone\":{\"old\":\"(+58) 4249541158\",\"new\":\"(+58) 4249541159\"},\"updated_at\":{\"old\":\"2025-09-15 09:43:01\",\"new\":\"2025-09-15 09:43:13\"}}', NULL, '149.50.211.135', 'unn-149-50-211-135.datapacket.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users/2ea94ca9-90b0-40b4-a119-a1dd60154828', '271F5AA', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', '2025-09-15 16:43:13', 'UTC'),
(36, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-15 09:48:43', 'America/Los_Angeles', '{\"email\":{\"old\":\"moisescelis21@gmail.com\",\"new\":\"moisescelis22@gmail.com\"},\"updated_at\":{\"old\":\"2025-09-12 15:39:49\",\"new\":\"2025-09-15 09:48:43\"},\"updated_by\":{\"old\":\"e3357e12-7a73-49c3-b51f-6dfe34151fb5\",\"new\":\"3a3963c7-a08e-44b9-9a89-7081a04b2c42\"}}', NULL, '86.106.87.105', '86.106.87.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'Florida', 'Miami', '33132', '25.7838,-80.1823', '2025-09-15 12:48:43', 'America/New_York'),
(37, 'comment_biomarker', '2a3a5969-9726-4629-8b9a-4db5b178b625', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-15 09:58:25', 'America/Los_Angeles', NULL, '{\"comment_biomarker_id\": \"2a3a5969-9726-4629-8b9a-4db5b178b625\", \"id_test_panel\": \"e6861593-7327-4f63-9511-11d56f5398dc\", \"id_test\": \"89199e2a-1b2a-4fdc-9309-297b66bf70cc\", \"id_biomarker\": \"e10ffc15-4829-450a-953a-f2aebfbee1f5\", \"id_specialist\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"comment\": \"fafafa\", \"created_at\": \"2025-09-15 09:58:25\", \"created_by\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\"}', '86.106.87.105', '86.106.87.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/biomarker-comments', '271F5AA', 'United States', 'Florida', 'Miami', '33132', '25.7838,-80.1823', '2025-09-15 12:58:25', 'America/New_York'),
(38, 'users', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'UPDATE', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'Moises Celis', 'Administrator', '2025-09-15 09:59:13', 'America/Los_Angeles', '{\"email\":{\"old\":\"moisescelis22@gmail.com\",\"new\":\"moisescelis21@gmail.com\"},\"updated_at\":{\"old\":\"2025-09-15 09:48:43\",\"new\":\"2025-09-15 09:59:13\"}}', NULL, '86.106.87.105', '86.106.87.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/users/e3357e12-7a73-49c3-b51f-6dfe34151fb5', '271F5AA', 'United States', 'Florida', 'Miami', '33132', '25.7838,-80.1823', '2025-09-15 12:59:13', 'America/New_York'),
(39, 'specialists', '6519d1be-db8c-4270-8177-f9b9f3f5a461', 'INSERT', '0', 'Unknown', 'Unknown', '2025-09-15 10:13:47', 'America/Los_Angeles', NULL, '{\"specialist_id\": \"6519d1be-db8c-4270-8177-f9b9f3f5a461\", \"first_name\": \"Jesús\", \"last_name\": \"del Barrio\", \"email\": \"jesusnbz23@gmail.com\", \"phone\": \"(+687) 2222222222\", \"password\": \"$2y$12$4F5Uji00/XeoWxBqV0lfqOa1Gtb/AmmNbHwZdW/xO9xvSn3ylUYMO\", \"specialty_id\": \"0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30\", \"title_id\": \"23c3f261-8e8b-4095-9986-2f1f0bfa330c\", \"bio\": null, \"whatsapp_link\": null, \"website_url\": null, \"avatar_url\": null, \"verified_status\": \"PENDING\", \"languages\": null, \"available_for_free_consults\": 0, \"max_free_consults_per_month\": 0, \"system_type\": \"US\", \"timezone\": \"America/Los_Angeles\", \"created_at\": \"2025-09-15 10:13:47\", \"created_by\": \"6519d1be-db8c-4270-8177-f9b9f3f5a461\"}', '181.208.26.134', '181.208.26.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/specialist/register', '271F5AA', 'Venezuela', 'Lara', 'Barquisimeto', 'Unknown', '10.0664,-69.3586', '2025-09-15 13:13:47', 'America/Caracas'),
(40, 'renal_function', '0c5da657-839d-4c1f-97d8-75aae271097a', 'INSERT', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'Jesus Zapatin', 'User', '2025-09-15 10:51:18', 'America/Los_Angeles', NULL, '{\"renal_function_id\": \"0c5da657-839d-4c1f-97d8-75aae271097a\", \"user_id\": \"2ea94ca9-90b0-40b4-a119-a1dd60154828\", \"renal_date\": \"2025-09-07\", \"renal_time\": \"13:54:11\", \"albumin\": 999.99, \"creatinine\": 2.00, \"acr\": 9999.99, \"created_at\": \"2025-09-15 10:51:18\", \"created_by\": \"2ea94ca9-90b0-40b4-a119-a1dd60154828\"}', '186.167.70.34', '186.167.70.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'vitakee.siti.tech', '/renal-function', '271F5AA', 'Venezuela', 'Anzoátegui', 'Puerto Cruz', 'Unknown', '10.2118,-64.631', '2025-09-15 13:51:18', 'America/Caracas'),
(41, 'specialist_social_links', '9b4cb6e9-bdee-439c-8586-b511529fbd0d', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-15 16:52:53', 'America/Los_Angeles', NULL, '{\"social_link_id\": \"9b4cb6e9-bdee-439c-8586-b511529fbd0d\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"platform\": \"facebook\", \"url\": \"\", \"created_at\": \"2025-09-15 16:52:53\", \"created_by\": 0}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows 10', 'Mozilla Firefox', 'vitakee.siti.tech', '/specialist-social-links', '271F5AA', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', '2025-09-15 16:52:53', 'America/Los_Angeles'),
(42, 'specialist_social_links', '233355e0-8ead-4105-9273-859fc4c1f2e8', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-15 16:53:15', 'America/Los_Angeles', NULL, '{\"social_link_id\": \"233355e0-8ead-4105-9273-859fc4c1f2e8\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"platform\": \"facebook\", \"url\": \"\", \"created_at\": \"2025-09-15 16:53:15\", \"created_by\": 0}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'Windows 10', 'Mozilla Firefox', 'vitakee.siti.tech', '/specialist-social-links', '271F5AA', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', '2025-09-15 16:53:15', 'America/Los_Angeles'),
(43, 'renal_function', '0c5da657-839d-4c1f-97d8-75aae271097a', 'UPDATE', '0', 'phpMyAdmin', 'system', '2025-09-17 16:30:58', 'SYSTEM', '{\"user_id\":{\"old\":\"2ea94ca9-90b0-40b4-a119-a1dd60154828\",\"new\":\"e3357e12-7a73-49c3-b51f-6dfe34151fb5\"}}', NULL, '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', '271F5AA', '', '', '', '', '', '2025-09-17 16:30:58', 'SYSTEM'),
(44, 'specialist_verification_requests', '59b3877d-94c3-11f0-b3e4-00e04cf70151', 'INSERT', '0', 'phpMyAdmin', 'system', '2025-09-18 15:11:59', 'SYSTEM', NULL, '{\"verification_request_id\": \"59b3877d-94c3-11f0-b3e4-00e04cf70151\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"status\": \"APPROVED\", \"submitted_at\": \"2025-09-18 15:11:59\", \"approved_at\": \"2025-09-17 15:11:05\", \"admin_id\": \"3a3963c7-a08e-44b9-9a89-7081a04b2c42\", \"verification_level\": \"PLUS\", \"created_at\": \"2025-09-18 15:11:59\", \"created_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', 'DESKTOP-92VMM39', '', '', '', '', '', '2025-09-18 15:11:59', 'SYSTEM'),
(45, 'specialist_reviews', '3ae8a497-94c4-11f0-b3e4-00e04cf70151', 'INSERT', '0', 'phpMyAdmin', 'system', '2025-09-18 15:18:17', 'SYSTEM', NULL, '{\"review_id\": \"3ae8a497-94c4-11f0-b3e4-00e04cf70151\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"user_id\": \"5c5434da-06cc-42a0-8b52-bacbb5ee93b2\", \"rating\": 5, \"comment\": \"fsafafa\", \"created_at\": \"2025-09-18 15:18:17\", \"created_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', 'DESKTOP-92VMM39', '', '', '', '', '', '2025-09-18 15:18:17', 'SYSTEM'),
(46, 'specialist_pricing', '6805f51f-94c4-11f0-b3e4-00e04cf70151', 'INSERT', '0', 'phpMyAdmin', 'system', '2025-09-18 15:19:32', 'SYSTEM', NULL, '{\"pricing_id\": \"6805f51f-94c4-11f0-b3e4-00e04cf70151\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"service_type\": \"CONSULTATION\", \"description\": \"fsafafa\", \"price_usd\": 10.00, \"is_active\": 1, \"created_at\": \"2025-09-18 15:19:32\", \"created_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', 'DESKTOP-92VMM39', '', '', '', '', '', '2025-09-18 15:19:32', 'SYSTEM'),
(47, 'specialist_locations', 'ca44c73c-9353-4f2a-aeea-30c285c1803b', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-18 15:40:50', 'America/Los_Angeles', NULL, '{\"location_id\": \"ca44c73c-9353-4f2a-aeea-30c285c1803b\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"city_id\": \"01fe0d28-988d-4237-8ec0-c405aeaaf250\", \"state_id\": \"004a5632-aa7e-4977-b028-b813fa64a66d\", \"country_id\": \"00515e61-97a8-425b-a2cb-421258dce0a4\", \"is_primary\": 1, \"created_at\": \"0000-00-00 00:00:00\", \"created_by\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\"}', '::1', 'DESKTOP-92VMM39', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', 'Linux', 'Google Chrome', 'localhost', '/vitakee/specialist-locations', 'DESKTOP-92VMM39', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-09-18 22:40:50', 'UTC'),
(48, 'specialist_locations', 'b0bf41fa-3e39-4c21-8c53-8d3ea2c0a30d', 'INSERT', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-18 15:40:58', 'America/Los_Angeles', NULL, '{\"location_id\": \"b0bf41fa-3e39-4c21-8c53-8d3ea2c0a30d\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"city_id\": \"01fe0d28-988d-4237-8ec0-c405aeaaf250\", \"state_id\": \"004a5632-aa7e-4977-b028-b813fa64a66d\", \"country_id\": \"00515e61-97a8-425b-a2cb-421258dce0a4\", \"is_primary\": 1, \"created_at\": \"0000-00-00 00:00:00\", \"created_by\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\"}', '::1', 'DESKTOP-92VMM39', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', 'Linux', 'Google Chrome', 'localhost', '/vitakee/specialist-locations', 'DESKTOP-92VMM39', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-09-18 22:40:58', 'UTC'),
(49, 'specialist_locations', 'ca44c73c-9353-4f2a-aeea-30c285c1803b', 'UPDATE', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises celiss', 'Specialist', '2025-09-18 15:40:58', 'America/Los_Angeles', '{\"is_primary\":{\"old\":\"1\",\"new\":\"0\"}}', NULL, '::1', 'DESKTOP-92VMM39', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', 'Linux', 'Google Chrome', 'localhost', '/vitakee/specialist-locations', 'DESKTOP-92VMM39', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', '2025-09-18 22:40:58', 'UTC'),
(50, 'specialist_locations', 'ca44c73c-9353-4f2a-aeea-30c285c1803b', 'DELETE_PHYSICAL', '0', 'phpMyAdmin', 'system', '2025-09-18 18:41:20', 'SYSTEM', NULL, '{\"location_id\": \"ca44c73c-9353-4f2a-aeea-30c285c1803b\", \"specialist_id\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"city_id\": \"01fe0d28-988d-4237-8ec0-c405aeaaf250\", \"state_id\": \"004a5632-aa7e-4977-b028-b813fa64a66d\", \"country_id\": \"00515e61-97a8-425b-a2cb-421258dce0a4\", \"is_primary\": 0, \"created_at\": \"0000-00-00 00:00:00\", \"created_by\": \"fdf23cb0-86f1-4902-85e3-c20a1f481835\", \"updated_at\": \"2025-09-18 15:40:58\", \"updated_by\": null, \"deleted_at\": null, \"deleted_by\": null}', '127.0.0.1', 'localhost', 'phpMyAdmin', 'unknown', 'phpMyAdmin', '', '', 'DESKTOP-92VMM39', '', '', '', '', '', '2025-09-18 18:41:20', 'SYSTEM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backups`
--

CREATE TABLE `backups` (
  `backup_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `backups`
--

INSERT INTO `backups` (`backup_id`, `name`, `date`, `created_at`, `created_by`, `deleted_at`, `deleted_by`) VALUES
('bcb126a3-9461-41bf-a358-8896764ba09f', 'bd_vitakee_developer-2025-07-12_00-40-47.sql', '2025-07-12', '2025-07-11 20:40:47', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('c8824b07-5e6e-495c-abdb-b6e51533d686', 'bd_vitakee_developer-2025-07-16_23-28-08.sql', '2025-07-16', '2025-07-16 18:28:09', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('f5ad04f7-53b5-4018-84d0-5f7d3a18a688', 'bd_vitakee_developer-2025-07-15_16-29-22.sql', '2025-07-15', '2025-07-15 09:29:22', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL);

--
-- Disparadores `backups`
--
DELIMITER $$
CREATE TRIGGER `trg_backups_delete` BEFORE DELETE ON `backups` FOR EACH ROW BEGIN
  -- Defaults seguros (ejecución fuera del sistema, p.ej. phpMyAdmin)
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'backups', OLD.backup_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'backup_id', OLD.backup_id,
      'name', OLD.name,
      'date', OLD.date,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_backups_delete_logical` AFTER UPDATE ON `backups` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- Cuando se marca borrado lógico por primera vez
  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'backups', OLD.backup_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT(
        'deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at),
        'deleted_by', JSON_OBJECT('old', OLD.deleted_by, 'new', NEW.deleted_by)
      ),
      JSON_OBJECT(
        'backup_id', OLD.backup_id,
        'name', OLD.name,
        'date', OLD.date,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_backups_insert` AFTER INSERT ON `backups` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'backups', NEW.backup_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'backup_id', NEW.backup_id,
      'name', NEW.name,
      'date', NEW.date,
      'deleted_at', NEW.deleted_at,
      'deleted_by', NEW.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_backups_update` AFTER UPDATE ON `backups` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- Evitar duplicar registro del borrado lógico en este trigger:
  IF NOT (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
    -- Construcción del JSON de cambios (usar <=> para manejar NULLs)
    IF NOT (OLD.name <=> NEW.name) THEN
      SET change_data = CONCAT(
        change_data, IF(change_data = '{', '', ','),
        '"name":{"old":"', escape_json(OLD.name), '","new":"', escape_json(NEW.name), '"}'
      );
    END IF;

    IF NOT (OLD.date <=> NEW.date) THEN
      SET change_data = CONCAT(
        change_data, IF(change_data = '{', '', ','),
        '"date":{"old":"', escape_json(OLD.date), '","new":"', escape_json(NEW.date), '"}'
      );
    END IF;

    SET change_data = CONCAT(change_data, '}');

    IF change_data <> '{}' THEN
      INSERT INTO audit_log (
        table_name, record_id, action_type, action_by,
        full_name, user_type, action_timestamp, action_timezone,
        changes, full_row,
        client_ip, client_hostname, user_agent,
        client_os, client_browser,
        domain_name, request_uri, server_hostname,
        client_country, client_region, client_city,
        client_zipcode, client_coordinates,
        geo_ip_timestamp, geo_ip_timezone
      ) VALUES (
        'backups', OLD.backup_id, 'UPDATE', v_action_by,
        v_full_name, v_user_type, NOW(), v_action_timezone,
        change_data, NULL,
        v_client_ip, v_client_hostname, v_user_agent,
        v_client_os, v_client_browser,
        v_domain_name, v_request_uri, v_server_hostname,
        v_client_country, v_client_region, v_client_city,
        v_client_zipcode, v_client_coordinates,
        v_geo_ip_timestamp, v_geo_ip_timezone
      );
    END IF;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biomarkers`
--

CREATE TABLE `biomarkers` (
  `biomarker_id` char(36) NOT NULL,
  `panel_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `reference_min` varchar(255) NOT NULL,
  `reference_max` varchar(255) NOT NULL,
  `deficiency_label` varchar(255) NOT NULL,
  `excess_label` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `name_es` longtext NOT NULL,
  `deficiency_es` longtext NOT NULL,
  `excess_es` longtext NOT NULL,
  `description_es` longtext NOT NULL,
  `max_exam` int(255) NOT NULL,
  `name_db` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `biomarkers`
--

INSERT INTO `biomarkers` (`biomarker_id`, `panel_id`, `name`, `unit`, `reference_min`, `reference_max`, `deficiency_label`, `excess_label`, `description`, `name_es`, `deficiency_es`, `excess_es`, `description_es`, `max_exam`, `name_db`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('134e2679-164c-45d2-9293-f88164abdce6', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Body Fat Percentage - Female', '%', '18', '28', '<18% (Fat Deficiency)', '>28% (Hyperadiposity)', 'Proportion of fat to total body weight; important indicator of health in females.', 'Porcentaje de Grasa Corporal - Mujer', '<18% (Deficiencia de grasa)', '>28% (Exceso de grasa corporal)', 'Proporción de grasa respecto al peso total; indicador importante de salud en mujeres.', 5, '', NULL, NULL, '2025-06-11 17:55:35', '201', NULL, NULL),
('1f1cc5a8-1fc5-4d65-ab35-db0f1f51b868', '60819af9-0533-472c-9d5a-24a5df5a83f7', 'Albumin', 'mg/dL', '0', '2', 'Hypoalbuminemia', 'Hyperalbuminemia', 'Albumin is a protein made by the liver, essential for maintaining oncotic pressure and transporting substances in the blood.', 'Albúmina', 'Hipoalbuminemia', 'Hiperalbuminemia', 'La albúmina es una proteína producida por el hígado, esencial para mantener la presión oncótica y transportar sustancias en la sangre.', 10, '', '2025-06-20 14:31:09', '1', '2025-09-12 17:07:46', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('201f62f5-b12c-43be-81aa-c546785798dd', 'e6861593-7327-4f63-9511-11d56f5398dc', 'Triglycerides', 'mg/dL', '0', '100', 'Not clinically significant', '≥150 mg/dL (Hypertriglyceridemia)', 'Fat found in the blood; high levels can increase heart disease risk.', 'Triglicéridos', 'No clínicamente significativo', '≥150 mg/dL (Hipertrigliceridemia)', 'Grasas presentes en la sangre; niveles altos pueden aumentar el riesgo de enfermedades del corazón.', 5, '', NULL, NULL, '2025-06-11 17:56:44', '201', NULL, NULL),
('21a06607-d4f9-47f5-8ba2-fcc249768f96', '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6', 'Ketones', 'mmol/L', '0.5', '3', '<0.5 mmol/L (Nutritional Deficiency)', '>3.0 mmol/L (Ketoacidosis Risk if >5.0)', 'Produced during fat metabolism; moderate levels support ketosis, very high levels may indicate ketoacidosis.', 'Cetonas', '<0.5 mmol/L (Deficiencia nutricional)', '>3.0 mmol/L (Riesgo de cetoacidosis si >5.0)', 'Producidas durante el metabolismo de las grasas; niveles moderados apoyan la cetosis, niveles muy altos pueden indicar cetoacidosis.', 5, '', NULL, NULL, '2025-06-11 17:56:09', '201', NULL, NULL),
('2d1ebb80-baac-4d1f-885e-e27adc343070', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Resting Metabolic Rate (RMR)', 'kcal/day', '1300', '1900', '<1300 kcal (Hypometabolism)', '>1900 kcal (Hypermetabolism)', 'Calories the body burns at rest; reflects metabolic activity.', 'Tasa Metabólica en Reposo (TMR)', '<1300 kcal (Hipometabolismo)', '>1900 kcal (Hipermetabolismo)', 'Calorías que el cuerpo quema en reposo; refleja la actividad metabólica.', 5, '', NULL, NULL, '2025-06-11 17:56:31', '201', NULL, NULL),
('2ed1d917-fc8b-4ec5-b991-72d993b0426e', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Visceral Fat Level', 'Level', '1', '12', 'Not clinically significant', '>12 (Visceral Obesity)', 'Fat stored around internal organs; high levels are linked to increased disease risk.', 'Nivel de Grasa Visceral', 'No clínicamente significativo', '>12 (Obesidad visceral)', 'Grasa almacenada alrededor de los órganos internos; niveles altos aumentan el riesgo de enfermedades.', 5, '', NULL, NULL, '2025-06-11 17:56:48', '201', NULL, NULL),
('32ec8598-0940-4ce4-a80c-d3e2ced21400', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Body Fat Percentage - Male', '%', '10', '20', '<10% (Fat Deficiency)', '>20% (Hyperadiposity)', 'Proportion of fat to total body weight; important indicator of health in males.', 'Porcentaje de Grasa Corporal - Hombre', '<10% (Deficiencia de grasa)', '>20% (Exceso de grasa corporal)', 'Proporción de grasa respecto al peso total; indicador importante de salud en hombres.', 5, '', NULL, NULL, '2025-06-11 17:55:40', '201', NULL, NULL),
('38fb7644-7aed-44f5-975d-9c7ab84e03ef', 'e6861593-7327-4f63-9511-11d56f5398dc', 'LDL Cholesterol', 'mg/dL', '0', '99.99', 'Not clinically significant', '≥100 mg/dL (Hyperlipidemia)', 'Low-density lipoprotein; high levels may increase cardiovascular risk.', 'Colesterol LDL', 'No clínicamente significativo', '≥100 mg/dL (Hiperlipidemia)', 'Lipoproteína de baja densidad; niveles altos pueden aumentar el riesgo cardiovascular.', 5, '', NULL, NULL, '2025-06-11 17:56:14', '201', NULL, NULL),
('3d345bbf-7cc1-4560-8ea4-a453ce9f3282', '60819af9-0533-472c-9d5a-24a5df5a83f7', 'Creatinine', 'mg/dL', '50', '200', 'Low creatinine', 'High creatinine', 'Creatinine is a waste product from muscle metabolism, used to assess kidney function.', 'Creatinina', 'Creatinina baja', 'Creatinina alta', 'La creatinina es un producto de desecho del metabolismo muscular, usado para evaluar la función renal.', 10, '', '2025-06-20 14:52:01', '1', '2025-06-20 16:56:57', '1', NULL, NULL),
('41397af2-9910-4265-974a-15c8def4d28b', 'e6861593-7327-4f63-9511-11d56f5398dc', 'Non-HDL Cholesterol', 'mg/dL', '0', '129.99', 'Not clinically significant', '≥130 mg/dL (Atherogenic Dyslipidemia)', 'Total cholesterol minus HDL; higher values may indicate atherogenic lipid profiles.', 'Colesterol No-HDL', 'No clínicamente significativo', '≥130 mg/dL (Dislipidemia aterogénica)', 'Colesterol total menos HDL; valores altos pueden indicar un perfil lipídico aterogénico.', 5, '', NULL, NULL, '2025-06-11 17:56:28', '201', NULL, NULL),
('5df04ae0-ea47-49be-9877-0bf322d6a483', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Muscle Mass Percentage - Male', '%', '40', '50', '<40% (Muscle Wasting)', '>50% (Hypermuscularity)', 'Muscle proportion in male bodies; essential for strength and metabolic health.', 'Porcentaje de Masa Muscular - Hombre', '<40% (Pérdida muscular)', '>50% (Exceso de masa muscular)', 'Proporción muscular en el cuerpo masculino; esencial para fuerza y salud metabólica.', 5, '', NULL, NULL, '2025-06-11 17:56:24', '201', NULL, NULL),
('60f2da27-1c22-4443-8247-b9f496d91ead', '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6', 'Glucose', 'mg/dL', '70', '99', '<70 mg/dL (Hypoglycemia)', '>99 mg/dL (Hyperglycemia)', 'Indicates the amount of sugar in the blood after fasting.', 'Glucosa', '<70 mg/dL (Hipoglucemia)', '>99 mg/dL (Hiperglucemia)', 'Indica la cantidad de azúcar en la sangre después de ayunar.', 5, '', NULL, NULL, '2025-06-11 17:55:54', '201', NULL, NULL),
('8297e0d3-4e44-44cb-a5e3-5c658a89476e', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Total Body Water Percentage', '%', '50', '65', '<50% (Dehydration)', '>65% (Water Retention)', 'Represents the percentage of body weight that is water; indicates hydration status.', 'Porcentaje Total de Agua Corporal', '<50% (Deshidratación)', '>65% (Retención de líquidos)', 'Porcentaje del peso corporal que corresponde al agua; indica el estado de hidratación.', 5, '', NULL, NULL, '2025-06-11 17:56:35', '201', NULL, NULL),
('9207f443-7d4f-4eb2-8c37-f6db4d511e17', 'e6861593-7327-4f63-9511-11d56f5398dc', 'HDL Cholesterol - Female', 'mg/dL', '50', '999.99', '≤50 mg/dL (Hypoalphalipoproteinemia)', 'Not clinically significant', 'High-density lipoprotein; higher levels are generally protective against heart disease in females.', 'Colesterol HDL - Mujer', '≤50 mg/dL (Hipoalfalipoproteinemia)', 'No clínicamente significativo', 'Lipoproteína de alta densidad; niveles altos suelen ser protectores contra enfermedades cardíacas en mujeres.', 5, '', NULL, NULL, '2025-06-11 17:56:00', '201', NULL, NULL),
('93a884c5-2b2e-4dbe-9a6f-cd685a042a9c', '60819af9-0533-472c-9d5a-24a5df5a83f7', 'Albumin-to-Creatinine Ratio', 'mg/g', '0', '29', 'Normal ACR', 'Microalbuminuria', 'ACR is used to detect kidney damage by measuring albumin excretion relative to creatinine.', 'Relación Albúmina-Creatinina (ACR)', 'ACR normal', 'Microalbuminuria', 'El ACR se usa para detectar daño renal midiendo la excreción de albúmina en relación con la creatinina.', 10, '', '2025-06-20 14:54:00', '1', '2025-06-20 16:56:46', '1', NULL, NULL),
('97ed28dd-80ea-4cfe-b087-f2d74593086e', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Body Mass Index (BMI)', 'Index', '18.5', '24.9', '<18.5 (Underweight)', '>26 (Overweight/Obesity)', 'Body fat based on height and weight; used to classify underweight, normal, overweight, and obesity.', 'Índice de Masa Corporal (IMC)', '<18.5 (Bajo peso)', '>26 (Sobrepeso/Obesidad)', 'Grasa corporal basada en altura y peso se usa para clasificar bajo peso, normal, sobrepeso y obesidad.', 5, '', NULL, NULL, '2025-06-11 17:55:49', '201', NULL, NULL),
('d2692ce5-cda1-4577-a661-86efde7e57d6', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Muscle Mass Percentage - Female', '%', '30', '40', '<30% (Muscle Wasting)', '>40% (Hypermuscularity)', 'Muscle proportion in female bodies; essential for strength and metabolic health.', 'Porcentaje de Masa Muscular - Mujer', '<30% (Pérdida muscular)', '>40% (Exceso de masa muscular)', 'Proporción muscular en el cuerpo femenino; esencial para fuerza y salud metabólica.', 5, '', NULL, NULL, '2025-06-11 17:56:19', '201', NULL, NULL),
('e10ffc15-4829-450a-953a-f2aebfbee1f5', 'e6861593-7327-4f63-9511-11d56f5398dc', 'HDL Cholesterol - Male', 'mg/dL', '40.01', '999.99', '≤40 mg/dL (Hypoalphalipoproteinemia)', 'Not clinically significant', 'High-density lipoprotein; higher levels are generally protective against heart disease in males.', 'Colesterol HDL - Hombre', '≤40 mg/dL (Hipoalfalipoproteinemia)', 'No clínicamente significativo', 'Lipoproteína de alta densidad; niveles altos suelen ser protectores contra enfermedades cardíacas en hombres.', 5, '', NULL, NULL, '2025-06-11 17:56:04', '201', NULL, NULL),
('f3328cee-dc7a-45aa-b513-721389258a5b', 'e6861593-7327-4f63-9511-11d56f5398dc', 'Total Cholesterol', 'mg/dL', '0', '199.99', 'Not clinically significant', '≥200 mg/dL (Hypercholesterolemia)', 'Sum of HDL, LDL, and other lipid components; high levels may indicate cardiovascular risk.', 'Colesterol Total', 'No clínicamente significativo', '≥200 mg/dL (Hipercolesterolemia)', 'Suma del HDL, LDL y otros lípidos; niveles altos pueden indicar riesgo cardiovascular.', 5, '', NULL, NULL, '2025-06-11 17:56:39', '201', NULL, NULL),
('f66c43f7-b282-497e-afd0-593fcd0e0f96', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 'Body Age', 'Age', '0', '5', 'Not applicable', '> Chronological Age (Accelerated Aging)', 'Compares biological body age to actual age; higher age suggests faster aging.', 'Edad Corporal', 'No aplicable', '> Edad cronológica (Envejecimiento acelerado)', 'Compara la edad biológica del cuerpo con la edad real; una edad más alta sugiere envejecimiento acelerado.', 5, '', NULL, NULL, '2025-06-18 16:33:00', '1', NULL, NULL);

--
-- Disparadores `biomarkers`
--
DELIMITER $$
CREATE TRIGGER `trg_biomarkers_delete` BEFORE DELETE ON `biomarkers` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'biomarkers', OLD.biomarker_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'biomarker_id', OLD.biomarker_id,
      'panel_id', OLD.panel_id,
      'name', OLD.name,
      'unit', OLD.unit,
      'reference_min', OLD.reference_min,
      'reference_max', OLD.reference_max,
      'deficiency_label', OLD.deficiency_label,
      'excess_label', OLD.excess_label,
      'description', OLD.description,
      'name_es', OLD.name_es,
      'deficiency_es', OLD.deficiency_es,
      'excess_es', OLD.excess_es,
      'description_es', OLD.description_es,
      'max_exam', OLD.max_exam,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_biomarkers_delete_logical` AFTER UPDATE ON `biomarkers` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'biomarkers', OLD.biomarker_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'biomarker_id', OLD.biomarker_id,
        'panel_id', OLD.panel_id,
        'name', OLD.name,
        'unit', OLD.unit,
        'reference_min', OLD.reference_min,
        'reference_max', OLD.reference_max,
        'deficiency_label', OLD.deficiency_label,
        'excess_label', OLD.excess_label,
        'description', OLD.description,
        'name_es', OLD.name_es,
        'deficiency_es', OLD.deficiency_es,
        'excess_es', OLD.excess_es,
        'description_es', OLD.description_es,
        'max_exam', OLD.max_exam,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_biomarkers_insert` AFTER INSERT ON `biomarkers` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'biomarkers', NEW.biomarker_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'biomarker_id', NEW.biomarker_id,
      'panel_id', NEW.panel_id,
      'name', NEW.name,
      'unit', NEW.unit,
      'reference_min', NEW.reference_min,
      'reference_max', NEW.reference_max,
      'deficiency_label', NEW.deficiency_label,
      'excess_label', NEW.excess_label,
      'description', NEW.description,
      'name_es', NEW.name_es,
      'deficiency_es', NEW.deficiency_es,
      'excess_es', NEW.excess_es,
      'description_es', NEW.description_es,
      'max_exam', NEW.max_exam,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_biomarkers_update` AFTER UPDATE ON `biomarkers` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.panel_id <> NEW.panel_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"panel_id":{"old":"', escape_json(OLD.panel_id), '","new":"', escape_json(NEW.panel_id), '"}');
  END IF;
  IF OLD.name <> NEW.name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name":{"old":"', escape_json(OLD.name), '","new":"', escape_json(NEW.name), '"}');
  END IF;
  IF OLD.unit <> NEW.unit THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"unit":{"old":"', escape_json(OLD.unit), '","new":"', escape_json(NEW.unit), '"}');
  END IF;
  IF OLD.reference_min <> NEW.reference_min THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"reference_min":{"old":"', escape_json(OLD.reference_min), '","new":"', escape_json(NEW.reference_min), '"}');
  END IF;
  IF OLD.reference_max <> NEW.reference_max THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"reference_max":{"old":"', escape_json(OLD.reference_max), '","new":"', escape_json(NEW.reference_max), '"}');
  END IF;
  IF OLD.deficiency_label <> NEW.deficiency_label THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"deficiency_label":{"old":"', escape_json(OLD.deficiency_label), '","new":"', escape_json(NEW.deficiency_label), '"}');
  END IF;
  IF OLD.excess_label <> NEW.excess_label THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"excess_label":{"old":"', escape_json(OLD.excess_label), '","new":"', escape_json(NEW.excess_label), '"}');
  END IF;
  IF OLD.description <> NEW.description THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"description":{"old":"', escape_json(OLD.description), '","new":"', escape_json(NEW.description), '"}');
  END IF;
  IF OLD.name_es <> NEW.name_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name_es":{"old":"', escape_json(OLD.name_es), '","new":"', escape_json(NEW.name_es), '"}');
  END IF;
  IF OLD.deficiency_es <> NEW.deficiency_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"deficiency_es":{"old":"', escape_json(OLD.deficiency_es), '","new":"', escape_json(NEW.deficiency_es), '"}');
  END IF;
  IF OLD.excess_es <> NEW.excess_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"excess_es":{"old":"', escape_json(OLD.excess_es), '","new":"', escape_json(NEW.excess_es), '"}');
  END IF;
  IF OLD.description_es <> NEW.description_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"description_es":{"old":"', escape_json(OLD.description_es), '","new":"', escape_json(NEW.description_es), '"}');
  END IF;
  IF OLD.max_exam <> NEW.max_exam THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"max_exam":{"old":"', escape_json(OLD.max_exam), '","new":"', escape_json(NEW.max_exam), '"}');
  END IF;

  -- Si quieres registrar también updated_at / updated_by:
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'biomarkers', OLD.biomarker_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `body_composition`
--

CREATE TABLE `body_composition` (
  `body_composition_id` varchar(255) NOT NULL,
  `user_id` char(36) NOT NULL,
  `composition_date` date NOT NULL,
  `composition_time` time NOT NULL,
  `weight_lb` decimal(5,2) NOT NULL,
  `bmi` decimal(4,2) NOT NULL,
  `body_fat_pct` decimal(4,1) NOT NULL,
  `water_pct` decimal(4,1) NOT NULL,
  `muscle_pct` decimal(4,1) NOT NULL,
  `resting_metabolism` int(255) NOT NULL,
  `visceral_fat` int(255) NOT NULL,
  `body_age` int(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `body_composition`
--
DELIMITER $$
CREATE TRIGGER `trg_body_composition_delete` BEFORE DELETE ON `body_composition` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'body_composition', OLD.body_composition_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'body_composition_id', OLD.body_composition_id,
      'user_id', OLD.user_id,
      'composition_date', OLD.composition_date,
      'composition_time', OLD.composition_time,
      'weight_lb', OLD.weight_lb,
      'bmi', OLD.bmi,
      'body_fat_pct', OLD.body_fat_pct,
      'water_pct', OLD.water_pct,
      'muscle_pct', OLD.muscle_pct,
      'resting_metabolism', OLD.resting_metabolism,
      'visceral_fat', OLD.visceral_fat,
      'body_age', OLD.body_age,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_body_composition_delete_logical` AFTER UPDATE ON `body_composition` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'body_composition', OLD.body_composition_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'body_composition_id', OLD.body_composition_id,
        'user_id', OLD.user_id,
        'composition_date', OLD.composition_date,
        'composition_time', OLD.composition_time,
        'weight_lb', OLD.weight_lb,
        'bmi', OLD.bmi,
        'body_fat_pct', OLD.body_fat_pct,
        'water_pct', OLD.water_pct,
        'muscle_pct', OLD.muscle_pct,
        'resting_metabolism', OLD.resting_metabolism,
        'visceral_fat', OLD.visceral_fat,
        'body_age', OLD.body_age,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_body_composition_insert` AFTER INSERT ON `body_composition` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'body_composition', NEW.body_composition_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'body_composition_id', NEW.body_composition_id,
      'user_id', NEW.user_id,
      'composition_date', NEW.composition_date,
      'composition_time', NEW.composition_time,
      'weight_lb', NEW.weight_lb,
      'bmi', NEW.bmi,
      'body_fat_pct', NEW.body_fat_pct,
      'water_pct', NEW.water_pct,
      'muscle_pct', NEW.muscle_pct,
      'resting_metabolism', NEW.resting_metabolism,
      'visceral_fat', NEW.visceral_fat,
      'body_age', NEW.body_age,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_body_composition_update` AFTER UPDATE ON `body_composition` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}');
  END IF;
  IF OLD.composition_date <> NEW.composition_date THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"composition_date":{"old":"', escape_json(OLD.composition_date), '","new":"', escape_json(NEW.composition_date), '"}');
  END IF;
  IF OLD.composition_time <> NEW.composition_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"composition_time":{"old":"', escape_json(OLD.composition_time), '","new":"', escape_json(NEW.composition_time), '"}');
  END IF;
  IF OLD.weight_lb <> NEW.weight_lb THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"weight_lb":{"old":"', escape_json(OLD.weight_lb), '","new":"', escape_json(NEW.weight_lb), '"}');
  END IF;
  IF OLD.bmi <> NEW.bmi THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"bmi":{"old":"', escape_json(OLD.bmi), '","new":"', escape_json(NEW.bmi), '"}');
  END IF;
  IF OLD.body_fat_pct <> NEW.body_fat_pct THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"body_fat_pct":{"old":"', escape_json(OLD.body_fat_pct), '","new":"', escape_json(NEW.body_fat_pct), '"}');
  END IF;
  IF OLD.water_pct <> NEW.water_pct THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"water_pct":{"old":"', escape_json(OLD.water_pct), '","new":"', escape_json(NEW.water_pct), '"}');
  END IF;
  IF OLD.muscle_pct <> NEW.muscle_pct THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"muscle_pct":{"old":"', escape_json(OLD.muscle_pct), '","new":"', escape_json(NEW.muscle_pct), '"}');
  END IF;
  IF OLD.resting_metabolism <> NEW.resting_metabolism THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"resting_metabolism":{"old":"', escape_json(OLD.resting_metabolism), '","new":"', escape_json(NEW.resting_metabolism), '"}');
  END IF;
  IF OLD.visceral_fat <> NEW.visceral_fat THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"visceral_fat":{"old":"', escape_json(OLD.visceral_fat), '","new":"', escape_json(NEW.visceral_fat), '"}');
  END IF;
  IF OLD.body_age <> NEW.body_age THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"body_age":{"old":"', escape_json(OLD.body_age), '","new":"', escape_json(NEW.body_age), '"}');
  END IF;

  -- También registramos updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  -- Si también quieres auditar borrado lógico dentro de UPDATE:
  IF OLD.deleted_at <> NEW.deleted_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"deleted_at":{"old":"', escape_json(OLD.deleted_at), '","new":"', escape_json(NEW.deleted_at), '"}');
  END IF;
  IF OLD.deleted_by <> NEW.deleted_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"deleted_by":{"old":"', escape_json(OLD.deleted_by), '","new":"', escape_json(NEW.deleted_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'body_composition', OLD.body_composition_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cities`
--

CREATE TABLE `cities` (
  `city_id` char(36) NOT NULL,
  `state_id` char(36) NOT NULL,
  `country_id` char(36) NOT NULL,
  `city_name` varchar(150) NOT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `cities`
--

INSERT INTO `cities` (`city_id`, `state_id`, `country_id`, `city_name`, `timezone`, `latitude`, `longitude`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('01fe0d28-988d-4237-8ec0-c405aeaaf250', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Rubio', NULL, 7.70131000, -72.35569000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('049c1c5e-31a3-4eab-8812-65b1a7837e26', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Tocuyito', NULL, 10.11347000, -68.06783000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('0692274c-cc92-41a5-b55c-f444ca20cdda', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Punta Cardón', NULL, 11.65806000, -70.21500000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('084b9817-3cdc-4593-9fd4-3ce1c2a4560a', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cabimas', NULL, 10.39907000, -71.45206000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('09349737-2520-4475-b68e-8704860573aa', '351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Zaraza', NULL, 9.35029000, -65.32452000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('09eafbe4-7b6a-4b2e-901f-dfd5c7a01d69', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Dolorita', NULL, 10.48830000, -66.78608000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('0b058074-a1e0-433c-a9bd-38585d0d5add', '4128d25f-f85e-4b98-bf88-7ce264f0d323', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Maturín', NULL, 9.40000000, -63.03333000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('0c3aed44-54d8-4b30-b603-267e69548797', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Juan de Manapiare', NULL, 5.32665000, -66.05402000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('12236588-abe0-4bff-8fd5-f36243164204', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Tocuyo', NULL, 9.78709000, -69.79294000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('13fea080-b93c-48b3-ba86-d21c0dccabcb', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Santa Teresa del Tuy', NULL, 10.23291000, -66.66474000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('14ab2b8b-5cf2-49df-bf13-4cf301b7b8a9', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Táriba', NULL, 7.81880000, -72.22427000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('158201c6-588d-4cb9-b918-fd470f7a493b', '8925b8fc-efb4-4fdb-95bf-b6145d1a665e', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Alto Barinas', NULL, 8.59310000, -70.22610000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('1ed22fcd-b2db-4bdf-af12-b3225ad0b981', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cabudare', NULL, 10.02658000, -69.26203000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('1f1a293c-ed58-46bf-bafa-552860a37091', '351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Altagracia de Orituco', NULL, 9.86005000, -66.38139000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('1fb2004b-c695-4bc7-8c3c-63141c11c3eb', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Puerto Ayacucho', NULL, 5.66049000, -67.58343000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('22b18f87-4cb9-4240-afe6-46fef93078ec', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Limón', NULL, 10.30589000, -67.63212000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('2387b5bc-92b1-4124-a429-5b4f00bc14e9', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Carrizal', NULL, 10.34985000, -66.98632000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('2522ffb4-48ab-4d72-b4c1-e62317ef65b5', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Los Teques', NULL, 10.34447000, -67.04325000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('2661654f-fdf2-49a0-956f-8571da66e535', '4128d25f-f85e-4b98-bf88-7ce264f0d323', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Uracoa', NULL, 8.99960000, -62.35164000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('2823ceab-6256-45c9-90fc-a59026e90b97', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Los Dos Caminos', NULL, 10.49389000, -66.82863000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('29e8ca5b-e0d8-4518-a373-a0b90cf4e641', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio José Gregorio Monagas', NULL, 7.73874000, -64.71876000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('2db744fc-fd91-46e0-ba20-0e2d4f470136', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Aragua de Barcelona', NULL, 9.45588000, -64.82928000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('300f1758-0777-4982-8539-78f9cc4db06f', '004a5632-aa7e-4977-b028-b813fa64a66d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Maiquetía', NULL, 10.59450000, -66.95624000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('30ec3077-b29d-4953-b328-97cc5056293e', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Carlos de Río Negro', NULL, 1.92027000, -67.06089000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('330e5c8f-3449-4825-af18-bde48139a4b4', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Charallave', NULL, 10.24247000, -66.85723000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('3388cf37-0528-4bb6-916c-69d7c9eb0443', 'cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Boconó', NULL, 9.25385000, -70.25105000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('33c8fcd7-39a7-44cd-9368-2ac21a7a005d', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio José María Vargas', NULL, 8.03514000, -72.05675000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('346225bc-38e6-4d6a-b7dd-0abb471232d3', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Guarenas', NULL, 10.47027000, -66.61934000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('3e3574df-698b-4776-9eee-cb7b1963e98f', '9d5806a9-a597-4331-975f-d94d14d0c34f', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Carúpano', NULL, 10.66516000, -63.25387000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('41304937-0f8a-4aeb-a976-4fe1896af3b1', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Villa de Cura', NULL, 10.03863000, -67.48938000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('42b1743e-a5e5-424a-8687-45302ee6673e', '8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Independencia', NULL, 10.33472000, -68.75555000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('42bff144-223c-4dda-99b9-30c9553050d5', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Barquisimeto', NULL, 10.06470000, -69.35703000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('4b516bc9-071d-443d-ba5f-258547909518', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San José de Guanipa', NULL, 8.88724000, -64.16512000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('4b7d2525-44dd-495f-8d68-ae014cb6cc69', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Quíbor', NULL, 9.92866000, -69.62010000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('4d5db67d-c157-4441-8232-50534c0abdd8', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Lagunillas', NULL, 10.13008000, -71.25946000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('4dff7db3-e3fe-429a-b609-c212d3f42831', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Colón', NULL, 8.03125000, -72.26053000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('4ebf5d6b-76b6-4b75-8513-f9403c9f06e4', '1035c65e-5189-4021-9245-7ffa7c421795', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Porlamar', NULL, 10.95771000, -63.86971000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5152a645-6b3a-448b-acb2-eda346f3ad4c', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Anaco', NULL, 9.42958000, -64.46428000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('52eda233-5634-49b6-8e94-e019eb6177ea', '004a5632-aa7e-4977-b028-b813fa64a66d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Catia La Mar', NULL, 10.60545000, -67.03238000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5309b19e-f658-4312-8aa8-f7d582d464ea', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Los Taques', NULL, 11.82308000, -70.25353000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5c4c77e4-4eac-4d71-bdb9-931f15b6bac7', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Joaquín', NULL, 10.26061000, -67.79348000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5cc27431-baeb-43c8-811e-fcd0a2d41e09', '9d5806a9-a597-4331-975f-d94d14d0c34f', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Güiria', NULL, 10.57721000, -62.29841000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5d6b04b6-e4cc-4ee7-ba90-87bd995f4dbd', '532c8da4-cd4b-4d07-b0d4-c3b69f7c262d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Tucupita', NULL, 9.05806000, -62.05000000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('5fcee9fb-50d1-492b-8afe-43a2d13019e1', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Ocumare del Tuy', NULL, 10.11820000, -66.77513000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('62975bff-9f22-41d6-8126-a810673cf677', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Carora', NULL, 10.17283000, -70.08100000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('653a0d43-ad90-4d71-848b-315492b7bb9d', 'cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Pampanito', NULL, 9.41147000, -70.49592000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('6a661ddb-86b2-4740-b6fe-04501d710611', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Caucagüito', NULL, 10.48666000, -66.73799000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('6c58ac36-d652-484b-949b-9d77c824b315', 'abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Ciudad Guayana', NULL, 8.35122000, -62.64102000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('6f6bcba5-d3d1-4320-84d6-8a7d1e3fed21', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Autónomo Alto Orinoco', NULL, 2.73456000, -64.83032000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('705e3fcb-8143-4e8d-b2aa-2fdef9ca0644', '351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Valle de La Pascua', NULL, 9.21554000, -66.00734000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7068e52d-4368-424c-8056-714c913ae840', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Maracaibo', NULL, 10.66663000, -71.61245000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('72cf384c-c52a-4ce4-b737-15a0f5ece5c9', '8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Felipe', NULL, 10.33991000, -68.74247000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('73c0e2d7-e4ac-482a-bcbb-aa9e4716fcbd', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Antonio de Los Altos', NULL, 10.38853000, -66.95179000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7423eda5-d4f2-43c8-ab1e-a696e38b9739', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Fría', NULL, 8.21523000, -72.24888000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('789e887d-371c-48f6-9be8-0e36cd6c2622', 'abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Santa Elena de Uairén', NULL, 4.60226000, -61.11025000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7983a27f-17bf-42b0-9b34-db9c8b5fe89c', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Guatire', NULL, 10.47400000, -66.54241000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7a9970f0-f42c-475e-84ec-183fcd9d2944', '351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Juan de los Morros', NULL, 9.91152000, -67.35381000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7ad59c95-cb13-4364-b02e-f86f010f3ad0', '1035c65e-5189-4021-9245-7ffa7c421795', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Asunción', NULL, 11.03333000, -63.86278000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7c318394-f03d-4b25-83e8-ce300a0f29b5', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Machiques', NULL, 10.06077000, -72.55212000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7c502904-9d3e-4bb3-b52a-a8d89030ca66', '8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Chivacoa', NULL, 10.15951000, -68.89453000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7c78f966-9d6b-45f7-bd34-24cba152c971', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Palo Negro', NULL, 10.17389000, -67.54194000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7d604a78-5266-4865-a1b4-bb1299c14820', '8925b8fc-efb4-4fdb-95bf-b6145d1a665e', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Barinitas', NULL, 8.76171000, -70.41199000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7ece60c5-738d-4c6d-b683-7c5fb23fcf61', '004a5632-aa7e-4977-b028-b813fa64a66d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Caraballeda', NULL, 10.61216000, -66.85192000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7feba4c0-a8bb-4ef4-92fe-91fd744c976d', '680549a3-58ea-4b1b-8b5d-2215528c10c4', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Fernando de Apure', NULL, 7.87266930, -67.48193280, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('8052eb2b-dbb3-4c7f-bf22-dc7350f737f1', '76ee1322-fe05-407c-996c-16dbd9c10005', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Guanare', NULL, 9.04183000, -69.74206000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('81df9fed-8005-4fe0-856c-1c110ad0dcc4', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Onoto', NULL, 9.59714000, -65.19350000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('828b53e2-48e7-433f-8f1a-18cd973af191', 'abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Padre Pedro Chien', NULL, 8.02455000, -61.88187000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('84b3e13f-cc9d-4f2e-8110-6386d483ec01', '76ee1322-fe05-407c-996c-16dbd9c10005', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Villa Bruzual', NULL, 9.33186000, -69.11968000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('864dd3fc-8775-4842-b1cf-19fad36b6add', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Coro', NULL, 11.40450000, -69.67344000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('883498f3-10ab-45ec-8cd0-f45f063b8ff9', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Morón', NULL, 10.48715000, -68.20078000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('89fa0613-e10c-44ca-9487-0b7dd12be0d0', '004a5632-aa7e-4977-b028-b813fa64a66d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Guaira', NULL, 10.60156000, -66.93293000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('8c032e42-d799-4ae1-958d-09ffee3d3d17', 'cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Trujillo', NULL, 9.36583000, -70.43694000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('8fa10bd4-1d36-4fb6-ba37-18283b4efa16', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cagua', NULL, 10.18634000, -67.45935000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('93b9b54c-a134-417e-8d1a-4418c973dc7e', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Miranda', NULL, 11.31667000, -69.86667000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('97c00d8a-3138-421d-a294-cde8744cb70d', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Villa del Rosario', NULL, 10.32580000, -72.31343000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('9d711efb-b8cc-4b43-92bc-279da1f5dbd0', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Tacarigua', NULL, 10.08621000, -67.91982000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('a0ac9e7d-bef5-4653-81da-cd9aa82dc446', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Santa Rita', NULL, 10.20540000, -67.55948000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('a4ea1ee4-afaf-4812-b6a8-495b869e1436', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Cristóbal', NULL, 7.76694000, -72.22500000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('a85a5941-5f0a-4625-a7ea-bf0ff2443e41', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cantaura', NULL, 9.30571000, -64.35841000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('a86270e4-d067-4b57-b6f9-b7c86c773947', 'cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio San Rafael de Carvajal', NULL, 9.30756000, -70.58965000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('aa2857b3-dc44-446a-a43b-6f8bcb9afcfc', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Tucacas', NULL, 10.79006000, -68.32564000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b17e52e5-0a24-4e8b-bdf4-6dc9fc5e1a6a', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Guacara', NULL, 10.22609000, -67.87700000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b2417495-7a39-4899-91c6-dd149c3bdf11', '71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Libertador', NULL, 8.33333000, -71.11667000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b2c2d008-4b07-46e4-89ee-0e4f2150635b', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Mariara', NULL, 10.29532000, -67.71770000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b2ffa6bc-7214-4109-b848-f534a1d3a17c', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Carlos del Zulia', NULL, 9.00098000, -71.92683000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b3eb8381-e3e6-4ae3-abcd-46f0835ad5b1', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Fernando de Atabapo', NULL, 4.04564000, -67.69934000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('b437254b-038c-44eb-bdf2-b0bbb9b75011', '4128d25f-f85e-4b98-bf88-7ce264f0d323', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Caripito', NULL, 10.11135000, -63.09985000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b487ccfc-14f6-4268-aa08-9317cac42824', '4128d25f-f85e-4b98-bf88-7ce264f0d323', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Maturín', NULL, 9.74569000, -63.18323000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b6003b5d-8975-4007-b87e-b17f574df2ed', '8925b8fc-efb4-4fdb-95bf-b6145d1a665e', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Barinas', NULL, 8.62261000, -70.20749000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('b8c8ab2c-ec99-4412-9ef6-df989db206b0', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Puerto Píritu', NULL, 10.05896000, -65.03698000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('bc612334-c150-469e-a7e8-ba4d9194b163', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Tigre', NULL, 8.88902000, -64.25270000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('bd285b9a-e61c-4428-9c0e-a0a85ca73e3f', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Cafetal', NULL, 10.46541000, -66.82951000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('be88c53a-f5cd-47de-a66b-731563739955', 'cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Valera', NULL, 9.31778000, -70.60361000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('bfeeac81-43a1-4378-9f09-77e50ac97c1b', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Mateo', NULL, 10.21302000, -67.42365000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c22d20f5-5eae-4ba4-b68d-ca6057b5af74', '137a2652-c0e5-4684-b658-f48e51ece6a0', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Tinaquillo', NULL, 9.91861000, -68.30472000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c259da5a-2d89-4cb7-96c6-297beefdf26d', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cúa', NULL, 10.16245000, -66.88248000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c3234bad-6836-44dc-bcfa-5b5869b11bb3', '8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Nirgua', NULL, 10.15039000, -68.56478000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c82a1219-22d7-42d5-9e7a-cc48d951d693', '71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Mérida', NULL, 8.58972000, -71.15611000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c9165d6f-ef81-4e27-9ab1-64cc8f835efb', 'abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Upata', NULL, 8.01620000, -62.40561000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('c9e9da2d-05ba-437c-bf1d-5c0b92e842d9', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Petare', NULL, 10.47679000, -66.80786000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('ca237ede-53db-4e97-aa5d-84b00b602892', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Grita', NULL, 8.13316000, -71.98390000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('ce8533a6-1be2-46d3-9553-47020e33e89b', '76ee1322-fe05-407c-996c-16dbd9c10005', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Araure', NULL, 9.58144000, -69.23851000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('d146a23f-5a85-4e5f-9889-ec43b93865a1', '9d5806a9-a597-4331-975f-d94d14d0c34f', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Valdez', NULL, 10.57945000, -62.30029000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('d6310e3d-1a52-4508-afc9-59ebbebbbac9', '137a2652-c0e5-4684-b658-f48e51ece6a0', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Carlos', NULL, 9.66124000, -68.58268000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('d9527bf1-ac59-454b-8712-a064551dc085', '9d5806a9-a597-4331-975f-d94d14d0c34f', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cumaná', NULL, 10.45397000, -64.18256000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('dc880e8a-dc6e-4d19-b4c9-bca88ebcf5be', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Puerto La Cruz', NULL, 10.21382000, -64.63280000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('dcfeae6b-6017-4d90-9101-c16f62c5c140', 'abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Ciudad Bolívar', NULL, 8.12923000, -63.54086000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('decdd64b-3313-4232-ba84-0db4a45f59c1', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Turmero', NULL, 10.22856000, -67.47421000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e07381f1-9948-44c7-ae67-eb91ea01af76', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Los Guayos', NULL, 10.18932000, -67.93828000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e19c4461-fd92-49d4-a72f-d32b79aaf658', '8925b8fc-efb4-4fdb-95bf-b6145d1a665e', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Municipio Barinas', NULL, 8.61497000, -70.19852000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e1cbe9dc-b624-4973-9e4f-dfbd69b84761', '76ee1322-fe05-407c-996c-16dbd9c10005', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Acarigua', NULL, 9.55451000, -69.19564000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e26e2edb-1a24-402e-ad0c-29b3a1acaa1b', '71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Vigía', NULL, 8.61350000, -71.65702000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e31f60df-345b-466d-97a9-4644ef8d91cf', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Valencia', NULL, 10.16202000, -68.00765000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e6c8eec0-43ea-4ce9-a7f8-51d679af5bba', '351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Calabozo', NULL, 8.92416000, -67.42929000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e8a1dac0-09c1-4052-a989-23573529cb45', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Puerto Cabello', NULL, 10.47306000, -68.01250000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e9052c11-7fc3-4be8-87da-9f623bea3c5c', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Maracay', NULL, 10.23535000, -67.59113000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('eb3c5927-8f69-4317-bd88-bcc7e903e11f', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Punto Fijo', NULL, 11.69152000, -70.19918000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('ee36f283-cb00-44a6-971b-2b084a1fe909', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Las Tejerías', NULL, 10.25416000, -67.17333000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f011d7bc-1304-474c-979e-641eb5812125', '3cedd23a-c38f-477c-9bdb-5bc78875b137', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Caracas', NULL, 10.50000000, -66.93333333, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f0b58ad8-55f0-4ea1-9eab-3a4fc9dc6cbf', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Santa Rita', NULL, 10.53642000, -71.51104000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f10f720a-21a4-4080-acca-65fcea3f4990', '71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Ejido', NULL, 8.54665000, -71.24087000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f1df4ce2-4037-4a65-b790-2ad7d2d12fe7', '7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'San Antonio del Táchira', NULL, 7.81454000, -72.44310000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f3bc2788-4ad4-4f66-82f1-6904e09473f8', '34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Los Rastrojos', NULL, 10.02588000, -69.24166000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f41328cc-c33c-415b-84f7-92c4156a7924', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'El Hatillo', NULL, 10.42411000, -66.82581000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f4f54018-c6ff-4c5c-9781-613e8ececaf7', '71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Mucumpiz', NULL, 8.41667000, -71.13333000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f573fe2e-8fb7-465b-8eb4-4b178064ddc4', '1035c65e-5189-4021-9245-7ffa7c421795', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Juan Griego', NULL, 11.08172000, -63.96549000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f57d7257-a28c-4205-a503-ad42494e5eee', '0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Chichiriviche', NULL, 10.92872000, -68.27283000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f72a1e18-6cda-4284-b0e8-b044ed2d1767', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Baruta', NULL, 10.43424000, -66.87558000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('f7364aba-ed52-4d4a-a6da-891848d5130e', 'acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Barcelona', NULL, 10.13625000, -64.68618000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('f85b4746-4ed9-4dff-af1c-3c8816d7a25f', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Chacao', NULL, 10.49581000, -66.85367000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('fa0ab48b-65f7-46da-bcce-457533435330', '8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Yaritagua', NULL, 10.08081000, -69.12420000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('faa566b2-929c-475a-b805-a482731135c3', 'c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Ciudad Ojeda', NULL, 10.20161000, -71.31480000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('fb0a9ef8-e37f-4d29-96dd-068f94aa00c0', '58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Caucaguita', NULL, 10.35782000, -66.80252000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('fddab626-1604-44ca-a7e5-d21cc43e1aab', '968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Güigüe', NULL, 10.08344000, -67.77799000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('fdfb5f5d-54b7-4e61-9e33-33d981573201', 'e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Victoria', NULL, 10.22677000, -67.33122000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('feeb9d57-4987-462a-a407-1d661900b969', 'fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Maroa', NULL, 2.71880000, -67.56046000, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comment_biomarker`
--

CREATE TABLE `comment_biomarker` (
  `comment_biomarker_id` char(36) NOT NULL,
  `id_test_panel` char(36) NOT NULL,
  `id_test` char(36) NOT NULL,
  `id_biomarker` char(36) NOT NULL,
  `id_specialist` char(36) DEFAULT NULL,
  `comment` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comment_biomarker`
--

INSERT INTO `comment_biomarker` (`comment_biomarker_id`, `id_test_panel`, `id_test`, `id_biomarker`, `id_specialist`, `comment`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('035a3ea2-20b5-489b-95cc-6ebc525607f1', '81054d57-92c9-4df8-a6dc-51334c1d82c4', '5', '2d1ebb80-baac-4d1f-885e-e27adc343070', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'fsafa', '2025-07-07 18:17:33', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, '2025-07-07 18:18:49', 'fdf23cb0-86f1-4902-85e3-c20a1f481835'),
('2a3a5969-9726-4629-8b9a-4db5b178b625', 'e6861593-7327-4f63-9511-11d56f5398dc', '89199e2a-1b2a-4fdc-9309-297b66bf70cc', 'e10ffc15-4829-450a-953a-f2aebfbee1f5', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'fafafa', '2025-09-15 09:58:25', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL),
('9fda9c3b-2510-47fd-ad26-2257a77dacb7', '81054d57-92c9-4df8-a6dc-51334c1d82c4', '5d20ff07-af6a-45d0-b1d7-0105929b1da5', '2d1ebb80-baac-4d1f-885e-e27adc343070', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'fsafa', '2025-07-07 18:18:57', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL),
('b567e7f9-354d-4864-a471-3e201dc0b3d9', '81054d57-92c9-4df8-a6dc-51334c1d82c4', '5d20ff07-af6a-45d0-b1d7-0105929b1da5', 'f66c43f7-b282-497e-afd0-593fcd0e0f96', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'fsaa', '2025-07-15 13:36:58', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL),
('ce5a2533-c494-4011-9832-57495bf9401b', '81054d57-92c9-4df8-a6dc-51334c1d82c4', '5d20ff07-af6a-45d0-b1d7-0105929b1da5', 'f66c43f7-b282-497e-afd0-593fcd0e0f96', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'fsafa', '2025-07-15 13:36:49', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, '2025-07-15 13:36:54', 'fdf23cb0-86f1-4902-85e3-c20a1f481835');

--
-- Disparadores `comment_biomarker`
--
DELIMITER $$
CREATE TRIGGER `trg_comment_biomarker_delete` BEFORE DELETE ON `comment_biomarker` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'comment_biomarker', OLD.comment_biomarker_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'comment_biomarker_id', OLD.comment_biomarker_id,
      'id_test_panel', OLD.id_test_panel,
      'id_test', OLD.id_test,
      'id_biomarker', OLD.id_biomarker,
      'id_specialist', OLD.id_specialist,
      'comment', OLD.comment,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_comment_biomarker_delete_logical` AFTER UPDATE ON `comment_biomarker` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'comment_biomarker', OLD.comment_biomarker_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'comment_biomarker_id', OLD.comment_biomarker_id,
        'id_test_panel', OLD.id_test_panel,
        'id_test', OLD.id_test,
        'id_biomarker', OLD.id_biomarker,
        'id_specialist', OLD.id_specialist,
        'comment', OLD.comment,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_comment_biomarker_insert` AFTER INSERT ON `comment_biomarker` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'comment_biomarker', NEW.comment_biomarker_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'comment_biomarker_id', NEW.comment_biomarker_id,
      'id_test_panel', NEW.id_test_panel,
      'id_test', NEW.id_test,
      'id_biomarker', NEW.id_biomarker,
      'id_specialist', NEW.id_specialist,
      'comment', NEW.comment,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_comment_biomarker_update` AFTER UPDATE ON `comment_biomarker` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.id_test_panel <> NEW.id_test_panel THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"id_test_panel":{"old":"', escape_json(OLD.id_test_panel), '","new":"', escape_json(NEW.id_test_panel), '"}');
  END IF;
  IF OLD.id_test <> NEW.id_test THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"id_test":{"old":"', escape_json(OLD.id_test), '","new":"', escape_json(NEW.id_test), '"}');
  END IF;
  IF OLD.id_biomarker <> NEW.id_biomarker THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"id_biomarker":{"old":"', escape_json(OLD.id_biomarker), '","new":"', escape_json(NEW.id_biomarker), '"}');
  END IF;
  IF OLD.id_specialist <> NEW.id_specialist THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"id_specialist":{"old":"', escape_json(OLD.id_specialist), '","new":"', escape_json(NEW.id_specialist), '"}');
  END IF;
  IF OLD.comment <> NEW.comment THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"comment":{"old":"', escape_json(OLD.comment), '","new":"', escape_json(NEW.comment), '"}');
  END IF;

  -- (opcional) registra updated_at/updated_by si existen en la tabla
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'comment_biomarker', OLD.comment_biomarker_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_emails`
--

CREATE TABLE `contact_emails` (
  `contact_email_id` char(36) NOT NULL,
  `entity_type` enum('administrator','specialist','user') NOT NULL,
  `entity_id` char(36) NOT NULL,
  `email` varchar(190) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `contact_emails`
--
DELIMITER $$
CREATE TRIGGER `trg_contact_emails_delete` BEFORE DELETE ON `contact_emails` FOR EACH ROW BEGIN
  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'contact_emails', OLD.contact_email_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'contact_email_id', OLD.contact_email_id,
      'entity_type', OLD.entity_type,
      'entity_id', OLD.entity_id,
      'email', OLD.email,
      'is_primary', OLD.is_primary,
      'is_active', OLD.is_active,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_emails_delete_logical` AFTER UPDATE ON `contact_emails` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio del bloque
  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'contact_emails', OLD.contact_email_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'contact_email_id', OLD.contact_email_id,
        'entity_type', OLD.entity_type,
        'entity_id', OLD.entity_id,
        'email', OLD.email,
        'is_primary', OLD.is_primary,
        'is_active', OLD.is_active,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', NEW.updated_at,
        'updated_by', NEW.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_phones`
--

CREATE TABLE `contact_phones` (
  `contact_phone_id` char(36) NOT NULL,
  `entity_type` enum('administrator','specialist','user') NOT NULL,
  `entity_id` char(36) NOT NULL,
  `phone_type` enum('mobile','office','fax','other') NOT NULL DEFAULT 'mobile',
  `country_code` varchar(8) DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `contact_phones`
--
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_delete` BEFORE DELETE ON `contact_phones` FOR EACH ROW BEGIN
  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'contact_phones', OLD.contact_phone_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'contact_phone_id', OLD.contact_phone_id,
      'entity_type', OLD.entity_type,
      'entity_id', OLD.entity_id,
      'phone_type', OLD.phone_type,
      'country_code', OLD.country_code,
      'phone_number', OLD.phone_number,
      'is_primary', OLD.is_primary,
      'is_active', OLD.is_active,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_delete_logical` AFTER UPDATE ON `contact_phones` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'contact_phones', OLD.contact_phone_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'contact_phone_id', OLD.contact_phone_id,
        'entity_type', OLD.entity_type,
        'entity_id', OLD.entity_id,
        'phone_type', OLD.phone_type,
        'country_code', OLD.country_code,
        'phone_number', OLD.phone_number,
        'is_primary', OLD.is_primary,
        'is_active', OLD.is_active,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', NEW.updated_at,
        'updated_by', NEW.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_insert` AFTER INSERT ON `contact_phones` FOR EACH ROW BEGIN
  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'contact_phones', NEW.contact_phone_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'contact_phone_id', NEW.contact_phone_id,
      'entity_type', NEW.entity_type,
      'entity_id', NEW.entity_id,
      'phone_type', NEW.phone_type,
      'country_code', NEW.country_code,
      'phone_number', NEW.phone_number,
      'is_primary', NEW.is_primary,
      'is_active', NEW.is_active,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_contact_phones_update` AFTER UPDATE ON `contact_phones` FOR EACH ROW BEGIN
  -- DECLARE siempre al inicio:
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by char(36)       DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type  VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- Comparaciones null-seguras
  IF (OLD.entity_type <> NEW.entity_type) OR (OLD.entity_type IS NULL AND NEW.entity_type IS NOT NULL) OR (OLD.entity_type IS NOT NULL AND NEW.entity_type IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"entity_type":{"old":"', escape_json(OLD.entity_type), '","new":"', escape_json(NEW.entity_type), '"}');
  END IF;

  IF (OLD.entity_id <> NEW.entity_id) OR (OLD.entity_id IS NULL AND NEW.entity_id IS NOT NULL) OR (OLD.entity_id IS NOT NULL AND NEW.entity_id IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"entity_id":{"old":"', escape_json(OLD.entity_id), '","new":"', escape_json(NEW.entity_id), '"}');
  END IF;

  IF (OLD.phone_type <> NEW.phone_type) OR (OLD.phone_type IS NULL AND NEW.phone_type IS NOT NULL) OR (OLD.phone_type IS NOT NULL AND NEW.phone_type IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"phone_type":{"old":"', escape_json(OLD.phone_type), '","new":"', escape_json(NEW.phone_type), '"}');
  END IF;

  IF (OLD.country_code <> NEW.country_code) OR (OLD.country_code IS NULL AND NEW.country_code IS NOT NULL) OR (OLD.country_code IS NOT NULL AND NEW.country_code IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"country_code":{"old":"', escape_json(OLD.country_code), '","new":"', escape_json(NEW.country_code), '"}');
  END IF;

  IF (OLD.phone_number <> NEW.phone_number) OR (OLD.phone_number IS NULL AND NEW.phone_number IS NOT NULL) OR (OLD.phone_number IS NOT NULL AND NEW.phone_number IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"phone_number":{"old":"', escape_json(OLD.phone_number), '","new":"', escape_json(NEW.phone_number), '"}');
  END IF;

  IF (OLD.is_primary <> NEW.is_primary) OR (OLD.is_primary IS NULL AND NEW.is_primary IS NOT NULL) OR (OLD.is_primary IS NOT NULL AND NEW.is_primary IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"is_primary":{"old":"', escape_json(OLD.is_primary), '","new":"', escape_json(NEW.is_primary), '"}');
  END IF;

  IF (OLD.is_active <> NEW.is_active) OR (OLD.is_active IS NULL AND NEW.is_active IS NOT NULL) OR (OLD.is_active IS NOT NULL AND NEW.is_active IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"is_active":{"old":"', escape_json(OLD.is_active), '","new":"', escape_json(NEW.is_active), '"}');
  END IF;

  IF (OLD.updated_at <> NEW.updated_at) OR (OLD.updated_at IS NULL AND NEW.updated_at IS NOT NULL) OR (OLD.updated_at IS NOT NULL AND NEW.updated_at IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;

  IF (OLD.updated_by <> NEW.updated_by) OR (OLD.updated_by IS NULL AND NEW.updated_by IS NOT NULL) OR (OLD.updated_by IS NOT NULL AND NEW.updated_by IS NULL) THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'contact_phones', OLD.contact_phone_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `countries`
--

CREATE TABLE `countries` (
  `country_id` char(36) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `full_prefix` varchar(20) DEFAULT NULL,
  `normalized_prefix` varchar(10) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `phone_mask` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `countries`
--

INSERT INTO `countries` (`country_id`, `suffix`, `full_prefix`, `normalized_prefix`, `country_name`, `phone_mask`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('00515e61-97a8-425b-a2cb-421258dce0a4', 'NC', 'NC +687', '+687', 'New Caledonia', '+687 ##########', NULL, NULL, '2025-09-12 17:07:11', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('00be12a2-a9bd-44af-873b-67b1eedf28ab', 'PY', 'PY +595', '+595', 'Paraguay', '+595 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('02241709-6796-4802-aa9c-3adfa967bcaa', 'VU', 'VU +678', '+678', 'Vanuatu', '+678 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('023da98e-c2b1-4063-848e-cc28b5bf7f74', 'UM', 'UM +268', '+268', 'United States Minor Outlying Islands', '+268 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('04ff54e0-d703-4406-8e78-eb3c9d65bebd', 'TD', 'TD +235', '+235', 'Chad', '+235 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('07361581-d8ae-4d78-baed-572b6226eff4', 'SV', 'SV +503', '+503', 'El Salvador', '+503 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0895d4fa-829f-43d2-88c0-437c1a7d913a', 'PL', 'PL +48', '+48', 'Poland', '+48 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('09f7908f-fb9f-4b33-8f89-198509125a3c', 'ST', 'ST +239', '+239', 'São Tomé and Príncipe', '+239 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b0422b1-4682-4218-91ef-d634ce4294c1', 'BW', 'BW +267', '+267', 'Botswana', '+267 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b774d90-da1b-44f6-95d2-df02446d990a', 'KG', 'KG +996', '+996', 'Kyrgyzstan', '+996 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b806713-c15f-4518-a17d-33102aa2e8bd', 'KW', 'KW +965', '+965', 'Kuwait', '+965 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0c600409-392d-4905-a3f3-f9fe02f70e2c', 'MF', 'MF +590', '+590', 'Saint Martin', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0ce52136-2a65-46e0-9186-7f9a154b29b2', 'IO', 'IO +246', '+246', 'British Indian Ocean Territory', '+246 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0f9afd8a-6b59-42c9-a507-a8a496e83c47', 'MS', 'MS +1664', '+1664', 'Montserrat', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('117aa9fc-0a95-4fe2-965f-bf34ff2b2bae', 'GS', 'GS +500', '+500', 'South Georgia', '+500 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('11ecc294-4943-4389-9c89-084974f840d7', 'SA', 'SA +966', '+966', 'Saudi Arabia', '+966 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('12a2946b-b242-43c1-90c2-93c0fd5d45bc', 'ZM', 'ZM +260', '+260', 'Zambia', '+260 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('13f29df4-c3d8-4646-a598-d076aa5db905', 'EH', 'EH +2125288', '+2125288', 'Western Sahara', '+2125288 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('150d409f-9dfa-4cb4-9ebd-a500b71e3a37', 'KZ', 'KZ +76', '+76', 'Kazakhstan', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('15a9cb5a-d684-403d-b2a9-cc79187ba4a3', 'GN', 'GN +224', '+224', 'Guinea', '+224 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('172f7a9a-b2d6-4157-bf76-4150f4eab724', 'AT', 'AT +43', '+43', 'Austria', '+43 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('17cd7262-0c6c-48b5-b910-dc1cac8fc3cd', 'SC', 'SC +248', '+248', 'Seychelles', '+248 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('181ae6c0-e13e-4a4a-a6bb-b257a81e96d4', 'BE', 'BE +32', '+32', 'Belgium', '+32 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('186e03f7-8862-4244-ba98-636141d94535', 'VN', 'VN +84', '+84', 'Vietnam', '+84 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('18c21a6a-72bc-4bc2-bfbe-6fbd5c8481d8', 'PR', 'PR +1939', '+1939', 'Puerto Rico', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('18c2d597-2d4f-4979-b6b2-5e28ff1b7ced', 'BV', 'BV +47', '+47', 'Bouvet Island', '+47 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('19a78efc-f89c-4fc0-b209-a49eddeef4f2', 'MT', 'MT +356', '+356', 'Malta', '+356 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1a209735-8a64-438c-bbf0-38184c587a26', 'SG', 'SG +65', '+65', 'Singapore', '+65 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1a80be2c-bbc6-4b52-9c8e-15a18b06636f', 'GG', 'GG +44', '+44', 'Guernsey', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1b8f7f56-3238-41cf-b2ea-d4bc03892006', 'PM', 'PM +508', '+508', 'Saint Pierre and Miquelon', '+508 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1e0f74bd-0fc8-435a-838c-b326175d8abb', 'AM', 'AM +374', '+374', 'Armenia', '+374 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1f8db347-bba7-4dcf-b8ea-4f2c6b630472', 'IR', 'IR +98', '+98', 'Iran', '+98 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('236adbcd-59b2-4e9b-b9e4-661119918ca9', 'PT', 'PT +351', '+351', 'Portugal', '+351 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('243d9bed-232d-42df-a467-9125a3438a09', 'TH', 'TH +66', '+66', 'Thailand', '+66 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('256ae45c-865a-4d63-81da-45e6e2aa7fa6', 'PG', 'PG +675', '+675', 'Papua New Guinea', '+675 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('27b00f30-ba4a-42cd-aa27-dca3a9ae9372', 'SS', 'SS +211', '+211', 'South Sudan', '+211 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2a6ceb75-6bf0-4548-842a-98879a7b3ae2', 'RS', 'RS +381', '+381', 'Serbia', '+381 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2aafd6d1-071c-4863-bd84-15c92f475c8d', 'SH', 'SH +247', '+247', 'Saint Helena, Ascension and Tristan da Cunha', '+247 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2bec19d3-8f4e-45f0-9bad-212301747e28', 'CX', 'CX +61', '+61', 'Christmas Island', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2dc825b1-105d-4806-8237-fc583d829fb9', 'RU', 'RU +74', '+74', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('2df76923-00b8-436f-9bf2-464c26d8a646', 'SI', 'SI +386', '+386', 'Slovenia', '+386 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2ec8166a-f385-4f03-b43f-e1587c9cfe9e', 'SZ', 'SZ +268', '+268', 'Eswatini', '+268 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2feb67cc-1b12-4ae1-be72-db7a17523810', 'DO', 'DO +1809', '+1809', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('30424f52-4928-407e-bb57-5bc81d5c20aa', 'JO', 'JO +962', '+962', 'Jordan', '+962 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('30816865-63e7-4e0b-8c41-ca2058c4bd81', 'JM', 'JM +1876', '+1876', 'Jamaica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('30adab4a-d730-43bc-a367-9c2b70016f35', 'RE', 'RE +262', '+262', 'Réunion', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('32fa3d92-2874-4d38-8192-3a425b66263f', 'TW', 'TW +886', '+886', 'Taiwan', '+886 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('337b9f29-1782-464a-a20c-3f1265b0f886', 'WF', 'WF +681', '+681', 'Wallis and Futuna', '+681 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('33911e4d-e11c-4289-885c-f3cd16dc050e', 'AS', 'AS +1684', '+1684', 'American Samoa', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('33c2dacf-fd24-4ef5-9c62-233672605dcb', 'NR', 'NR +674', '+674', 'Nauru', '+674 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('33e9a261-9b23-4c6a-804b-693c53d91a90', 'BM', 'BM +1441', '+1441', 'Bermuda', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('346ce5e4-b1dd-4a21-a462-a8c5b67c864c', 'TR', 'TR +90', '+90', 'Turkey', '+90 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3555485b-dcf9-4fcb-8b71-bca3fede6ad7', 'UG', 'UG +256', '+256', 'Uganda', '+256 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('360db8df-6095-41a0-9ad2-8cc519a58f65', 'ID', 'ID +62', '+62', 'Indonesia', '+62 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36286fb2-83c2-4d06-bece-27c658dd837f', 'PH', 'PH +63', '+63', 'Philippines', '+63 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36c8de7e-d5c1-4dff-83f4-d47adbd8f0bd', 'HK', 'HK +852', '+852', 'Hong Kong', '+852 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36ec1e0d-d549-4e4f-984d-843f990be788', 'CG', 'CG +242', '+242', 'Republic of the Congo', '+242 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('37459f20-57d9-41f1-a09f-c7fbead93577', 'HR', 'HR +385', '+385', 'Croatia', '+385 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('37e2b227-95b2-45ff-b578-c7c7558bc6de', 'PS', 'PS +970', '+970', 'Palestine', '+970 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3843aba3-bcae-49f2-91b6-3c187c414a1f', 'GW', 'GW +245', '+245', 'Guinea-Bissau', '+245 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a16fd94-0ed2-49f5-9913-b95b889853a8', 'HT', 'HT +509', '+509', 'Haiti', '+509 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a31848b-438f-4b21-bdfe-41e33d9a7314', 'SD', 'SD +249', '+249', 'Sudan', '+249 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a630ca2-5bbb-4d77-8a5f-1cc624be6107', 'MU', 'MU +230', '+230', 'Mauritius', '+230 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3ac7a7f6-a4a7-4e4a-93f1-868e68074e00', 'TG', 'TG +228', '+228', 'Togo', '+228 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3b7b8004-2944-4345-b571-a748e68df5fc', 'VG', 'VG +1284', '+1284', 'British Virgin Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('3cd8564a-6bcd-4206-86e3-42cd594d81b7', 'IQ', 'IQ +964', '+964', 'Iraq', '+964 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3e52e28c-e5ea-4aa4-8845-9e4522f036f1', 'DJ', 'DJ +253', '+253', 'Djibouti', '+253 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3f0a7573-1756-4a5f-8079-5f800358b6d1', 'AZ', 'AZ +994', '+994', 'Azerbaijan', '+994 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3feb9545-1778-415b-9dfe-b5fd9ca08338', 'LV', 'LV +371', '+371', 'Latvia', '+371 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('40dabfb4-0947-488c-924f-4f67bfaa6ade', 'IT', 'IT +39', '+39', 'Italy', '+39 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('41e2a4da-7343-4fa2-a8af-442c9895e92d', 'MV', 'MV +960', '+960', 'Maldives', '+960 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4258c16f-ea69-4832-8070-3e0087670aad', 'FM', 'FM +691', '+691', 'Micronesia', '+691 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('430f1b42-b56b-40b9-b5e8-33b0c96d228f', 'AI', 'AI +1264', '+1264', 'Anguilla', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('452777a5-f606-454c-8863-82dc16c9a3b5', 'PA', 'PA +507', '+507', 'Panama', '+507 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('45c6e8ac-ac5d-4e56-a959-35085844c7d6', 'AX', 'AX +35818', '+35818', 'Åland Islands', '+35818 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4666683d-8062-44ce-b1ae-2458cd31f8de', 'TC', 'TC +1649', '+1649', 'Turks and Caicos Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('4717f9bc-16f7-4905-b77f-b9089eb24eeb', 'JP', 'JP +81', '+81', 'Japan', '+81 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('49df48c1-bc7c-47b9-ae2c-6530d6ab7de6', 'GB', 'GB +44', '+44', 'United Kingdom', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('49ed8c8d-f7cd-4bb0-801e-380310652663', 'RU', 'RU +78', '+78', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('4bec0e66-c030-46cb-880b-5b5a030e9721', 'MG', 'MG +261', '+261', 'Madagascar', '+261 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4d1b7282-ea47-44fe-9287-be4b6f9388d6', 'TJ', 'TJ +992', '+992', 'Tajikistan', '+992 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4e4ebd49-0d86-4a09-8469-1e3dd4e7f916', 'NG', 'NG +234', '+234', 'Nigeria', '+234 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4fbe5e27-4ae8-4d14-be25-5dc17eb5bede', 'SX', 'SX +1721', '+1721', 'Sint Maarten', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('50f5e741-f4c2-4812-8c7c-ce0fc1603696', 'RU', 'RU +79', '+79', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('51ac912b-6d39-4614-ae9d-59c2dd902738', 'BT', 'BT +975', '+975', 'Bhutan', '+975 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('528259cf-102c-4b0c-a02c-99a6f8ddd3a0', 'GF', 'GF +594', '+594', 'French Guiana', '+594 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('538c55c0-a7e4-4fd1-b1be-ffefd136a0a2', 'SH', 'SH +290', '+290', 'Saint Helena, Ascension and Tristan da Cunha', '+290 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('555a2c1c-9740-46ad-ad0b-0569e0145c83', 'KE', 'KE +254', '+254', 'Kenya', '+254 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('555b7767-01f8-4968-957c-baf7cb41b859', 'TK', 'TK +690', '+690', 'Tokelau', '+690 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5575d6c9-0aa7-448c-a4b7-7bf3df94f1b2', 'FI', 'FI +358', '+358', 'Finland', '+358 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5669ad4c-5e5e-4357-9629-bac4fcdea95b', 'GE', 'GE +995', '+995', 'Georgia', '+995 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('56d6ad24-5d5b-4b5a-9717-2b49b161394c', 'RO', 'RO +40', '+40', 'Romania', '+40 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('583da943-9671-4de9-a492-dc04d15fef1f', 'CU', 'CU +53', '+53', 'Cuba', '+53 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5a815258-b828-4b67-a0ee-222c168454d9', 'BJ', 'BJ +229', '+229', 'Benin', '+229 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5ab652a7-a5e3-4561-9fb8-cc29e796cb2a', 'DM', 'DM +1767', '+1767', 'Dominica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('5af36b29-0a70-44fb-b83f-afacf79386f3', 'MM', 'MM +95', '+95', 'Myanmar', '+95 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5b33f793-e4dc-41fc-b1e1-e9a478b8df20', 'IN', 'IN +91', '+91', 'India', '+91 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5baa0939-8041-4347-8918-b10dc372c0e2', 'GP', 'GP +590', '+590', 'Guadeloupe', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f4b1996-898b-41de-8967-fa25073f9022', 'CZ', 'CZ +420', '+420', 'Czechia', '+420 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f53943d-ea4f-42dd-8813-9c36b3e4c6a7', 'NP', 'NP +977', '+977', 'Nepal', '+977 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f7d6817-4868-47e1-8308-85e04e97d351', 'MC', 'MC +377', '+377', 'Monaco', '+377 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('60efcdc8-ad9f-40cc-b87a-2c48a9b76b76', 'ZW', 'ZW +263', '+263', 'Zimbabwe', '+263 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6226422a-4a3c-4f20-b88c-99d55adecd38', 'DO', 'DO +1849', '+1849', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('63005142-7990-49a5-ac06-78bc0a24fae5', 'GD', 'GD +1473', '+1473', 'Grenada', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('65e4f419-d731-4672-a786-f3613a274276', 'FO', 'FO +298', '+298', 'Faroe Islands', '+298 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('66878b27-7801-41d4-bd13-1c228af0e33b', 'MQ', 'MQ +596', '+596', 'Martinique', '+596 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('68cfa791-524e-4a58-bf30-4aa170f6dfcb', 'GA', 'GA +241', '+241', 'Gabon', '+241 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6ccd2437-2f75-4d59-b82f-06a2869f85ad', 'BL', 'BL +590', '+590', 'Saint Barthélemy', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6f37c015-2878-405a-a63d-2e4e1bbe598e', 'IM', 'IM +44', '+44', 'Isle of Man', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('73478714-0d8b-4027-85af-4fa807977615', 'LR', 'LR +231', '+231', 'Liberia', '+231 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7383714f-6513-4481-8001-94834d590408', 'FK', 'FK +500', '+500', 'Falkland Islands', '+500 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('743e11dc-be0f-4299-9beb-6d4af51f7151', 'TF', 'TF +262', '+262', 'French Southern and Antarctic Lands', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('748a87e3-d032-493f-af6e-3f2ffe590fb7', 'AD', 'AD +376', '+376', 'Andorra', '+376 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('75132070-b850-4703-baeb-5d18bba8f4aa', 'SN', 'SN +221', '+221', 'Senegal', '+221 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7683ced1-4248-4249-b71c-adbb41abcfbf', 'CM', 'CM +237', '+237', 'Cameroon', '+237 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('778f23c0-a58d-4e23-b84d-4f87296089c8', 'KI', 'KI +686', '+686', 'Kiribati', '+686 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7834a4f5-7c89-48d9-b2a6-37d6a1980cfb', 'SK', 'SK +421', '+421', 'Slovakia', '+421 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('79662e12-2501-4479-9b92-6dd8dcf9ac7c', 'TM', 'TM +993', '+993', 'Turkmenistan', '+993 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7bf69417-d304-4ede-adab-651a2662e341', 'KN', 'KN +1869', '+1869', 'Saint Kitts and Nevis', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7c6b9c41-b3c2-4865-9a18-a490dd1d4326', 'VI', 'VI +1340', '+1340', 'United States Virgin Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7cd107c1-0e39-4838-baca-4b815f047cb4', 'PR', 'PR +1787', '+1787', 'Puerto Rico', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7cd3c3f0-1d89-4a0e-a197-8aba56caa24f', 'MW', 'MW +265', '+265', 'Malawi', '+265 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7dbe5abf-3888-4528-a470-e4b9212e0f9e', 'CC', 'CC +61', '+61', 'Cocos (Keeling) Islands', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7decda8a-a95c-4f5e-8616-d01b29dc95ce', 'ES', 'ES +34', '+34', 'Spain', '+34 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('81b355e0-99e6-4552-8add-c16dcf94a20b', 'AO', 'AO +244', '+244', 'Angola', '+244 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('81e4c781-e590-44ae-a053-f96e360f6bf1', 'EG', 'EG +20', '+20', 'Egypt', '+20 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('820dcf7c-4c09-4f1d-b807-2ad94bbe66e2', 'TV', 'TV +688', '+688', 'Tuvalu', '+688 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('822852e5-49d8-4755-b18a-69b910bcc074', 'GH', 'GH +233', '+233', 'Ghana', '+233 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('824bbc9b-9341-4b78-860b-4bc996a828cd', 'XK', 'XK +383', '+383', 'Kosovo', '+383 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('85325c6d-2ccf-4702-9629-39a618b88515', 'SE', 'SE +46', '+46', 'Sweden', '+46 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('86c4e3fe-72d9-4680-8dd7-42743ba180b0', 'GL', 'GL +299', '+299', 'Greenland', '+299 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('889907ae-f300-48ad-9742-48c01cf56cbb', 'VA', 'VA +3906698', '+3906698', 'Vatican City', '+3906698 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('891b19ef-5fa2-41be-95f6-86199b1eb04e', 'UZ', 'UZ +998', '+998', 'Uzbekistan', '+998 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8a4d0d30-445d-47c6-8917-894ac26e5c11', 'KR', 'KR +82', '+82', 'South Korea', '+82 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8a9d73f9-b096-459c-82e6-48bc4deea454', 'MD', 'MD +373', '+373', 'Moldova', '+373 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8aa1aee0-8311-4f4b-aef5-0b1eb2eaca2c', 'EH', 'EH +2125289', '+2125289', 'Western Sahara', '+2125289 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8b3be802-68b6-4417-89df-209f9f2f434f', 'TO', 'TO +676', '+676', 'Tonga', '+676 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8c048c15-67f3-429c-8a08-130d7b831e22', 'SJ', 'SJ +4779', '+4779', 'Svalbard and Jan Mayen', '+4779 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8c9d3073-44bc-47dc-b43d-eb2162926ba4', 'VA', 'VA +379', '+379', 'Vatican City', '+379 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8d6fa47e-6ba7-49a5-8cd3-430b619ac80d', 'ER', 'ER +291', '+291', 'Eritrea', '+291 ##########', NULL, NULL, '2025-07-05 07:48:35', '1', NULL, NULL),
('8da21574-f216-49de-93f3-6870839a7535', 'PF', 'PF +689', '+689', 'French Polynesia', '+689 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8fd023db-4874-415b-9471-2c502ef01218', 'AF', 'AF +93', '+93', 'Afghanistan', '+93 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('903935fd-369b-4cae-9ba3-7b774d77db9c', 'AU', 'AU +61', '+61', 'Australia', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('90d6be62-be87-4a2d-8e64-54f5b149a37e', 'LS', 'LS +266', '+266', 'Lesotho', '+266 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('91028643-d017-43b7-90fe-642ba8c16050', 'EE', 'EE +372', '+372', 'Estonia', '+372 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('91a8f0f4-45ec-49c5-a2ab-e4e3c0aa642f', 'BA', 'BA +387', '+387', 'Bosnia and Herzegovina', '+387 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('93f8eba0-fe82-40ee-9a3d-3eee046071fd', 'QA', 'QA +974', '+974', 'Qatar', '+974 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('94fb6f9d-337f-40e9-9d2e-614e8aeddb4a', 'FR', 'FR +33', '+33', 'France', '+33 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('96437b88-4199-4242-b6f1-6f2c3758b406', 'UY', 'UY +598', '+598', 'Uruguay', '+598 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('98244349-53b5-4cb6-bde8-0d7ca2f339d7', 'MN', 'MN +976', '+976', 'Mongolia', '+976 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9eaf4a96-597c-4dda-ae2d-156b022e4a3c', 'CY', 'CY +357', '+357', 'Cyprus', '+357 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9fe5446c-662b-45b6-a96f-9c74eca260ad', 'UA', 'UA +380', '+380', 'Ukraine', '+380 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9feff50f-4194-4624-8391-04aef0bdbf7f', 'JE', 'JE +44', '+44', 'Jersey', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a0489b7c-c740-4906-91a5-cae57e02ff98', 'TZ', 'TZ +255', '+255', 'Tanzania', '+255 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a0854f22-2e32-4028-877d-e6697407cece', 'ZA', 'ZA +27', '+27', 'South Africa', '+27 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a09b8524-1c9b-4325-9278-b3b3d31a8b87', 'NZ', 'NZ +64', '+64', 'New Zealand', '+64 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a1717c67-44c7-431f-a2cf-623b53352a8e', 'BD', 'BD +880', '+880', 'Bangladesh', '+880 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a1e55ca0-c7cd-4b65-9c8b-4974963fec5c', 'BG', 'BG +359', '+359', 'Bulgaria', '+359 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a3cc54ed-46bb-4e3b-8cc0-7fdcbbfe835f', 'OM', 'OM +968', '+968', 'Oman', '+968 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a66d0c87-bbfe-416a-be71-5fc39145c5d9', 'BR', 'BR +55', '+55', 'Brazil', '+55 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a6eee62d-a8a2-48de-be4b-44c55b174167', 'NO', 'NO +47', '+47', 'Norway', '+47 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a76066aa-4cdf-4d15-ae5f-c51be60294d4', 'KZ', 'KZ +77', '+77', 'Kazakhstan', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('a7e59386-0bf6-42be-803c-52b691c784b7', 'VC', 'VC +1784', '+1784', 'Saint Vincent and the Grenadines', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('a867b30d-5672-4ccc-b12f-1e7abdd25fc7', 'LA', 'LA +856', '+856', 'Laos', '+856 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a9cd3fe4-b580-4108-941a-774e695bcb4a', 'YE', 'YE +967', '+967', 'Yemen', '+967 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aaf5bc0b-1f65-4a9b-a3e7-14ed51f9d3f8', 'DO', 'DO +1829', '+1829', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ab31edd4-9ef6-44c9-be49-0918caf74c3b', 'NU', 'NU +683', '+683', 'Niue', '+683 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('acdac3a3-f7c4-4105-b5f4-60ff7bc276c9', 'PE', 'PE +51', '+51', 'Peru', '+51 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ae0899b4-2461-4f66-b75b-8ae4b02d5dd7', 'SY', 'SY +963', '+963', 'Syria', '+963 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ae669915-e4a3-49db-a1ea-62c89f21cff7', 'YT', 'YT +262', '+262', 'Mayotte', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'VE', 'VE +58', '+58', 'Venezuela', '+58 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aeeb222f-98ba-4c2f-80a2-a6698491f9c0', 'ME', 'ME +382', '+382', 'Montenegro', '+382 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('af71f929-2f59-4712-9187-764429bd9def', 'PK', 'PK +92', '+92', 'Pakistan', '+92 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('affa3981-4ea0-4d47-9296-3ba6a00dcee5', 'BN', 'BN +673', '+673', 'Brunei', '+673 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b01b8f01-f543-4b4c-b332-eedc678e90ab', 'GQ', 'GQ +240', '+240', 'Equatorial Guinea', '+240 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b0ae9513-7622-43f5-b08e-fba6b43f20da', 'CR', 'CR +506', '+506', 'Costa Rica', '+506 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b0dfaabc-0382-4b6c-8f02-f3fcd79206e8', 'KM', 'KM +269', '+269', 'Comoros', '+269 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b2ab24d9-953d-47ad-b788-2dbaa6462bdb', 'LY', 'LY +218', '+218', 'Libya', '+218 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b32d6364-b76c-47ec-8baa-f62c28ef674a', 'CW', 'CW +599', '+599', 'Curaçao', '+599 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b356cea1-9df7-4611-a78b-c32b2fd21597', 'BO', 'BO +591', '+591', 'Bolivia', '+591 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b36f16be-ab1e-4abd-9f3b-debbf243bffa', 'ET', 'ET +251', '+251', 'Ethiopia', '+251 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b39d99f7-cdeb-49fb-8ba3-7d91265694fa', 'RU', 'RU +73', '+73', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('b572be68-ce3f-419a-956a-96e73775be76', 'GT', 'GT +502', '+502', 'Guatemala', '+502 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b6b5df8b-0ee1-4841-94f6-2b4c017a2375', 'CD', 'CD +243', '+243', 'DR Congo', '+243 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b7b94516-fd84-453c-85a8-b464daa7e088', 'AE', 'AE +971', '+971', 'United Arab Emirates', '+971 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b84716e2-ae3b-4650-be46-1c4b879d9697', 'MR', 'MR +222', '+222', 'Mauritania', '+222 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b8b4c7e9-a05d-4172-8548-54731bb15f75', 'GY', 'GY +592', '+592', 'Guyana', '+592 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b909f94d-6643-4d25-aa56-114ddddca2a0', 'CO', 'CO +57', '+57', 'Colombia', '+57 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b98ba1d7-2a92-4ab6-a7f1-055275e559e6', 'CL', 'CL +56', '+56', 'Chile', '+56 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ba046ed9-60c4-465c-a471-94afb71ff0a7', 'AW', 'AW +297', '+297', 'Aruba', '+297 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ba103ae9-05c7-4b28-bbf3-4deb1ae2e719', 'GR', 'GR +30', '+30', 'Greece', '+30 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bb3975ef-214e-4ec2-8a33-bda5413f978a', 'NF', 'NF +672', '+672', 'Norfolk Island', '+672 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bd06fd3b-c602-4911-8040-2c07d0e28aae', 'WS', 'WS +685', '+685', 'Samoa', '+685 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bdd72a6a-917c-42c4-8428-8fd26e9a24f2', 'AG', 'AG +1268', '+1268', 'Antigua and Barbuda', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('be4603ac-980c-4c68-8ac2-7e76e8e79592', 'SL', 'SL +232', '+232', 'Sierra Leone', '+232 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('be4a20e5-35bd-41cc-ae36-f08694f0c2fc', 'CH', 'CH +41', '+41', 'Switzerland', '+41 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c00ddc84-ae13-4493-8306-ad41ce1223bb', 'TL', 'TL +670', '+670', 'Timor-Leste', '+670 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c12501a8-a62d-4d69-8a6f-fb3777ef8f73', 'MX', 'MX +52', '+52', 'Mexico', '+52 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c24c2ab6-973e-4af9-bc7f-e5e94e25cb95', 'AR', 'AR +54', '+54', 'Argentina', '+54 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c2fac0b3-69e4-47c7-80ba-61cb6b916f3d', 'LK', 'LK +94', '+94', 'Sri Lanka', '+94 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c3353d1c-3448-400e-9291-e885de816abc', 'JM', 'JM +1658', '+1658', 'Jamaica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('c471e2ad-a866-4087-b27a-e298f4513c8d', 'MZ', 'MZ +258', '+258', 'Mozambique', '+258 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c71c0aa4-0a9b-4c67-950e-21fc93137d29', 'BZ', 'BZ +501', '+501', 'Belize', '+501 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c78368c2-57ec-4e8b-bada-2e2958aa3610', 'BS', 'BS +1242', '+1242', 'Bahamas', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('c7eafc40-4820-4661-95fb-b7de84758842', 'MK', 'MK +389', '+389', 'North Macedonia', '+389 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cb5b3c5c-8971-45b2-b380-b26a6b695881', 'IE', 'IE +353', '+353', 'Ireland', '+353 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cc4411bf-f096-487b-816e-4736a703457d', 'IL', 'IL +972', '+972', 'Israel', '+972 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cced6bb3-270d-4876-b9fa-ff7bfac93086', 'CA', 'CA +1', '+1', 'Canada', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ce194c0d-e85b-44f8-9004-ebb3fc6c65b7', 'IS', 'IS +354', '+354', 'Iceland', '+354 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cf9d5895-764d-4568-b0e7-3d4d4c89f04c', 'HU', 'HU +36', '+36', 'Hungary', '+36 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cfd86892-1018-4e68-ac78-d715b4637445', 'CF', 'CF +236', '+236', 'Central African Republic', '+236 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cfdb7266-b9c5-4b98-aeba-f7bcbadc04a5', 'GU', 'GU +1671', '+1671', 'Guam', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d123a3eb-4fd0-43c7-90e8-c6f8d42b4a39', 'DK', 'DK +45', '+45', 'Denmark', '+45 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d1aecba3-fe25-4743-a375-f56c3e637265', 'SM', 'SM +378', '+378', 'San Marino', '+378 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d308234d-51dc-450f-b45b-8e0df605a204', 'KP', 'KP +850', '+850', 'North Korea', '+850 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d39b491d-08e8-4702-a2d2-cde3b85c20b7', 'TT', 'TT +1868', '+1868', 'Trinidad and Tobago', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d6075a0d-0a57-4635-ba6b-3f3cc2b671d8', 'RU', 'RU +75', '+75', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('d672d7b5-d55a-45b2-b1a4-11f7ca3b7447', 'CV', 'CV +238', '+238', 'Cape Verde', '+238 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d68b29af-2517-4473-b89c-c9cf1d15f8ef', 'CN', 'CN +86', '+86', 'China', '+86 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d7757833-9c19-4e3c-b744-a6e053e82cb8', 'KY', 'KY +1345', '+1345', 'Cayman Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d8f6ed74-3a90-4991-9c76-93daf4ab7c00', 'BB', 'BB +1246', '+1246', 'Barbados', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d96ced36-2535-440d-aff3-041a0e86b15f', 'MY', 'MY +60', '+60', 'Malaysia', '+60 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('da229fab-f417-4b59-8000-b06dcd62f039', 'PN', 'PN +64', '+64', 'Pitcairn Islands', '+64 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('da57e68b-eef6-4b45-a4d1-d9ddb367d25c', 'PW', 'PW +680', '+680', 'Palau', '+680 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('dbfac4fe-0c9a-4e81-9d3e-00a32d5fd7a1', 'HN', 'HN +504', '+504', 'Honduras', '+504 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('dc1f878d-1ea6-41e7-802e-cd0bf3b21054', 'AL', 'AL +355', '+355', 'Albania', '+355 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e01831bb-4275-4975-9a08-6e18c8138063', 'CI', 'CI +225', '+225', 'Ivory Coast', '+225 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e0f4ec83-290d-48d5-b7b0-0fe10cd1d027', 'US', 'US +1', '+1', 'United States', '(###) ###-####', NULL, NULL, '2025-07-11 17:49:55', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('e2a3d229-de87-4530-a14f-3fef04eb83db', 'NL', 'NL +31', '+31', 'Netherlands', '+31 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e42f8984-a2ab-4dc1-940e-580adc718c84', 'DZ', 'DZ +213', '+213', 'Algeria', '+213 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e617c476-5814-439d-bb90-cd3f8582d853', 'SR', 'SR +597', '+597', 'Suriname', '+597 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e6a61cf5-2baf-4749-b23a-5c37bcebab5c', 'MO', 'MO +853', '+853', 'Macau', '+853 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e6a8e1cc-3cb9-426d-8336-677f66b65036', 'MH', 'MH +692', '+692', 'Marshall Islands', '+692 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e75d2b54-60c5-436f-b8e5-d829351fe89b', 'BI', 'BI +257', '+257', 'Burundi', '+257 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e966f156-2913-49d0-a30f-117b3d9c6ad9', 'MA', 'MA +212', '+212', 'Morocco', '+212 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ea9c731b-8cb5-4998-88d4-6f3782867ba2', 'EC', 'EC +593', '+593', 'Ecuador', '+593 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('eadae9d1-f8c3-4c1e-9832-20547c77cc7b', 'NE', 'NE +227', '+227', 'Niger', '+227 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ed3cbbc7-7825-41a1-87e6-ee76d611987d', 'BH', 'BH +973', '+973', 'Bahrain', '+973 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ee0f8afd-2967-4a8b-9368-7c40e89f506b', 'MP', 'MP +1670', '+1670', 'Northern Mariana Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ef1524ef-4de9-48ce-b6c5-36e443081955', 'LU', 'LU +352', '+352', 'Luxembourg', '+352 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ef164c57-fb37-4530-b6cd-da0898ebbd2c', 'GI', 'GI +350', '+350', 'Gibraltar', '+350 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ef96fd8f-8dea-44b7-961d-57c8ff026a86', 'TN', 'TN +216', '+216', 'Tunisia', '+216 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f033e969-e87f-462d-b3e4-56c59b8c60a2', 'NI', 'NI +505', '+505', 'Nicaragua', '+505 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f2bfe512-fc71-4f9a-9038-6426e60e5f71', 'LB', 'LB +961', '+961', 'Lebanon', '+961 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f3e62764-054e-415b-9024-1f52e220c21e', 'ML', 'ML +223', '+223', 'Mali', '+223 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f4e21264-6324-445d-b858-234b75adac56', 'LI', 'LI +423', '+423', 'Liechtenstein', '+423 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f5a2bd5b-9bcc-4c20-a128-3bd79708f4d8', 'LT', 'LT +370', '+370', 'Lithuania', '+370 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f5f82d9c-c855-47ab-ae17-93248eea8603', 'SB', 'SB +677', '+677', 'Solomon Islands', '+677 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f6d24b6d-b90f-4d01-afc0-95910ae9df78', 'NA', 'NA +264', '+264', 'Namibia', '+264 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f7bf610f-68f3-492d-a991-99b0354a83e9', 'BY', 'BY +375', '+375', 'Belarus', '+375 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f7d6020c-faa4-4d91-ba70-14d54af7fa48', 'FJ', 'FJ +679', '+679', 'Fiji', '+679 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f87e2a0a-23f4-40fd-ad50-7c14b4ca1dd5', 'DE', 'DE +49', '+49', 'Germany', '+49 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f96c3c78-0671-4791-9a76-5bfb0e3df8d9', 'BF', 'BF +226', '+226', 'Burkina Faso', '+226 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f9a27e1f-e658-4128-b9da-dd674280a145', 'RW', 'RW +250', '+250', 'Rwanda', '+250 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fa86b3a7-ae9c-4133-9059-36e201564694', 'GM', 'GM +220', '+220', 'Gambia', '+220 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fb73176a-efe9-4fe8-b7f6-0cef3f347199', 'LC', 'LC +1758', '+1758', 'Saint Lucia', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('fe50099f-90d6-4636-ba2e-b6bfcaf01c8a', 'CK', 'CK +682', '+682', 'Cook Islands', '+682 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fe91cf71-9167-4271-9f87-ad54c964ba18', 'SO', 'SO +252', '+252', 'Somalia', '+252 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ff8338e5-82dc-49bb-9efc-556c0f5b2ae2', 'KH', 'KH +855', '+855', 'Cambodia', '+855 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ffa605f1-ebb2-4f49-97d7-0744f03ea06e', 'BQ', 'BQ +599', '+599', 'Caribbean Netherlands', '+599 ##########', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `countries`
--
DELIMITER $$
CREATE TRIGGER `trg_countries_delete` BEFORE DELETE ON `countries` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'countries', OLD.country_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'country_id', OLD.country_id,
      'suffix', OLD.suffix,
      'full_prefix', OLD.full_prefix,
      'normalized_prefix', OLD.normalized_prefix,
      'country_name', OLD.country_name,
      'phone_mask', OLD.phone_mask,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_delete_logical` AFTER UPDATE ON `countries` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'countries', OLD.country_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'country_id', OLD.country_id,
        'suffix', OLD.suffix,
        'full_prefix', OLD.full_prefix,
        'normalized_prefix', OLD.normalized_prefix,
        'country_name', OLD.country_name,
        'phone_mask', OLD.phone_mask,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_insert` AFTER INSERT ON `countries` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'countries', NEW.country_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'country_id', NEW.country_id,
      'suffix', NEW.suffix,
      'full_prefix', NEW.full_prefix,
      'normalized_prefix', NEW.normalized_prefix,
      'country_name', NEW.country_name,
      'phone_mask', NEW.phone_mask,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_update` AFTER UPDATE ON `countries` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.suffix <> NEW.suffix THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"suffix":{"old":"', escape_json(OLD.suffix), '","new":"', escape_json(NEW.suffix), '"}');
  END IF;
  IF OLD.full_prefix <> NEW.full_prefix THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"full_prefix":{"old":"', escape_json(OLD.full_prefix), '","new":"', escape_json(NEW.full_prefix), '"}');
  END IF;
  IF OLD.normalized_prefix <> NEW.normalized_prefix THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"normalized_prefix":{"old":"', escape_json(OLD.normalized_prefix), '","new":"', escape_json(NEW.normalized_prefix), '"}');
  END IF;
  IF OLD.country_name <> NEW.country_name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"country_name":{"old":"', escape_json(OLD.country_name), '","new":"', escape_json(NEW.country_name), '"}');
  END IF;
  IF OLD.phone_mask <> NEW.phone_mask THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"phone_mask":{"old":"', escape_json(OLD.phone_mask), '","new":"', escape_json(NEW.phone_mask), '"}');
  END IF;

  -- (opcional) si quieres registrar también updated_at/updated_by:
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'countries', OLD.country_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `energy_metabolism`
--

CREATE TABLE `energy_metabolism` (
  `energy_metabolism_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `energy_date` date NOT NULL,
  `energy_time` time DEFAULT NULL,
  `glucose` decimal(5,2) NOT NULL,
  `ketone` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `energy_metabolism`
--

INSERT INTO `energy_metabolism` (`energy_metabolism_id`, `user_id`, `energy_date`, `energy_time`, `glucose`, `ketone`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
('701f7857-b578-49af-9e21-f524f541bcb7', '2ea94ca9-90b0-40b4-a119-a1dd60154828', '2025-09-11', '20:43:46', 12.00, 12.00, '2025-09-11 17:40:45', '2ea94ca9-90b0-40b4-a119-a1dd60154828', NULL, NULL, NULL, NULL);

--
-- Disparadores `energy_metabolism`
--
DELIMITER $$
CREATE TRIGGER `trg_energy_metabolism_delete` BEFORE DELETE ON `energy_metabolism` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'energy_metabolism', OLD.energy_metabolism_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'energy_metabolism_id', OLD.energy_metabolism_id,
      'user_id', OLD.user_id,
      'energy_date', OLD.energy_date,
      'energy_time', OLD.energy_time,
      'glucose', OLD.glucose,
      'ketone', OLD.ketone,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_energy_metabolism_delete_logical` AFTER UPDATE ON `energy_metabolism` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'energy_metabolism', OLD.energy_metabolism_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'energy_metabolism_id', OLD.energy_metabolism_id,
        'user_id', OLD.user_id,
        'energy_date', OLD.energy_date,
        'energy_time', OLD.energy_time,
        'glucose', OLD.glucose,
        'ketone', OLD.ketone,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_energy_metabolism_insert` AFTER INSERT ON `energy_metabolism` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'energy_metabolism', NEW.energy_metabolism_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'energy_metabolism_id', NEW.energy_metabolism_id,
      'user_id', NEW.user_id,
      'energy_date', NEW.energy_date,
      'energy_time', NEW.energy_time,
      'glucose', NEW.glucose,
      'ketone', NEW.ketone,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_energy_metabolism_update` AFTER UPDATE ON `energy_metabolism` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)         DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)     DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)      DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}');
  END IF;
  IF OLD.energy_date <> NEW.energy_date THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"energy_date":{"old":"', escape_json(OLD.energy_date), '","new":"', escape_json(NEW.energy_date), '"}');
  END IF;
  IF OLD.energy_time <> NEW.energy_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"energy_time":{"old":"', escape_json(OLD.energy_time), '","new":"', escape_json(NEW.energy_time), '"}');
  END IF;
  IF OLD.glucose <> NEW.glucose THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"glucose":{"old":"', escape_json(OLD.glucose), '","new":"', escape_json(NEW.glucose), '"}');
  END IF;
  IF OLD.ketone <> NEW.ketone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"ketone":{"old":"', escape_json(OLD.ketone), '","new":"', escape_json(NEW.ketone), '"}');
  END IF;

  -- (opcional) registrar updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'energy_metabolism', OLD.energy_metabolism_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lipid_profile_record`
--

CREATE TABLE `lipid_profile_record` (
  `lipid_profile_record_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `lipid_profile_date` date DEFAULT NULL,
  `lipid_profile_time` time DEFAULT NULL,
  `ldl` decimal(5,2) DEFAULT NULL,
  `hdl` decimal(5,2) DEFAULT NULL,
  `total_cholesterol` decimal(5,2) DEFAULT NULL,
  `triglycerides` decimal(5,2) DEFAULT NULL,
  `non_hdl` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lipid_profile_record`
--

INSERT INTO `lipid_profile_record` (`lipid_profile_record_id`, `user_id`, `lipid_profile_date`, `lipid_profile_time`, `ldl`, `hdl`, `total_cholesterol`, `triglycerides`, `non_hdl`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
('89199e2a-1b2a-4fdc-9309-297b66bf70cc', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', '2025-09-12', '18:49:21', 120.00, 100.00, 230.00, 50.00, 130.00, '2025-09-12 15:49:21', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', NULL, NULL, NULL, NULL);

--
-- Disparadores `lipid_profile_record`
--
DELIMITER $$
CREATE TRIGGER `trg_lipid_profile_record_delete` BEFORE DELETE ON `lipid_profile_record` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'lipid_profile_record', OLD.lipid_profile_record_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'lipid_profile_record_id', OLD.lipid_profile_record_id,
      'user_id', OLD.user_id,
      'lipid_profile_date', OLD.lipid_profile_date,
      'lipid_profile_time', OLD.lipid_profile_time,
      'ldl', OLD.ldl,
      'hdl', OLD.hdl,
      'total_cholesterol', OLD.total_cholesterol,
      'triglycerides', OLD.triglycerides,
      'non_hdl', OLD.non_hdl,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lipid_profile_record_delete_logical` AFTER UPDATE ON `lipid_profile_record` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'lipid_profile_record', OLD.lipid_profile_record_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'lipid_profile_record_id', OLD.lipid_profile_record_id,
        'user_id', OLD.user_id,
        'lipid_profile_date', OLD.lipid_profile_date,
        'lipid_profile_time', OLD.lipid_profile_time,
        'ldl', OLD.ldl,
        'hdl', OLD.hdl,
        'total_cholesterol', OLD.total_cholesterol,
        'triglycerides', OLD.triglycerides,
        'non_hdl', OLD.non_hdl,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lipid_profile_record_insert` AFTER INSERT ON `lipid_profile_record` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'lipid_profile_record', NEW.lipid_profile_record_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'lipid_profile_record_id', NEW.lipid_profile_record_id,
      'user_id', NEW.user_id,
      'lipid_profile_date', NEW.lipid_profile_date,
      'lipid_profile_time', NEW.lipid_profile_time,
      'ldl', NEW.ldl,
      'hdl', NEW.hdl,
      'total_cholesterol', NEW.total_cholesterol,
      'triglycerides', NEW.triglycerides,
      'non_hdl', NEW.non_hdl,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_lipid_profile_record_update` AFTER UPDATE ON `lipid_profile_record` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}');
  END IF;
  IF OLD.lipid_profile_date <> NEW.lipid_profile_date THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"lipid_profile_date":{"old":"', escape_json(OLD.lipid_profile_date), '","new":"', escape_json(NEW.lipid_profile_date), '"}');
  END IF;
  IF OLD.lipid_profile_time <> NEW.lipid_profile_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"lipid_profile_time":{"old":"', escape_json(OLD.lipid_profile_time), '","new":"', escape_json(NEW.lipid_profile_time), '"}');
  END IF;
  IF OLD.ldl <> NEW.ldl THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"ldl":{"old":"', escape_json(OLD.ldl), '","new":"', escape_json(NEW.ldl), '"}');
  END IF;
  IF OLD.hdl <> NEW.hdl THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"hdl":{"old":"', escape_json(OLD.hdl), '","new":"', escape_json(NEW.hdl), '"}');
  END IF;
  IF OLD.total_cholesterol <> NEW.total_cholesterol THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"total_cholesterol":{"old":"', escape_json(OLD.total_cholesterol), '","new":"', escape_json(NEW.total_cholesterol), '"}');
  END IF;
  IF OLD.triglycerides <> NEW.triglycerides THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"triglycerides":{"old":"', escape_json(OLD.triglycerides), '","new":"', escape_json(NEW.triglycerides), '"}');
  END IF;
  IF OLD.non_hdl <> NEW.non_hdl THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"non_hdl":{"old":"', escape_json(OLD.non_hdl), '","new":"', escape_json(NEW.non_hdl), '"}');
  END IF;

  -- (opcional) registrar updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'lipid_profile_record', OLD.lipid_profile_record_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` char(36) NOT NULL,
  `id_panel` char(36) NOT NULL,
  `id_record` char(36) NOT NULL,
  `id_biomarker` char(36) NOT NULL,
  `status` varchar(255) NOT NULL,
  `no_alert_user` int(255) NOT NULL,
  `no_alert_admin` int(255) NOT NULL,
  `user_id` char(36) NOT NULL,
  `new` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`notification_id`, `id_panel`, `id_record`, `id_biomarker`, `status`, `no_alert_user`, `no_alert_admin`, `user_id`, `new`) VALUES
('19f6a765-0b95-4eb7-bcea-19a1cc1e6326', 'e6861593-7327-4f63-9511-11d56f5398dc', '89199e2a-1b2a-4fdc-9309-297b66bf70cc', '38fb7644-7aed-44f5-975d-9c7ab84e03ef', 'High', 0, 0, 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 1),
('3aaaf382-d4b1-4b0d-879e-56667e805eee', '60819af9-0533-472c-9d5a-24a5df5a83f7', '0c5da657-839d-4c1f-97d8-75aae271097a', '1f1cc5a8-1fc5-4d65-ab35-db0f1f51b868', 'High', 0, 0, '2ea94ca9-90b0-40b4-a119-a1dd60154828', 1),
('6393655f-e750-43de-9ba4-f17beee38630', 'e6861593-7327-4f63-9511-11d56f5398dc', '89199e2a-1b2a-4fdc-9309-297b66bf70cc', 'f3328cee-dc7a-45aa-b513-721389258a5b', 'High', 0, 0, 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 1),
('77fc7e47-30d5-431f-96e3-842380eaa1e4', '60819af9-0533-472c-9d5a-24a5df5a83f7', '0c5da657-839d-4c1f-97d8-75aae271097a', '93a884c5-2b2e-4dbe-9a6f-cd685a042a9c', 'High', 1, 0, '2ea94ca9-90b0-40b4-a119-a1dd60154828', 1),
('8c40298a-5411-4fdd-a375-89edad0cdf4d', '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6', '701f7857-b578-49af-9e21-f524f541bcb7', '21a06607-d4f9-47f5-8ba2-fcc249768f96', 'High', 0, 0, '2ea94ca9-90b0-40b4-a119-a1dd60154828', 1),
('bb9e8438-0b14-479e-ad65-02a0e8355c6c', '60819af9-0533-472c-9d5a-24a5df5a83f7', '0c5da657-839d-4c1f-97d8-75aae271097a', '3d345bbf-7cc1-4560-8ea4-a453ce9f3282', 'Low', 1, 0, '2ea94ca9-90b0-40b4-a119-a1dd60154828', 1),
('de4e8f4a-c6f4-4804-9cd6-32f05f80dcb0', '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6', '701f7857-b578-49af-9e21-f524f541bcb7', '60f2da27-1c22-4443-8247-b9f496d91ead', 'Low', 1, 0, '2ea94ca9-90b0-40b4-a119-a1dd60154828', 1),
('e0dcc6ac-537d-4713-8c5d-22e3c83dcfe0', 'e6861593-7327-4f63-9511-11d56f5398dc', '89199e2a-1b2a-4fdc-9309-297b66bf70cc', '41397af2-9910-4265-974a-15c8def4d28b', 'High', 0, 0, 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `password_reset_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`password_reset_id`, `email`, `token`, `created_at`) VALUES
(5, 'moisescelis21@gmail.com', '940cdc6892e7ecf9b8c951aadc8df1942c45ebdcf89f0a8a367be21ca8b70539', '2025-09-12 22:31:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renal_function`
--

CREATE TABLE `renal_function` (
  `renal_function_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `renal_date` date NOT NULL,
  `renal_time` time NOT NULL,
  `albumin` decimal(5,2) DEFAULT NULL,
  `creatinine` decimal(5,2) DEFAULT NULL,
  `acr` decimal(6,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `renal_function`
--

INSERT INTO `renal_function` (`renal_function_id`, `user_id`, `renal_date`, `renal_time`, `albumin`, `creatinine`, `acr`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('0c5da657-839d-4c1f-97d8-75aae271097a', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', '2025-09-07', '13:54:11', 999.99, 2.00, 9999.99, '2025-09-15 10:51:18', '2ea94ca9-90b0-40b4-a119-a1dd60154828', '2025-09-17 16:30:58', NULL, NULL, NULL);

--
-- Disparadores `renal_function`
--
DELIMITER $$
CREATE TRIGGER `trg_renal_function_delete` BEFORE DELETE ON `renal_function` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'renal_function', OLD.renal_function_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'renal_function_id', OLD.renal_function_id,
      'user_id', OLD.user_id,
      'renal_date', OLD.renal_date,
      'renal_time', OLD.renal_time,
      'albumin', OLD.albumin,
      'creatinine', OLD.creatinine,
      'acr', OLD.acr,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_renal_function_delete_logical` AFTER UPDATE ON `renal_function` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'renal_function', OLD.renal_function_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'renal_function_id', OLD.renal_function_id,
        'user_id', OLD.user_id,
        'renal_date', OLD.renal_date,
        'renal_time', OLD.renal_time,
        'albumin', OLD.albumin,
        'creatinine', OLD.creatinine,
        'acr', OLD.acr,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_renal_function_insert` AFTER INSERT ON `renal_function` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'renal_function', NEW.renal_function_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'renal_function_id', NEW.renal_function_id,
      'user_id', NEW.user_id,
      'renal_date', NEW.renal_date,
      'renal_time', NEW.renal_time,
      'albumin', NEW.albumin,
      'creatinine', NEW.creatinine,
      'acr', NEW.acr,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_renal_function_update` AFTER UPDATE ON `renal_function` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}');
  END IF;
  IF OLD.renal_date <> NEW.renal_date THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"renal_date":{"old":"', escape_json(OLD.renal_date), '","new":"', escape_json(NEW.renal_date), '"}');
  END IF;
  IF OLD.renal_time <> NEW.renal_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"renal_time":{"old":"', escape_json(OLD.renal_time), '","new":"', escape_json(NEW.renal_time), '"}');
  END IF;
  IF OLD.albumin <> NEW.albumin THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"albumin":{"old":"', escape_json(OLD.albumin), '","new":"', escape_json(NEW.albumin), '"}');
  END IF;
  IF OLD.creatinine <> NEW.creatinine THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"creatinine":{"old":"', escape_json(OLD.creatinine), '","new":"', escape_json(NEW.creatinine), '"}');
  END IF;
  IF OLD.acr <> NEW.acr THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"acr":{"old":"', escape_json(OLD.acr), '","new":"', escape_json(NEW.acr), '"}');
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'renal_function', OLD.renal_function_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `second_opinion_data`
--

CREATE TABLE `second_opinion_data` (
  `second_opinion_data_id` char(36) NOT NULL,
  `second_opinion_id` char(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `share_type` varchar(255) NOT NULL,
  `panel_id` longtext DEFAULT NULL,
  `biomarkers_id` longtext DEFAULT NULL,
  `records_id` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by` char(36) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` char(36) NOT NULL,
  `deleted_at` datetime NOT NULL,
  `deleted_by` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `second_opinion_requests`
--

CREATE TABLE `second_opinion_requests` (
  `second_opinion_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('PENDING','APPROVED','EXPIRED','DECLINED') DEFAULT 'PENDING',
  `notes` text DEFAULT NULL,
  `shared_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Disparadores `second_opinion_requests`
--
DELIMITER $$
CREATE TRIGGER `trg_second_opinion_requests_delete` BEFORE DELETE ON `second_opinion_requests` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'second_opinion_requests', OLD.second_opinion_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'second_opinion_id', OLD.second_opinion_id,
      'user_id', OLD.user_id,
      'specialist_id', OLD.specialist_id,
      'status', OLD.status,
      'notes', OLD.notes,
      'shared_until', OLD.shared_until,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_second_opinion_requests_delete_logical` AFTER UPDATE ON `second_opinion_requests` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'second_opinion_requests', OLD.second_opinion_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'second_opinion_id', OLD.second_opinion_id,
        'user_id', OLD.user_id,
        'specialist_id', OLD.specialist_id,
        'status', OLD.status,
        'notes', OLD.notes,
        'shared_until', OLD.shared_until,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_second_opinion_requests_insert` AFTER INSERT ON `second_opinion_requests` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'second_opinion_requests', NEW.second_opinion_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'second_opinion_id', NEW.second_opinion_id,
      'user_id', NEW.user_id,
      'specialist_id', NEW.specialist_id,
      'status', NEW.status,
      'notes', NEW.notes,
      'shared_until', NEW.shared_until,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_second_opinion_requests_update` AFTER UPDATE ON `second_opinion_requests` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}');
  END IF;
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}');
  END IF;
  IF OLD.status <> NEW.status THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"status":{"old":"', escape_json(OLD.status), '","new":"', escape_json(NEW.status), '"}');
  END IF;
  IF OLD.notes <> NEW.notes THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"notes":{"old":"', escape_json(OLD.notes), '","new":"', escape_json(NEW.notes), '"}');
  END IF;
  IF OLD.shared_until <> NEW.shared_until THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"shared_until":{"old":"', escape_json(OLD.shared_until), '","new":"', escape_json(NEW.shared_until), '"}');
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'second_opinion_requests', OLD.second_opinion_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_questions`
--

CREATE TABLE `security_questions` (
  `security_question_id` char(36) NOT NULL,
  `user_id_user` char(36) DEFAULT NULL,
  `user_id_admin` char(36) DEFAULT NULL,
  `user_id_specialist` char(36) DEFAULT NULL,
  `user_type` enum('User','Administrator','Specialist') DEFAULT NULL,
  `question1` varchar(255) NOT NULL,
  `answer1` varchar(255) NOT NULL,
  `question2` varchar(255) NOT NULL,
  `answer2` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `security_questions`
--

INSERT INTO `security_questions` (`security_question_id`, `user_id_user`, `user_id_admin`, `user_id_specialist`, `user_type`, `question1`, `answer1`, `question2`, `answer2`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('cb02618c-d46f-49a5-b879-15d2f70fd6db', NULL, NULL, 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'Specialist', 'Color favorito', 'Azul', 'Numero Favorito', '07', '2025-07-17 06:54:37', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL);

--
-- Disparadores `security_questions`
--
DELIMITER $$
CREATE TRIGGER `trg_security_questions_delete` BEFORE DELETE ON `security_questions` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'security_questions', OLD.security_question_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'security_question_id', OLD.security_question_id,
      'user_id_user', OLD.user_id_user,
      'user_id_admin', OLD.user_id_admin,
      'user_id_specialist', OLD.user_id_specialist,
      'user_type', OLD.user_type,
      'question1', OLD.question1,
      'answer1', OLD.answer1,
      'question2', OLD.question2,
      'answer2', OLD.answer2,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_security_questions_delete_logical` AFTER UPDATE ON `security_questions` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'security_questions', OLD.security_question_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'security_question_id', OLD.security_question_id,
        'user_id_user', OLD.user_id_user,
        'user_id_admin', OLD.user_id_admin,
        'user_id_specialist', OLD.user_id_specialist,
        'user_type', OLD.user_type,
        'question1', OLD.question1,
        'answer1', OLD.answer1,
        'question2', OLD.question2,
        'answer2', OLD.answer2,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_security_questions_insert` AFTER INSERT ON `security_questions` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'security_questions', NEW.security_question_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'security_question_id', NEW.security_question_id,
      'user_id_user', NEW.user_id_user,
      'user_id_admin', NEW.user_id_admin,
      'user_id_specialist', NEW.user_id_specialist,
      'user_type', NEW.user_type,
      'question1', NEW.question1,
      'answer1', NEW.answer1,
      'question2', NEW.question2,
      'answer2', NEW.answer2,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_security_questions_update` AFTER UPDATE ON `security_questions` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en los campos sensibles/posible texto)
  IF OLD.user_id_user <> NEW.user_id_user THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id_user":{"old":"', escape_json(OLD.user_id_user), '","new":"', escape_json(NEW.user_id_user), '"}');
  END IF;
  IF OLD.user_id_admin <> NEW.user_id_admin THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id_admin":{"old":"', escape_json(OLD.user_id_admin), '","new":"', escape_json(NEW.user_id_admin), '"}');
  END IF;
  IF OLD.user_id_specialist <> NEW.user_id_specialist THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id_specialist":{"old":"', escape_json(OLD.user_id_specialist), '","new":"', escape_json(NEW.user_id_specialist), '"}');
  END IF;
  IF OLD.user_type <> NEW.user_type THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"user_type":{"old":"', escape_json(OLD.user_type), '","new":"', escape_json(NEW.user_type), '"}');
  END IF;
  IF OLD.question1 <> NEW.question1 THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"question1":{"old":"', escape_json(OLD.question1), '","new":"', escape_json(NEW.question1), '"}');
  END IF;
  IF OLD.answer1 <> NEW.answer1 THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"answer1":{"old":"', escape_json(OLD.answer1), '","new":"', escape_json(NEW.answer1), '"}');
  END IF;
  IF OLD.question2 <> NEW.question2 THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"question2":{"old":"', escape_json(OLD.question2), '","new":"', escape_json(NEW.question2), '"}');
  END IF;
  IF OLD.answer2 <> NEW.answer2 THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"answer2":{"old":"', escape_json(OLD.answer2), '","new":"', escape_json(NEW.answer2), '"}');
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'security_questions', OLD.security_question_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session_config`
--

CREATE TABLE `session_config` (
  `config_id` int(11) NOT NULL,
  `timeout_minutes` int(11) NOT NULL DEFAULT 30,
  `allow_ip_change` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `session_config`
--

INSERT INTO `session_config` (`config_id`, `timeout_minutes`, `allow_ip_change`, `updated_at`, `updated_by`) VALUES
(1, 15, 0, '2025-07-16 09:13:28', '3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session_management`
--

CREATE TABLE `session_management` (
  `session_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_type` enum('admin','specialist','user') NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL,
  `inactivity_duration` varchar(255) DEFAULT NULL,
  `login_success` tinyint(1) NOT NULL DEFAULT 1,
  `failure_reason` varchar(255) DEFAULT NULL,
  `session_status` enum('active','closed','expired','failed','kicked') NOT NULL DEFAULT 'active',
  `ip_address` varchar(45) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `coordinates` varchar(50) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `device_type` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `session_management`
--

INSERT INTO `session_management` (`session_id`, `user_id`, `user_name`, `user_type`, `full_name`, `login_time`, `logout_time`, `inactivity_duration`, `login_success`, `failure_reason`, `session_status`, `ip_address`, `city`, `region`, `country`, `zipcode`, `coordinates`, `hostname`, `os`, `browser`, `user_agent`, `device_id`, `device_type`, `created_at`) VALUES
('05af2436-6fa2-4078-a725-a4a33edbe9c6', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-25 20:08:15', '2025-07-25 20:24:08', '930', 1, NULL, 'expired', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-07-25 20:08:15'),
('070d7c49-dcc0-4f8e-bb81-ce05b85dcaf0', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-03 17:59:45', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.226', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.226', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-03 17:59:45'),
('0973a561-4f11-45a8-a6fc-07d8f1eb8455', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 16:44:04', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:44:04'),
('0a7b2fc3-4d9f-474d-a491-c475ecfd3d5c', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 16:47:16', NULL, NULL, 1, NULL, 'active', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:47:16'),
('0b83ad9b-8613-44e0-b978-3042418e05cb', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-25 17:43:30', NULL, NULL, 0, 'invalid_password', 'failed', '89.41.26.56', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.56', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:43:30'),
('0b93d51a-252d-4ebb-aaaf-f61b359efb47', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 17:57:11', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:57:11'),
('0cda20bd-edd7-453e-b70f-399a12968e20', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-18 18:51:49', NULL, NULL, 0, 'invalid_password', 'failed', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-92VMM39', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'eab4108b-3c42-4c2d-adb5-7bb669b9d23e', 0, '2025-09-18 18:51:49'),
('0e9831be-3f98-4c19-90d2-614fd3dabb47', NULL, 'UNKNOWN', 'admin', 'UNKNOWN', '2025-09-12 18:13:22', NULL, NULL, 0, 'user_not_found', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:13:22'),
('111b696c-8396-408e-afa7-d472b8a120ba', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-24 17:28:54', NULL, NULL, 1, NULL, 'active', '104.223.111.215', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.215', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-08-24 17:28:54'),
('15b7e821-949b-4158-8b37-049ecb2bfd98', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-15 12:53:47', '2025-09-15 12:54:30', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 1, '2025-09-15 12:53:47'),
('15fd3469-673b-40c4-8d91-7c21225690bc', '6519d1be-db8c-4270-8177-f9b9f3f5a461', 'jesusnbz23@gmail.com', 'specialist', 'Jesús del Barrio', '2025-09-15 13:13:57', NULL, NULL, 1, NULL, 'active', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 13:13:57'),
('15fdf73f-25ae-450e-a3e7-5faa4a6f6ebd', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-19 11:36:54', NULL, NULL, 1, NULL, 'active', '200.8.108.199', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.199', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-19 11:36:54'),
('179b7199-04f4-4fae-9fbd-f27e78c0f3ea', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:07:33', '2025-09-12 18:13:16', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:07:33'),
('18303358-0527-4a3d-bacd-53af0c1c63c3', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 12:49:20', NULL, NULL, 0, 'user_not_found', 'failed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 1, '2025-09-15 12:49:20'),
('1f058dd3-1a69-4c35-b968-2d391b745185', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 17:37:06', NULL, NULL, 1, NULL, 'active', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 17:37:06'),
('1f76ae29-389d-4235-9792-6684c4466079', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-11 07:17:23', NULL, NULL, 0, 'invalid_password', 'failed', '185.236.200.27', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '185.236.200.27', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-08-11 07:17:23'),
('23ce0dc0-9053-470e-a45a-0dd7328bca5e', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-10 15:55:15', '2025-09-10 16:12:36', '905', 1, NULL, 'expired', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'b909b5eb-fa92-4375-ad72-ceaf86f4071d', 0, '2025-09-10 15:55:15'),
('2618ad0e-8008-47a4-99ff-c82e9ffb207e', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 20:07:47', NULL, NULL, 0, 'invalid_password', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-07-25 20:07:47'),
('277e8527-d1be-4662-8234-743f4697e9fd', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesusmadafaka13@gmail.com', 'specialist', 'jesus Specialist', '2025-09-15 13:11:11', NULL, NULL, 0, 'invalid_password', 'failed', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 13:11:11'),
('27d5b391-9e6e-4a14-882f-ece80e16a393', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'jesusnbz22@gmail.com', 'user', 'Jesus Zapatin', '2025-09-15 13:47:43', NULL, NULL, 1, NULL, 'active', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 13:47:43'),
('29cbd626-4c57-4e79-a297-24238de44baa', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'jesusmadafaka13@gmail.com', 'user', 'Jesus Zapatin', '2025-09-11 20:39:46', '2025-09-11 20:41:28', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-11 20:39:46'),
('2a9cd341-b739-4961-8b06-2b48a75454be', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-24 17:28:50', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.215', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.215', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-08-24 17:28:50'),
('2c7ee1bf-0728-45d0-9aff-2925b75ed4d1', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 16:47:06', NULL, NULL, 0, 'invalid_password', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:47:06'),
('2dc10713-2991-40ad-952d-4a4b5cc447f8', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'jesusnbz22@gmail.com', 'admin', 'Jesús del Barrio', '2025-09-15 12:28:32', '2025-09-15 16:43:31', NULL, 1, NULL, 'closed', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 12:28:32'),
('3143b56d-8faf-4204-85df-f6f408bde951', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-08-24 17:28:29', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.215', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.215', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-08-24 17:28:29'),
('33cd6313-7b28-4589-970d-1fb5fa581a36', NULL, 'UNKNOWN', 'specialist', 'UNKNOWN', '2025-09-15 15:09:59', NULL, NULL, 0, 'user_not_found', 'failed', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 15:09:59'),
('35409cde-52a3-4ad6-a6a5-f796619b7ce5', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-21 14:43:32', '2025-07-21 15:05:54', '948', 1, NULL, 'expired', '23.94.49.154', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '23-94-49-154-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-21 14:43:32'),
('389efb76-6d85-4148-9e5f-81d5a0710ab5', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 16:47:06', NULL, NULL, 0, 'invalid_password', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:47:06'),
('3e407fd3-5338-40a6-8bcc-4f03ed029a62', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 17:40:34', '2025-09-12 18:05:40', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 17:40:34'),
('3ea8c68d-a7b2-4ffa-b0a8-e14fcf0b36d3', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-10 18:56:24', '2025-09-10 19:13:46', '909', 1, NULL, 'expired', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-10 18:56:24'),
('3f2dfbd6-df8f-4807-921b-7fac28a2f4dc', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-12 18:35:15', NULL, NULL, 1, NULL, 'active', '149.102.226.104', 'New York', 'New York', 'United States', '10118', '40.7126,-74.0066', 'unn-149-102-226-104.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-12 18:35:15'),
('4001808d-42eb-4936-8260-ac072bbf82d4', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 12:49:01', NULL, NULL, 0, 'user_not_found', 'failed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:49:01'),
('40d37c42-6b56-419f-ae2d-ef0240560df3', '1ec9501f-047f-469c-af5d-a71ce4a121bb', 'jesusnbz22@gmail.com', 'admin', 'Jesús del Barrio', '2025-09-12 18:15:21', NULL, NULL, 1, NULL, 'active', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:15:21'),
('42163a68-a03f-46e3-812a-eb3d49665551', NULL, 'UNKNOWN', 'specialist', 'UNKNOWN', '2025-09-15 15:09:56', NULL, NULL, 0, 'user_not_found', 'failed', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 15:09:56'),
('424f9849-d152-4355-ba01-270e323d0542', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 13:54:48', '2025-07-17 14:23:59', '938', 1, NULL, 'expired', '::1', 'Unknown', 'Unknown', 'Unknown', NULL, '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 13:54:48'),
('4a8a1d20-1c58-4195-9efc-a5c6e3626b8a', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 17:56:54', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:56:54'),
('4c8f4fa1-57a9-4741-b2f4-c1600f5809fb', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-29 23:20:44', '2025-08-29 23:20:57', NULL, 1, NULL, 'closed', '198.46.249.8', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '198-46-249-8-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-08-29 23:20:44'),
('4cd92d92-e551-4ae1-bc53-c01e349c9ddf', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-03 17:59:50', '2025-09-03 18:15:43', '925', 1, NULL, 'expired', '104.223.111.226', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.226', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-03 17:59:50'),
('5336f66e-6c64-4e8d-9680-ec955d71b4ae', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-16 18:52:18', NULL, NULL, 1, NULL, 'active', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-16 18:52:18'),
('5392083a-ad9f-48a1-b87a-94b5d8906dd8', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 16:41:22', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'd43ddfaf-345c-46d4-865c-fd8af9bbfccc', 0, '2025-09-15 16:41:22'),
('55b3aeb9-e8a3-4836-b022-51993624638e', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-29 23:20:41', NULL, NULL, 0, 'invalid_password', 'failed', '198.46.249.8', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '198-46-249-8-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-08-29 23:20:41'),
('56925d72-85d1-42c0-8771-b34ade2b1695', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-18 18:51:54', NULL, NULL, 1, NULL, 'active', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-92VMM39', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'eab4108b-3c42-4c2d-adb5-7bb669b9d23e', 0, '2025-09-18 18:51:54'),
('569f7e79-f239-4931-8531-96de834e96b2', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-23 15:18:42', '2025-07-23 15:42:27', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'e71d0396-d7c0-4a23-830d-a5380b17217e', 0, '2025-07-23 15:18:42'),
('573ae9b3-01b9-438f-8973-d330cff6cd9d', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-11 06:45:02', '2025-08-11 06:45:36', NULL, 1, NULL, 'closed', '185.236.200.27', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '185.236.200.27', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-08-11 06:45:02'),
('57a258f8-3f4a-4a63-85a3-e681505ef8a0', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-22 07:59:42', NULL, NULL, 1, NULL, 'active', '91.219.212.87', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '91.219.212.87', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-22 07:59:42'),
('588713a2-3aa3-415d-8dff-ad597c8700ad', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'jesusmadafaka13@gmail.com', 'user', 'Jesus Zapatin', '2025-09-11 18:29:53', NULL, NULL, 0, 'invalid_password', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-11 18:29:53'),
('58f7b384-a091-48b6-a3a4-3474a4c74d75', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-08-11 07:17:28', '2025-08-11 07:40:58', '930', 1, NULL, 'expired', '185.236.200.27', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '185.236.200.27', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-08-11 07:17:28'),
('5a96ed80-9b2f-40a2-b2ee-8cf42c65ed19', NULL, 'UNKNOWN', 'specialist', 'UNKNOWN', '2025-09-15 15:10:04', NULL, NULL, 0, 'user_not_found', 'failed', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 15:10:04'),
('5b9d1cc1-cd3d-4b20-b253-fd6c3a5a670f', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-07-17 13:42:56', '2025-07-17 13:54:40', NULL, 1, NULL, 'closed', '::1', 'Unknown', 'Unknown', 'Unknown', NULL, '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 13:42:56'),
('5fc93d35-8532-4fa0-a51e-18b5ae2a4e78', NULL, 'UNKNOWN', 'admin', 'UNKNOWN', '2025-09-12 18:15:17', NULL, NULL, 0, 'user_not_found', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:15:17'),
('61fb04f1-93a3-497c-95f0-b546e818d1f3', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 15:38:24', '2025-07-17 16:25:12', '929', 1, NULL, 'expired', '::1', 'Unknown', 'Unknown', 'Unknown', NULL, '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 15:38:24'),
('65fba044-29cf-446d-b2d8-7d1c6be41180', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 16:53:44', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:53:44'),
('68905783-bbd1-4d70-86fe-6eff3477eb39', 'd3aa1ffb-7dd6-4397-a1ae-38798890a585', 'marcel85rs@gmail.com', 'user', 'Alejandro Rojas', '2025-09-10 15:53:41', '2025-09-10 15:54:33', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'b909b5eb-fa92-4375-ad72-ceaf86f4071d', 0, '2025-09-10 15:53:41'),
('6df633e7-eb99-4acb-8a5d-4f0c46635aaa', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 12:57:44', NULL, NULL, 0, 'invalid_password', 'failed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 1, '2025-09-15 12:57:44'),
('6ed5f2a8-5908-476c-bec3-a6e20e517796', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-10 18:54:08', '2025-09-10 18:56:13', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-10 18:54:08'),
('74f6b38b-07b9-4f23-81b9-f81945e4b583', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-09-03 17:59:31', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.226', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.226', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-03 17:59:31'),
('753d44a7-5e8c-4843-9f46-fb5cb3b57be9', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 15:06:38', NULL, NULL, 1, NULL, 'active', '190.142.206.21', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '190.142.206.21', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-15 15:06:38'),
('766877fb-4e7e-478c-8e6c-7c69f0165388', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 20:07:49', '2025-07-25 20:08:08', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-07-25 20:07:49'),
('76f68970-63c4-43e7-bd76-7e23d6dd8805', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:27:34', '2025-09-12 18:29:39', NULL, 1, NULL, 'closed', '149.102.226.104', 'New York', 'New York', 'United States', '10118', '40.7126,-74.0066', 'unn-149-102-226-104.datapacket.com', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 1, '2025-09-12 18:27:34'),
('77d11e07-62e2-40c0-baaf-f7bd062ef004', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-22 13:28:12', '2025-07-22 13:55:47', '944', 1, NULL, 'expired', '23.94.49.142', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '23-94-49-142-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-22 13:28:12'),
('7c925a88-dad0-4d85-8f1b-70da19de09da', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-21 16:46:51', '2025-07-21 17:03:55', '917', 1, NULL, 'expired', '23.94.49.154', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '23-94-49-154-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-21 16:46:51'),
('7dee3c84-242c-410c-ac96-f518ddedeb0f', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-10 19:40:54', '2025-09-10 19:41:04', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-10 19:40:54'),
('7ed7d5c4-0ab9-4bf0-af86-09f3a046c438', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 17:58:09', NULL, NULL, 0, 'too_many_attempts', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:58:09'),
('80833848-14bf-4582-b3df-f94661782690', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-23 14:58:38', '2025-07-23 15:15:20', '901', 1, NULL, 'expired', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'e71d0396-d7c0-4a23-830d-a5380b17217e', 0, '2025-07-23 14:58:38'),
('81b6febc-857b-4024-bb9d-3add8f5f7687', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesusmadafaka13@gmail.com', 'specialist', 'jesus Specialist', '2025-09-15 20:07:44', NULL, NULL, 1, NULL, 'active', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 20:07:44'),
('824d537b-95f2-4971-9db4-52c1f9ef3eee', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 17:57:31', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:57:31'),
('83a4f17b-a4d6-4fed-8425-c7040a10ab5d', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-22 09:57:10', NULL, NULL, 1, NULL, 'active', '91.219.212.87', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '91.219.212.87', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-22 09:57:10'),
('83eaecf0-be1b-46be-838a-9f3834d6fd2d', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-16 18:52:15', NULL, NULL, 0, 'invalid_password', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-16 18:52:15'),
('84967616-6bcd-4fcd-8022-a5329e90aeed', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 16:41:29', '2025-09-15 16:43:57', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'd43ddfaf-345c-46d4-865c-fd8af9bbfccc', 0, '2025-09-15 16:41:29'),
('87ccdb90-067b-4a36-998d-6541f546a3e8', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 17:09:34', NULL, NULL, 0, 'invalid_password', 'failed', '89.41.26.61', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.61', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:09:34'),
('88025262-6265-44e0-b1ea-04ebd6c910f0', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'jesusnbz22@gmail.com', 'user', 'Jesus Zapatin', '2025-09-15 14:28:03', '2025-09-15 14:30:01', NULL, 1, NULL, 'closed', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 14:28:03'),
('8869e34c-30b5-42ca-a4e1-b72fe7719c08', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-28 22:19:37', '2025-07-28 22:38:08', '900', 1, NULL, 'expired', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '9cd0777b-32d3-48bb-95a0-f2e7ffcb6f90', 0, '2025-07-28 22:19:37'),
('88b87e3c-47e9-4d33-8ee4-e46bc61b3320', NULL, 'UNKNOWN', 'specialist', 'UNKNOWN', '2025-09-15 16:56:03', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:56:03'),
('89a424be-07ec-4b6d-b3f1-7517a0ba5a48', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 19:25:20', '2025-07-25 19:52:09', '919', 1, NULL, 'expired', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-07-25 19:25:20'),
('8d1476a8-2d57-4953-8ad7-24f314eec9d6', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 18:04:11', '2025-07-16 18:09:11', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Android', 'Mozilla Firefox', 'Mozilla/5.0 (Android 16; Mobile; rv:140.0) Gecko/140.0 Firefox/140.0', 'f600c11e-4483-455f-aa83-bf183c4344de', 1, '2025-07-16 18:04:11'),
('8d41572d-6f9f-4249-b82e-deaaaa03cac4', 'd08a28f7-95f4-468b-981c-9b988d7d5df9', 'fsafassfafa@gmail.com', 'admin', 'fsafsaf fsafafss', '2025-09-15 19:42:14', '2025-09-15 19:48:22', NULL, 1, NULL, 'closed', '190.142.206.21', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '190.142.206.21', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-15 19:42:14'),
('8f021a65-e467-4426-9773-2531cab66112', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesusmadafaka13@gmail.com', 'specialist', 'jesus Specialist', '2025-09-15 15:10:09', NULL, NULL, 0, 'invalid_password', 'failed', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 15:10:09'),
('92044530-b158-420d-98f3-e289a7892ac2', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:01:29', '2025-09-12 18:08:46', NULL, 1, NULL, 'closed', '149.102.226.104', 'New York', 'New York', 'United States', '10118', '40.7126,-74.0066', 'unn-149-102-226-104.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-12 18:01:29'),
('93ec9e24-57a7-40b8-b7be-66c898618fc5', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 12:57:50', '2025-09-15 12:58:35', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 1, '2025-09-15 12:57:50'),
('9410f9c5-8ac2-44f7-88c3-0d6c8b64d8fc', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-09-11 20:39:39', NULL, NULL, 0, 'user_blocked', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-11 20:39:39'),
('96bf1b3e-e630-4856-9297-b1b0fe5caf55', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 12:41:48', '2025-09-15 12:44:34', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:41:48'),
('97706a9e-8337-4bde-96d6-daf5a64f3e47', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-25 17:43:34', NULL, NULL, 1, NULL, 'active', '89.41.26.56', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.56', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:43:34'),
('99c378b5-59fb-4a19-a8fd-054184ddb2a7', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-19 15:34:28', '2025-07-19 11:36:39', NULL, 1, NULL, 'closed', '198.46.249.59', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '198-46-249-59-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-19 15:34:28'),
('9dfc5851-6001-4ae0-bd77-2f8f0c143c04', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 17:09:39', '2025-07-25 17:09:48', NULL, 1, NULL, 'closed', '89.41.26.61', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.61', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:09:39'),
('9ec1b9bd-fe20-47ea-a888-2990edeada38', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-20 22:55:02', NULL, NULL, 1, NULL, 'active', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Android', 'Mozilla Firefox', 'Mozilla/5.0 (Android 16; Mobile; rv:140.0) Gecko/140.0 Firefox/140.0', 'be7bea48-b41a-4feb-872b-17b24285c998', 1, '2025-07-20 22:55:02'),
('9ffcd20b-edc3-494b-9085-ab384e7e24ca', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-16 17:58:32', NULL, NULL, 0, 'user_blocked', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:58:32'),
('a1199bdd-eee9-4418-93b3-3b5dc8ca4d72', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-25 17:09:29', NULL, NULL, 0, 'invalid_password', 'failed', '89.41.26.61', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.61', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:09:29'),
('a3c07970-2e5c-4c25-ae9c-7aa70a27f6ea', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:13:26', '2025-09-12 18:14:56', NULL, 1, NULL, 'closed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:13:26'),
('a66def03-de98-41c8-a239-528d3562d6d1', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 16:46:46', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:46:46'),
('a6a8ed45-b3dd-4697-ba2f-fbbd4def7c32', 'd3aa1ffb-7dd6-4397-a1ae-38798890a585', 'marcel85rs@gmail.com', 'user', 'Alejandro Rojas', '2025-09-03 18:42:40', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'eef4382f-d108-4790-a750-aefe825aa44c', 0, '2025-09-03 18:42:40'),
('a8275ac8-21d2-42ea-90af-0f24d0717f78', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 16:43:56', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:43:56'),
('a83bdd20-9acf-42c5-9a8d-96f0a2934e64', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-21 18:36:48', NULL, NULL, 1, NULL, 'active', '96.31.87.167', 'Tampa', 'Florida', 'United States', '33614', '28.0109,-82.4948', '96-31-87-167.static.hvvc.us', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-21 18:36:48'),
('ae06445d-b475-49a4-a706-739a52f11246', '34e023d4-5339-41bc-a6ee-ed8cfcbebb77', 'jesuszapata@gmail.com', 'user', 'Jesús Zapata', '2025-09-04 19:34:04', NULL, NULL, 1, NULL, 'active', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-04 19:34:04'),
('b0b1cd36-7035-414c-b265-9ff636eee4dc', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-15 12:58:47', NULL, NULL, 1, NULL, 'active', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:58:47'),
('b2741291-51eb-42ea-864f-350c5f5dc205', NULL, 'UNKNOWN', 'specialist', 'UNKNOWN', '2025-09-10 18:54:03', NULL, NULL, 0, 'user_not_found', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-10 18:54:03'),
('b7a115f4-7ea9-4e4f-a337-be8396dc9f3a', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesusmadafaka13@gmail.com', 'specialist', 'jesus Specialist', '2025-09-15 15:10:13', NULL, NULL, 1, NULL, 'active', '186.167.70.34', 'Puerto Cruz', 'Anzoátegui', 'Venezuela', 'Unknown', '10.2118,-64.631', '186.167.70.34', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 15:10:13');
INSERT INTO `session_management` (`session_id`, `user_id`, `user_name`, `user_type`, `full_name`, `login_time`, `logout_time`, `inactivity_duration`, `login_success`, `failure_reason`, `session_status`, `ip_address`, `city`, `region`, `country`, `zipcode`, `coordinates`, `hostname`, `os`, `browser`, `user_agent`, `device_id`, `device_type`, `created_at`) VALUES
('bc0f05e7-afe4-4028-aca9-332ab36b2a4f', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-09-15 16:51:16', NULL, NULL, 0, 'user_not_found', 'failed', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:51:16'),
('bebc31fc-5e58-467d-af8e-abecc7aa6297', 'd3aa1ffb-7dd6-4397-a1ae-38798890a585', 'marcel85rs@gmail.com', 'user', 'Alejandro Rojas', '2025-09-03 18:42:34', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'eef4382f-d108-4790-a750-aefe825aa44c', 0, '2025-09-03 18:42:34'),
('bf571684-2e83-47bb-a5c9-6febfa9d6afd', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-18 22:32:50', NULL, NULL, 1, NULL, 'active', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-92VMM39', 'Linux', 'Google Chrome', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', 'eab4108b-3c42-4c2d-adb5-7bb669b9d23e', 1, '2025-09-18 22:32:50'),
('bf694cc9-0053-4fc2-b3d5-6e48d26e1e37', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-15 12:48:15', '2025-09-15 12:48:47', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:48:15'),
('bf6dd79f-6c65-406e-9313-1ad9d295dea0', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-15 16:44:22', '2025-09-15 16:46:11', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'd43ddfaf-345c-46d4-865c-fd8af9bbfccc', 0, '2025-09-15 16:44:22'),
('c0edf465-bef2-48b2-a34b-5ede907f25d3', 'd3aa1ffb-7dd6-4397-a1ae-38798890a585', 'marcel85rs@gmail.com', 'user', 'Alejandro Rojas', '2025-09-03 18:45:06', '2025-09-03 19:03:08', '910', 1, NULL, 'expired', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'eef4382f-d108-4790-a750-aefe825aa44c', 0, '2025-09-03 18:45:06'),
('c1d7997f-93d0-498c-a83c-15b359d19e26', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 16:48:04', '2025-09-15 16:56:40', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'd43ddfaf-345c-46d4-865c-fd8af9bbfccc', 0, '2025-09-15 16:48:04'),
('c53fddd5-5af4-4505-82d0-544d7fe62344', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-21 17:16:35', '2025-07-21 17:33:54', '927', 1, NULL, 'expired', '23.94.49.154', 'Marietta', 'Georgia', 'United States', '30006', '33.9521,-84.5475', '23-94-49-154-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-07-21 17:16:35'),
('c7b87535-e236-4aab-b6c6-2ced1eabf9b7', '2ea94ca9-90b0-40b4-a119-a1dd60154828', 'jesusmadafaka13@gmail.com', 'user', 'Jesus Zapatin', '2025-09-11 18:29:58', '2025-09-11 18:48:28', '908', 1, NULL, 'expired', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-11 18:29:58'),
('c8f57357-df27-4ae9-9334-850f8ebcdb26', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-28 23:20:14', '2025-07-29 00:19:18', '904', 1, NULL, 'expired', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '7a78218c-4020-47e8-bc41-e119bfe93649', 0, '2025-07-28 23:20:14'),
('cc4bf7ff-c75a-4347-935a-a02e97cdbd65', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-15 12:37:57', '2025-09-15 12:40:37', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:37:57'),
('ccbef635-f49b-46f0-98d4-24494332bdaf', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 16:56:07', NULL, NULL, 1, NULL, 'active', '149.50.211.135', 'Singapore', 'Unknown', 'Singapore', '60', '1.3254,103.7433', 'unn-149-50-211-135.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 16:56:07'),
('cf9f8e2b-fb2d-4562-90f0-ecea33f86ddf', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-23 15:58:17', NULL, NULL, 1, NULL, 'active', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'e71d0396-d7c0-4a23-830d-a5380b17217e', 0, '2025-07-23 15:58:17'),
('d0e257aa-c290-4257-b898-11615baf2671', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-09-03 17:59:25', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.226', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.226', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-03 17:59:25'),
('d16f64f5-7670-437b-91f1-8f42c73c7569', 'c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesusmadafaka13@gmail.com', 'specialist', 'jesus Specialist', '2025-09-15 20:07:40', NULL, NULL, 0, 'invalid_password', 'failed', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 20:07:40'),
('d2cf1628-e540-4ed7-98c8-e1e5607ef498', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-16 17:27:24', '2025-07-16 18:05:33', NULL, 1, NULL, 'closed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '6784e2be-f421-4311-afe5-ec4b487a560d', 0, '2025-07-16 17:27:24'),
('d36816e9-9683-4e75-b263-750a11c96e60', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:05:50', NULL, NULL, 0, 'invalid_password', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:05:50'),
('da843a68-e4cc-4b09-a081-1a347fd44f39', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-12 18:49:32', NULL, NULL, 1, NULL, 'active', '149.102.226.104', 'New York', 'New York', 'United States', '10118', '40.7126,-74.0066', 'unn-149-102-226-104.datapacket.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-12 18:49:32'),
('dc4183d7-fd60-403a-8e2d-3d271b1095e0', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moisescelis21@gmail.com', 'specialist', 'moises celiss', '2025-09-15 19:48:39', NULL, NULL, 1, NULL, 'active', '190.142.206.21', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '190.142.206.21', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-15 19:48:39'),
('dcb7b78c-ef5f-4cd7-ae7a-fd14657aad2d', NULL, 'UNKNOWN', 'user', 'UNKNOWN', '2025-07-16 17:56:25', NULL, NULL, 0, 'user_not_found', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', NULL, '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '09219ecb-7999-4e0b-b931-37b2a753b7a1', 0, '2025-07-16 17:56:25'),
('dd2f8eb1-f235-4979-bf32-3fcc750ece1f', NULL, 'UNKNOWN', 'admin', 'UNKNOWN', '2025-09-10 15:54:58', NULL, NULL, 0, 'user_not_found', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'b909b5eb-fa92-4375-ad72-ceaf86f4071d', 0, '2025-09-10 15:54:58'),
('e24ca08f-dab5-493e-bf31-02776e33b87e', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-09-10 19:40:34', NULL, NULL, 0, 'user_blocked', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '75d870ce-9eaf-49b3-a3e1-9b08ba7c95c0', 0, '2025-09-10 19:40:34'),
('e2639f3b-28f9-4d79-9497-1324de5dd5eb', NULL, 'UNKNOWN', 'admin', 'UNKNOWN', '2025-09-11 10:10:46', NULL, NULL, 0, 'user_not_found', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', 'c5930752-43d5-4703-8582-296ba267e687', 0, '2025-09-11 10:10:46'),
('e2f26827-a943-461f-a52f-b0f2d2e98e74', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 13:13:45', '2025-07-17 13:42:48', NULL, 1, NULL, 'closed', '::1', 'Unknown', 'Unknown', 'Unknown', NULL, '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 13:13:45'),
('e3019617-5464-4772-b6c2-f3082896019f', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-07-21 20:48:40', '2025-07-21 20:49:39', NULL, 1, NULL, 'closed', '96.31.87.167', 'Tampa', 'Florida', 'United States', '33614', '28.0109,-82.4948', '96-31-87-167.static.hvvc.us', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-21 20:48:40'),
('e6e9f995-041c-4afb-b535-ffd53d75c565', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-09-12 18:05:46', NULL, NULL, 0, 'invalid_password', 'failed', '200.8.108.206', 'Ciudad Bolívar', 'Bolívar', 'Venezuela', 'Unknown', '8.1187,-63.5517', '200.8.108.206', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '7add8f47-7729-4f03-bd5c-bf0b560ff467', 0, '2025-09-12 18:05:46'),
('e7a1ed22-0292-4158-a7df-e1f281f104a9', NULL, 'UNKNOWN', 'admin', 'UNKNOWN', '2025-09-15 12:28:24', NULL, NULL, 0, 'user_not_found', 'failed', '181.208.26.134', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '181.208.26.134', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'f41c85b2-d2c4-4712-9d32-a849f05440d2', 0, '2025-09-15 12:28:24'),
('eba7bf74-d852-407a-a52c-08f8ea51170f', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'moisescelis21@gmail.com', 'user', 'Moises Francisco Celis Salazar', '2025-09-15 12:40:50', '2025-09-15 12:41:37', NULL, 1, NULL, 'closed', '86.106.87.105', 'Miami', 'Florida', 'United States', '33132', '25.7838,-80.1823', '86.106.87.105', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '253c5093-807e-41fd-80f7-e00f8990d7b1', 0, '2025-09-15 12:40:50'),
('ec9fe422-f999-4b92-ac68-9742874c30d1', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 17:23:45', '2025-07-17 17:37:01', NULL, 1, NULL, 'closed', '::1', 'Unknown', 'Unknown', 'Unknown', NULL, '0.0,0.0', 'DESKTOP-BRTU0R4', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'c0002a1c-88f8-4037-8422-9c95f45d4fb3', 0, '2025-07-17 17:23:45'),
('f74ef3da-f24b-41aa-9259-c95040feaf0f', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-23 14:58:31', NULL, NULL, 0, 'invalid_password', 'failed', '172.116.235.110', 'Corona', 'California', 'United States', '92879', '33.8789,-117.5353', 'syn-172-116-235-110.res.spectrum.com', 'Windows 10', 'Mozilla Firefox', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'e71d0396-d7c0-4a23-830d-a5380b17217e', 0, '2025-07-23 14:58:31'),
('fbdfd492-7692-42ed-8985-3ad333499939', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-25 17:09:56', '2025-07-25 17:34:06', '959', 1, NULL, 'expired', '89.41.26.61', 'Los Angeles', 'California', 'United States', '90014', '34.0481,-118.2531', '89.41.26.61', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-25 17:09:56'),
('fc9e75c1-3aa7-48b4-adc7-882357868613', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'moisescelis21@gmail.com', 'admin', 'Moises Celis', '2025-07-17 00:53:36', '2025-07-17 01:23:09', '904', 1, NULL, 'expired', '108.174.60.85', 'Marietta', 'Georgia', 'United States', NULL, '33.9521,-84.5475', '108-174-60-85-host.colocrossing.com', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '13b4980f-d1e1-44e5-8f77-e4bebc307527', 0, '2025-07-17 00:53:36'),
('fd061f20-cc39-4570-8f76-af83a246ae4d', 'd08a28f7-95f4-468b-981c-9b988d7d5df9', 'fsafassfafa@gmail.com', 'admin', 'fsafsaf fsafafss', '2025-09-15 19:42:10', NULL, NULL, 0, 'invalid_password', 'failed', '190.142.206.21', 'Barquisimeto', 'Lara', 'Venezuela', 'Unknown', '10.0664,-69.3586', '190.142.206.21', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-15 19:42:10'),
('febf1a28-3063-49f9-bb36-31726d24396d', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-09-03 17:59:35', NULL, NULL, 0, 'invalid_password', 'failed', '104.223.111.226', 'Los Angeles', 'California', 'United States', '90060', '34.0544,-118.244', '104.223.111.226', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '3d33e89e-21ef-4d0d-abe8-0c1b5a4f80f5', 0, '2025-09-03 17:59:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialists`
--

CREATE TABLE `specialists` (
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `specialty_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `verified_status` enum('PENDING','APPROVED','REJECTED','VERIFIED') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'PENDING',
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`languages`)),
  `available_for_free_consults` tinyint(1) DEFAULT 0,
  `max_free_consults_per_month` int(11) DEFAULT 0,
  `system_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'US',
  `timezone` varchar(255) DEFAULT 'America/Los_Angeles',
  `birthday` date DEFAULT NULL,
  `status` int(255) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialists`
--

INSERT INTO `specialists` (`specialist_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `specialty_id`, `title_id`, `bio`, `whatsapp_link`, `website_url`, `avatar_url`, `verified_status`, `languages`, `available_for_free_consults`, `max_free_consults_per_month`, `system_type`, `timezone`, `birthday`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('2', 'Jesus', 'Zapata', 'jesusnbz22@gmail.com', '(+291) 4545445454', '$2y$12$rxl4pO9HvsSDYOnQ6.dU/uvkPkzoen.hzsuIEIbu9J.HWO4rBTdKi', '17d10ab1-c5bd-45e2-a2f9-29a2dbe4482e', '23c3f261-8e8b-4095-9986-2f1f0bfa330c', NULL, NULL, NULL, NULL, 'PENDING', NULL, 0, 0, 'US', 'America/Los_Angeles', NULL, 1, '2025-06-18 16:21:07', '2', '2025-06-25 09:52:27', '1', '2025-06-25 09:52:27', '1'),
('4', 'moises', 'celis', 'fsafafa@gmail.com', '(+58) 4249173468', '$2y$10$9xXzJNvuVZ7YhHD3BP077uS7eQlbWocsAcYEehuFzZOP9.C10JvW6', '83e72807-bba8-4b9a-b43d-c67088adea6b', '3711d360-3124-4e87-8671-3bcd6de31d9f', NULL, NULL, NULL, NULL, 'PENDING', NULL, 0, 0, 'US', 'America/Los_Angeles', NULL, 1, '2025-07-03 10:13:47', '4', '2025-07-05 08:34:00', NULL, '2025-07-05 08:34:00', '1'),
('6519d1be-db8c-4270-8177-f9b9f3f5a461', 'Jesús', 'del Barrio', 'jesusnbz23@gmail.com', '(+687) 2222222222', '$2y$12$4F5Uji00/XeoWxBqV0lfqOa1Gtb/AmmNbHwZdW/xO9xvSn3ylUYMO', '0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30', '23c3f261-8e8b-4095-9986-2f1f0bfa330c', NULL, NULL, NULL, NULL, 'PENDING', NULL, 0, 0, 'US', 'America/Los_Angeles', NULL, 1, '2025-09-15 10:13:47', '6519d1be-db8c-4270-8177-f9b9f3f5a461', NULL, NULL, NULL, NULL),
('694abbe9-2938-4cb0-96fa-9deb60ebcf4f', 'fsafa', 'fsafa', 'fsafassfa@gmail.com', '(+58) 4545645645', '$2y$10$00BAU8JpYBb/PgHmNLz9q.oP3bmeNMfpWPqKjUHK/dxz1ErJuV0zC', '0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', '0153d168-9348-45ea-bf17-f8026d2751d3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'US', 'America/Los_Angeles', NULL, 1, '2025-07-07 17:14:32', '3', '2025-07-08 05:59:46', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-08 05:59:46', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('c033baab-84d3-4bfa-bbf0-f07bf73476ae', 'jesus', 'Specialist', 'jesusmadafaka13@gmail.com', '(+291) __________', '$2y$12$FvSM/l/2VLDzDYT4K6Q6HO8TtnxzYUECkL4AnNprBjfgTt7m0J7ki', '0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', '0153d168-9348-45ea-bf17-f8026d2751d3', NULL, NULL, NULL, NULL, 'PENDING', NULL, 0, 0, 'US', 'America/Los_Angeles', NULL, 1, '2025-06-25 11:58:18', '3', '2025-07-19 10:37:02', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('c78b9311-1e0c-45f0-a415-88ee236bcbc9', 'fsafa', 'fsafafa', 'fsafasf222qfs@gmail.com', '(+687) 5252525252', '$2y$10$5Dym59N02ee71CFZWBomKeh7v7sPY7f1.SGSILdYaaWUOu83Gus3a', '0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', '0153d168-9348-45ea-bf17-f8026d2751d3', NULL, NULL, NULL, NULL, 'PENDING', NULL, 0, 0, 'US', 'America/Los_Angeles', NULL, 1, '2025-07-08 14:41:05', 'c78b9311-1e0c-45f0-a415-88ee236bcbc9', '2025-07-11 20:35:34', NULL, '2025-07-11 20:35:34', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('fdf23cb0-86f1-4902-85e3-c20a1f481835', 'moises', 'celiss', 'moisescelis21@gmail.com', '(+58) 4249173469', '$2y$12$DBdapixGgJn0aKeOeB0yyOGUhQfCxbcp6ttNntE8q8Ci/cBiH7T6e', '0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', '3711d360-3124-4e87-8671-3bcd6de31d9f', 'fsafafas', 'http://localhost/profile_specialist', 'http://localhost/profile_specialist', NULL, 'PENDING', '[\"en\",\"es\"]', 1, 5, 'US', 'America/Los_Angeles', NULL, 1, '2025-06-18 14:52:12', '1', '2025-09-12 17:07:34', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL);

--
-- Disparadores `specialists`
--
DELIMITER $$
CREATE TRIGGER `trg_specialists_delete` BEFORE DELETE ON `specialists` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialists', OLD.specialist_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'specialist_id', OLD.specialist_id,
      'first_name', OLD.first_name,
      'last_name', OLD.last_name,
      'email', OLD.email,
      'phone', OLD.phone,
      'password', OLD.password,
      'specialty_id', OLD.specialty_id,
      'title_id', OLD.title_id,
      'bio', OLD.bio,
      'whatsapp_link', OLD.whatsapp_link,
      'website_url', OLD.website_url,
      'avatar_url', OLD.avatar_url,
      'verified_status', OLD.verified_status,
      'languages', OLD.languages,
      'available_for_free_consults', OLD.available_for_free_consults,
      'max_free_consults_per_month', OLD.max_free_consults_per_month,
      'system_type', OLD.system_type,
      'timezone', OLD.timezone,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_delete_logical` AFTER UPDATE ON `specialists` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialists', OLD.specialist_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'specialist_id', OLD.specialist_id,
        'first_name', OLD.first_name,
        'last_name', OLD.last_name,
        'email', OLD.email,
        'phone', OLD.phone,
        'password', OLD.password,
        'specialty_id', OLD.specialty_id,
        'title_id', OLD.title_id,
        'bio', OLD.bio,
        'whatsapp_link', OLD.whatsapp_link,
        'website_url', OLD.website_url,
        'avatar_url', OLD.avatar_url,
        'verified_status', OLD.verified_status,
        'languages', OLD.languages,
        'available_for_free_consults', OLD.available_for_free_consults,
        'max_free_consults_per_month', OLD.max_free_consults_per_month,
        'system_type', OLD.system_type,
        'timezone', OLD.timezone,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_insert` AFTER INSERT ON `specialists` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialists', NEW.specialist_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'specialist_id', NEW.specialist_id,
      'first_name', NEW.first_name,
      'last_name', NEW.last_name,
      'email', NEW.email,
      'phone', NEW.phone,
      'password', NEW.password,
      'specialty_id', NEW.specialty_id,
      'title_id', NEW.title_id,
      'bio', NEW.bio,
      'whatsapp_link', NEW.whatsapp_link,
      'website_url', NEW.website_url,
      'avatar_url', NEW.avatar_url,
      'verified_status', NEW.verified_status,
      'languages', NEW.languages,
      'available_for_free_consults', NEW.available_for_free_consults,
      'max_free_consults_per_month', NEW.max_free_consults_per_month,
      'system_type', NEW.system_type,
      'timezone', NEW.timezone,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_update` AFTER UPDATE ON `specialists` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.first_name <> NEW.first_name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"first_name":{"old":"', escape_json(OLD.first_name), '","new":"', escape_json(NEW.first_name), '"}');
  END IF;
  IF OLD.last_name <> NEW.last_name THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"last_name":{"old":"', escape_json(OLD.last_name), '","new":"', escape_json(NEW.last_name), '"}');
  END IF;
  IF OLD.email <> NEW.email THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"email":{"old":"', escape_json(OLD.email), '","new":"', escape_json(NEW.email), '"}');
  END IF;
  IF OLD.phone <> NEW.phone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"phone":{"old":"', escape_json(OLD.phone), '","new":"', escape_json(NEW.phone), '"}');
  END IF;
  IF OLD.password <> NEW.password THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"password":{"old":"', escape_json(OLD.password), '","new":"', escape_json(NEW.password), '"}');
  END IF;
  IF OLD.specialty_id <> NEW.specialty_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"specialty_id":{"old":"', escape_json(OLD.specialty_id), '","new":"', escape_json(NEW.specialty_id), '"}');
  END IF;
  IF OLD.title_id <> NEW.title_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"title_id":{"old":"', escape_json(OLD.title_id), '","new":"', escape_json(NEW.title_id), '"}');
  END IF;
  IF OLD.bio <> NEW.bio THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"bio":{"old":"', escape_json(OLD.bio), '","new":"', escape_json(NEW.bio), '"}');
  END IF;
  IF OLD.whatsapp_link <> NEW.whatsapp_link THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"whatsapp_link":{"old":"', escape_json(OLD.whatsapp_link), '","new":"', escape_json(NEW.whatsapp_link), '"}');
  END IF;
  IF OLD.website_url <> NEW.website_url THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"website_url":{"old":"', escape_json(OLD.website_url), '","new":"', escape_json(NEW.website_url), '"}');
  END IF;
  IF OLD.avatar_url <> NEW.avatar_url THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"avatar_url":{"old":"', escape_json(OLD.avatar_url), '","new":"', escape_json(NEW.avatar_url), '"}');
  END IF;
  IF OLD.verified_status <> NEW.verified_status THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"verified_status":{"old":"', escape_json(OLD.verified_status), '","new":"', escape_json(NEW.verified_status), '"}');
  END IF;
  IF OLD.languages <> NEW.languages THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"languages":{"old":"', escape_json(OLD.languages), '","new":"', escape_json(NEW.languages), '"}');
  END IF;
  IF OLD.available_for_free_consults <> NEW.available_for_free_consults THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"available_for_free_consults":{"old":"', escape_json(OLD.available_for_free_consults), '","new":"', escape_json(NEW.available_for_free_consults), '"}');
  END IF;
  IF OLD.max_free_consults_per_month <> NEW.max_free_consults_per_month THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"max_free_consults_per_month":{"old":"', escape_json(OLD.max_free_consults_per_month), '","new":"', escape_json(NEW.max_free_consults_per_month), '"}');
  END IF;
  IF OLD.system_type <> NEW.system_type THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"system_type":{"old":"', escape_json(OLD.system_type), '","new":"', escape_json(NEW.system_type), '"}');
  END IF;
  IF OLD.timezone <> NEW.timezone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"timezone":{"old":"', escape_json(OLD.timezone), '","new":"', escape_json(NEW.timezone), '"}');
  END IF;

  -- (opcional) registrar updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialists', OLD.specialist_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialists_titles`
--

CREATE TABLE `specialists_titles` (
  `title_id` char(36) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_es` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `specialists_titles`
--

INSERT INTO `specialists_titles` (`title_id`, `name_en`, `name_es`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('0153d168-9348-45ea-bf17-f8026d2751d3', 'Gynecologist', 'Ginecólogo', NULL, NULL, '2025-09-12 17:07:17', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('069dfce4-baac-46b3-9d25-3e91cde4e60d', 'Radiologist', 'Radiólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('0a340b40-04a5-40bd-8ef4-b9bb733276c2', 'Nurse', 'Enfermero', NULL, NULL, NULL, NULL, NULL, NULL),
('150fca84-c44f-4fb0-86c0-21b28b6ac24d', 'Psychologist', 'Psicólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('23c3f261-8e8b-4095-9986-2f1f0bfa330c', 'Urologist', 'Urólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('24', 'Doctorfsa', 'Doctorsfa', '2025-07-05 07:36:11', '1', '2025-07-05 07:42:16', '1', '2025-07-05 07:42:23', '1'),
('2ee7c3f5-eb4f-4b9a-92d2-f03774226c70', 'Physiotherapist', 'Fisioterapeuta', NULL, NULL, NULL, NULL, NULL, NULL),
('300ad498-f945-43cb-b6f9-0c66f149eb64', 'Nutritionist', 'Nutricionista', NULL, NULL, NULL, NULL, NULL, NULL),
('323002d1-da10-455a-aedc-af58a74bd99a', 'Therapist', 'Terapeuta', NULL, NULL, NULL, NULL, NULL, NULL),
('3711d360-3124-4e87-8671-3bcd6de31d9f', 'Doctor', 'Doctor', NULL, NULL, '2025-07-05 07:42:08', '1', NULL, NULL),
('458ac963-69d4-4511-bdf9-740e9a77b356', 'Ophthalmologist', 'Oftalmólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('6a89572a-223b-4ca2-bbc2-3732f644b8ec', 'Neurologist', 'Neurólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('7a9b73fc-3be5-4c11-8764-8490a2f7bf8d', 'General Practitioner', 'Médico General', NULL, NULL, NULL, NULL, NULL, NULL),
('82e98a53-3004-47c5-9bac-23774930b0f0', 'Dentist', 'Dentista', NULL, NULL, NULL, NULL, NULL, NULL),
('94de8012-131c-4793-9283-58a3d354eea6', 'Surgeon', 'Cirujano', NULL, NULL, NULL, NULL, NULL, NULL),
('9ae67f3a-a551-460f-a7be-3f9ec65ca3c6', 'Cardiologist', 'Cardiólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('ab087a1b-5207-4cd4-859d-48955e00cdd3', 'xyz', 'xyz', '2025-07-11 17:51:41', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL, '2025-07-11 17:51:50', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('b283eb84-8d75-4419-83e2-28d5ef60e56a', 'abcxyz', 'abcxyz', '2025-07-11 17:53:14', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL, NULL, NULL),
('be8e35b8-54f1-4cc2-b6e2-8980b22ddfc0', 'Physician', 'Médico', NULL, NULL, NULL, NULL, NULL, NULL),
('c671a193-a25d-4bba-89d0-639d61b898e8', 'Endocrinologist', 'Endocrinólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('cd94c83f-4997-4863-ab9b-50f52ebefaac', 'Specialist', 'Especialista', NULL, NULL, NULL, NULL, NULL, NULL),
('e070263a-42ca-4fe5-ad0a-f49bb1ce9ed2', 'Hematologist', 'Hematólogo', NULL, NULL, NULL, NULL, NULL, NULL),
('e679b9a5-bd6d-4aec-ad83-7c44d999d231', 'Pediatrician', 'Pediatra', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `specialists_titles`
--
DELIMITER $$
CREATE TRIGGER `trg_specialists_titles_delete` BEFORE DELETE ON `specialists_titles` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialists_titles', OLD.title_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'title_id', OLD.title_id,
      'name_en', OLD.name_en,
      'name_es', OLD.name_es,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_titles_delete_logical` AFTER UPDATE ON `specialists_titles` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialists_titles', OLD.title_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'title_id', OLD.title_id,
        'name_en', OLD.name_en,
        'name_es', OLD.name_es,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_titles_insert` AFTER INSERT ON `specialists_titles` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialists_titles', NEW.title_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'title_id', NEW.title_id,
      'name_en', NEW.name_en,
      'name_es', NEW.name_es,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialists_titles_update` AFTER UPDATE ON `specialists_titles` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.name_en <> NEW.name_en THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name_en":{"old":"', escape_json(OLD.name_en), '","new":"', escape_json(NEW.name_en), '"}');
  END IF;
  IF OLD.name_es <> NEW.name_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name_es":{"old":"', escape_json(OLD.name_es), '","new":"', escape_json(NEW.name_es), '"}');
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialists_titles', OLD.title_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_availability`
--

CREATE TABLE `specialist_availability` (
  `availability_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `weekday` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Disparadores `specialist_availability`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_availability_delete` BEFORE DELETE ON `specialist_availability` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_availability', OLD.availability_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'availability_id', OLD.availability_id,
      'specialist_id', OLD.specialist_id,
      'weekday', OLD.weekday,
      'start_time', OLD.start_time,
      'end_time', OLD.end_time,
      'timezone', OLD.timezone,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_availability_delete_logical` AFTER UPDATE ON `specialist_availability` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_availability', OLD.availability_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'availability_id', OLD.availability_id,
        'specialist_id', OLD.specialist_id,
        'weekday', OLD.weekday,
        'start_time', OLD.start_time,
        'end_time', OLD.end_time,
        'timezone', OLD.timezone,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_availability_insert` AFTER INSERT ON `specialist_availability` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_availability', NEW.availability_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'availability_id', NEW.availability_id,
      'specialist_id', NEW.specialist_id,
      'weekday', NEW.weekday,
      'start_time', NEW.start_time,
      'end_time', NEW.end_time,
      'timezone', NEW.timezone,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_availability_update` AFTER UPDATE ON `specialist_availability` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}');
  END IF;
  IF OLD.weekday <> NEW.weekday THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"weekday":{"old":"', escape_json(OLD.weekday), '","new":"', escape_json(NEW.weekday), '"}');
  END IF;
  IF OLD.start_time <> NEW.start_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"start_time":{"old":"', escape_json(OLD.start_time), '","new":"', escape_json(NEW.start_time), '"}');
  END IF;
  IF OLD.end_time <> NEW.end_time THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"end_time":{"old":"', escape_json(OLD.end_time), '","new":"', escape_json(NEW.end_time), '"}');
  END IF;
  IF OLD.timezone <> NEW.timezone THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"timezone":{"old":"', escape_json(OLD.timezone), '","new":"', escape_json(NEW.timezone), '"}');
  END IF;

  -- (opcional) registrar updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_availability', OLD.availability_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_certifications`
--

CREATE TABLE `specialist_certifications` (
  `certification_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `visibility` enum('PUBLIC','PRIVATE') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_certifications`
--

INSERT INTO `specialist_certifications` (`certification_id`, `specialist_id`, `file_url`, `title`, `description`, `visibility`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('1', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', '/uploads/certifications/cert_6866ef7b212e3.png', 'fsfafa', 'fsafafasssss', '', '2025-07-03 13:39:53', '1', '0000-00-00 00:00:00', '1', NULL, NULL),
('2', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', '/uploads/certifications/cert_6866eab6dbbe4.pdf', 'fsafa', 'fsafass', 'PUBLIC', '2025-07-03 13:40:22', '1', '0000-00-00 00:00:00', '1', NULL, NULL),
('b9113b2d-fdcb-49f9-aada-4be380b329ae', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', '/uploads/certifications/cert_686dac7cb759c.pdf', 'fdsafa', 'fsafa', 'PUBLIC', '2025-07-08 16:40:44', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_certifications`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_certifications_delete` BEFORE DELETE ON `specialist_certifications` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_certifications', OLD.certification_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'certification_id', OLD.certification_id,
      'specialist_id', OLD.specialist_id,
      'file_url', OLD.file_url,
      'title', OLD.title,
      'description', OLD.description,
      'visibility', OLD.visibility,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_certifications_delete_logical` AFTER UPDATE ON `specialist_certifications` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_certifications', OLD.certification_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'certification_id', OLD.certification_id,
        'specialist_id', OLD.specialist_id,
        'file_url', OLD.file_url,
        'title', OLD.title,
        'description', OLD.description,
        'visibility', OLD.visibility,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_certifications_insert` AFTER INSERT ON `specialist_certifications` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_certifications', NEW.certification_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'certification_id', NEW.certification_id,
      'specialist_id', NEW.specialist_id,
      'file_url', NEW.file_url,
      'title', NEW.title,
      'description', NEW.description,
      'visibility', NEW.visibility,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_certifications_update` AFTER UPDATE ON `specialist_certifications` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}');
  END IF;
  IF OLD.file_url <> NEW.file_url THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"file_url":{"old":"', escape_json(OLD.file_url), '","new":"', escape_json(NEW.file_url), '"}');
  END IF;
  IF OLD.title <> NEW.title THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"title":{"old":"', escape_json(OLD.title), '","new":"', escape_json(NEW.title), '"}');
  END IF;
  IF OLD.description <> NEW.description THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"description":{"old":"', escape_json(OLD.description), '","new":"', escape_json(NEW.description), '"}');
  END IF;
  IF OLD.visibility <> NEW.visibility THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"visibility":{"old":"', escape_json(OLD.visibility), '","new":"', escape_json(NEW.visibility), '"}');
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_certifications', OLD.certification_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_locations`
--

CREATE TABLE `specialist_locations` (
  `location_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` char(36) DEFAULT NULL,
  `state_id` char(36) DEFAULT NULL,
  `country_id` char(36) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_locations`
--

INSERT INTO `specialist_locations` (`location_id`, `specialist_id`, `city_id`, `state_id`, `country_id`, `is_primary`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('b0bf41fa-3e39-4c21-8c53-8d3ea2c0a30d', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', '01fe0d28-988d-4237-8ec0-c405aeaaf250', '004a5632-aa7e-4977-b028-b813fa64a66d', '00515e61-97a8-425b-a2cb-421258dce0a4', 1, '0000-00-00 00:00:00', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_locations`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_locations_delete` BEFORE DELETE ON `specialist_locations` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_locations', OLD.location_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'location_id', OLD.location_id,
      'specialist_id', OLD.specialist_id,
      'city_id', OLD.city_id,
      'state_id', OLD.state_id,
      'country_id', OLD.country_id,
      'is_primary', OLD.is_primary,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_locations_delete_logical` AFTER UPDATE ON `specialist_locations` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_locations', OLD.location_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'location_id', OLD.location_id,
        'specialist_id', OLD.specialist_id,
        'city_id', OLD.city_id,
        'state_id', OLD.state_id,
        'country_id', OLD.country_id,
        'is_primary', OLD.is_primary,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_locations_insert` AFTER INSERT ON `specialist_locations` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_locations', NEW.location_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'location_id', NEW.location_id,
      'specialist_id', NEW.specialist_id,
      'city_id', NEW.city_id,
      'state_id', NEW.state_id,
      'country_id', NEW.country_id,
      'is_primary', NEW.is_primary,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_locations_update` AFTER UPDATE ON `specialist_locations` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.city_id <> NEW.city_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"city_id":{"old":"', escape_json(OLD.city_id), '","new":"', escape_json(NEW.city_id), '"}'
    );
  END IF;

  IF OLD.state_id <> NEW.state_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"state_id":{"old":"', escape_json(OLD.state_id), '","new":"', escape_json(NEW.state_id), '"}'
    );
  END IF;

  IF OLD.country_id <> NEW.country_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"country_id":{"old":"', escape_json(OLD.country_id), '","new":"', escape_json(NEW.country_id), '"}'
    );
  END IF;

  IF OLD.is_primary <> NEW.is_primary THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"is_primary":{"old":"', escape_json(OLD.is_primary), '","new":"', escape_json(NEW.is_primary), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_locations', OLD.location_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_pricing`
--

CREATE TABLE `specialist_pricing` (
  `pricing_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `service_type` enum('CONSULTATION','REVIEW','FOLLOW_UP','SUBSCRIPTION') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price_usd` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_pricing`
--

INSERT INTO `specialist_pricing` (`pricing_id`, `specialist_id`, `service_type`, `description`, `price_usd`, `is_active`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('6805f51f-94c4-11f0-b3e4-00e04cf70151', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'CONSULTATION', 'fsafafa', 10.00, 1, '2025-09-18 15:19:32', NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_pricing`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_pricing_delete` BEFORE DELETE ON `specialist_pricing` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_pricing', OLD.pricing_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'pricing_id', OLD.pricing_id,
      'specialist_id', OLD.specialist_id,
      'service_type', OLD.service_type,
      'description', OLD.description,
      'price_usd', OLD.price_usd,
      'is_active', OLD.is_active,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_pricing_delete_logical` AFTER UPDATE ON `specialist_pricing` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_pricing', OLD.pricing_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'pricing_id', OLD.pricing_id,
        'specialist_id', OLD.specialist_id,
        'service_type', OLD.service_type,
        'description', OLD.description,
        'price_usd', OLD.price_usd,
        'is_active', OLD.is_active,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_pricing_insert` AFTER INSERT ON `specialist_pricing` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_pricing', NEW.pricing_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'pricing_id', NEW.pricing_id,
      'specialist_id', NEW.specialist_id,
      'service_type', NEW.service_type,
      'description', NEW.description,
      'price_usd', NEW.price_usd,
      'is_active', NEW.is_active,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_pricing_update` AFTER UPDATE ON `specialist_pricing` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.service_type <> NEW.service_type THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"service_type":{"old":"', escape_json(OLD.service_type), '","new":"', escape_json(NEW.service_type), '"}'
    );
  END IF;

  IF OLD.description <> NEW.description THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"description":{"old":"', escape_json(OLD.description), '","new":"', escape_json(NEW.description), '"}'
    );
  END IF;

  IF OLD.price_usd <> NEW.price_usd THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"price_usd":{"old":"', escape_json(OLD.price_usd), '","new":"', escape_json(NEW.price_usd), '"}'
    );
  END IF;

  IF OLD.is_active <> NEW.is_active THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"is_active":{"old":"', escape_json(OLD.is_active), '","new":"', escape_json(NEW.is_active), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_pricing', OLD.pricing_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_reviews`
--

CREATE TABLE `specialist_reviews` (
  `review_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_reviews`
--

INSERT INTO `specialist_reviews` (`review_id`, `specialist_id`, `user_id`, `rating`, `comment`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('3ae8a497-94c4-11f0-b3e4-00e04cf70151', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', '5c5434da-06cc-42a0-8b52-bacbb5ee93b2', 5, 'fsafafa', '2025-09-18 15:18:17', NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_reviews`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_reviews_delete` BEFORE DELETE ON `specialist_reviews` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_reviews', OLD.review_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'review_id', OLD.review_id,
      'specialist_id', OLD.specialist_id,
      'user_id', OLD.user_id,
      'rating', OLD.rating,
      'comment', OLD.comment,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_reviews_delete_logical` AFTER UPDATE ON `specialist_reviews` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_reviews', OLD.review_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'review_id', OLD.review_id,
        'specialist_id', OLD.specialist_id,
        'user_id', OLD.user_id,
        'rating', OLD.rating,
        'comment', OLD.comment,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_reviews_insert` AFTER INSERT ON `specialist_reviews` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_reviews', NEW.review_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'review_id', NEW.review_id,
      'specialist_id', NEW.specialist_id,
      'user_id', NEW.user_id,
      'rating', NEW.rating,
      'comment', NEW.comment,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_reviews_update` AFTER UPDATE ON `specialist_reviews` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}'
    );
  END IF;

  IF OLD.rating <> NEW.rating THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"rating":{"old":"', escape_json(OLD.rating), '","new":"', escape_json(NEW.rating), '"}'
    );
  END IF;

  IF OLD.comment <> NEW.comment THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"comment":{"old":"', escape_json(OLD.comment), '","new":"', escape_json(NEW.comment), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_reviews', OLD.review_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_social_links`
--

CREATE TABLE `specialist_social_links` (
  `social_link_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_social_links`
--

INSERT INTO `specialist_social_links` (`social_link_id`, `specialist_id`, `platform`, `url`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('1', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'facebook', 'https://www.facebook.com/moises.celis', '2025-07-03 12:01:30', 1, '2025-07-03 12:10:39', NULL, '2025-07-03 12:10:39', 1),
('2', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'facebook', 'https://www.facebook.com/moises.celis', '2025-07-03 12:10:49', 1, '2025-07-03 15:00:54', 1, NULL, NULL),
('233355e0-8ead-4105-9273-859fc4c1f2e8', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'facebook', '', '2025-09-15 16:53:15', 0, NULL, NULL, NULL, NULL),
('3', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'twitter', 'https://www.facebook.com/moises.celis', '2025-07-03 12:11:49', 1, NULL, NULL, NULL, NULL),
('4', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'instagram', 'https://www.facebook.com/moises.celis', '2025-07-03 12:11:56', 1, NULL, NULL, NULL, NULL),
('5', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'twitter', 'fsafa', '2025-07-03 14:25:32', 1, NULL, NULL, NULL, NULL),
('9b4cb6e9-bdee-439c-8586-b511529fbd0d', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'facebook', '', '2025-09-15 16:52:53', 0, NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_social_links`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_social_links_delete` BEFORE DELETE ON `specialist_social_links` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_social_links', OLD.social_link_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'social_link_id', OLD.social_link_id,
      'specialist_id', OLD.specialist_id,
      'platform', OLD.platform,
      'url', OLD.url,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_social_links_delete_logical` AFTER UPDATE ON `specialist_social_links` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_social_links', OLD.social_link_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'social_link_id', OLD.social_link_id,
        'specialist_id', OLD.specialist_id,
        'platform', OLD.platform,
        'url', OLD.url,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_social_links_insert` AFTER INSERT ON `specialist_social_links` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_social_links', NEW.social_link_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'social_link_id', NEW.social_link_id,
      'specialist_id', NEW.specialist_id,
      'platform', NEW.platform,
      'url', NEW.url,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_social_links_update` AFTER UPDATE ON `specialist_social_links` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.platform <> NEW.platform THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"platform":{"old":"', escape_json(OLD.platform), '","new":"', escape_json(NEW.platform), '"}'
    );
  END IF;

  IF OLD.url <> NEW.url THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"url":{"old":"', escape_json(OLD.url), '","new":"', escape_json(NEW.url), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_social_links', OLD.social_link_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialist_verification_requests`
--

CREATE TABLE `specialist_verification_requests` (
  `verification_request_id` char(36) NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `submitted_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `admin_id` char(36) DEFAULT NULL,
  `verification_level` enum('STANDARD','PLUS') DEFAULT 'STANDARD',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `specialist_verification_requests`
--

INSERT INTO `specialist_verification_requests` (`verification_request_id`, `specialist_id`, `status`, `submitted_at`, `approved_at`, `admin_id`, `verification_level`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('59b3877d-94c3-11f0-b3e4-00e04cf70151', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', 'APPROVED', '2025-09-18 15:11:59', '2025-09-17 15:11:05', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', 'PLUS', '2025-09-18 15:11:59', NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `specialist_verification_requests`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_verification_requests_delete` BEFORE DELETE ON `specialist_verification_requests` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_verification_requests', OLD.verification_request_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'verification_request_id', OLD.verification_request_id,
      'specialist_id', OLD.specialist_id,
      'status', OLD.status,
      'submitted_at', OLD.submitted_at,
      'approved_at', OLD.approved_at,
      'admin_id', OLD.admin_id,
      'verification_level', OLD.verification_level,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_verification_requests_delete_logical` AFTER UPDATE ON `specialist_verification_requests` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_verification_requests', OLD.verification_request_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'verification_request_id', OLD.verification_request_id,
        'specialist_id', OLD.specialist_id,
        'status', OLD.status,
        'submitted_at', OLD.submitted_at,
        'approved_at', NEW.approved_at,
        'admin_id', NEW.admin_id,
        'verification_level', NEW.verification_level,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_verification_requests_insert` AFTER INSERT ON `specialist_verification_requests` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialist_verification_requests', NEW.verification_request_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'verification_request_id', NEW.verification_request_id,
      'specialist_id', NEW.specialist_id,
      'status', NEW.status,
      'submitted_at', NEW.submitted_at,
      'approved_at', NEW.approved_at,
      'admin_id', NEW.admin_id,
      'verification_level', NEW.verification_level,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialist_verification_requests_update` AFTER UPDATE ON `specialist_verification_requests` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.status <> NEW.status THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"status":{"old":"', escape_json(OLD.status), '","new":"', escape_json(NEW.status), '"}'
    );
  END IF;

  IF OLD.submitted_at <> NEW.submitted_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"submitted_at":{"old":"', escape_json(OLD.submitted_at), '","new":"', escape_json(NEW.submitted_at), '"}'
    );
  END IF;

  IF OLD.approved_at <> NEW.approved_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"approved_at":{"old":"', escape_json(OLD.approved_at), '","new":"', escape_json(NEW.approved_at), '"}'
    );
  END IF;

  IF OLD.admin_id <> NEW.admin_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"admin_id":{"old":"', escape_json(OLD.admin_id), '","new":"', escape_json(NEW.admin_id), '"}'
    );
  END IF;

  IF OLD.verification_level <> NEW.verification_level THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"verification_level":{"old":"', escape_json(OLD.verification_level), '","new":"', escape_json(NEW.verification_level), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialist_verification_requests', OLD.verification_request_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `specialty`
--

CREATE TABLE `specialty` (
  `specialty_id` char(36) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `name_es` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `specialty`
--

INSERT INTO `specialty` (`specialty_id`, `name_en`, `name_es`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('0bff4eaa-ed0d-44b6-9dd2-86de60e47e34', 'Gynecology', 'Ginecología', NULL, NULL, '2025-09-12 17:07:21', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('0e4f3ffc-bf3e-4b6d-ab0f-d97776b0de30', 'Allergy and Immunology', 'Alergología e Inmunología', NULL, NULL, NULL, NULL, NULL, NULL),
('1656ad34-0db6-4c29-8c90-d029c3182bb9', 'abcxyz123', 'abcxyz123', '2025-07-16 19:28:03', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-16 19:33:02', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-16 19:35:25', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('17d10ab1-c5bd-45e2-a2f9-29a2dbe4482e', 'Occupational Medicine', 'Medicina del Trabajo', NULL, NULL, NULL, NULL, NULL, NULL),
('239c8b43-44bc-46e9-982b-3f4244f20a62', 'Orthopedics', 'Ortopedia', NULL, NULL, NULL, NULL, NULL, NULL),
('3065df0b-ceef-4cef-95df-e2f417fb27bd', 'fsafa', 'fsafa', '2025-07-16 14:27:11', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL, '2025-07-16 14:27:15', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('3e27cc9e-2c11-462b-94ae-6b1706f44aba', 'Infectious Disease', 'Enfermedades Infecciosas', NULL, NULL, NULL, NULL, NULL, NULL),
('4b8f6f1a-1f9d-42b5-9542-63a33b7ed2fb', 'Nephrology', 'Nefrología', NULL, NULL, NULL, NULL, NULL, NULL),
('59363e66-364c-4348-a2b0-586dab755f23', 'Dermatology', 'Dermatología', NULL, NULL, NULL, NULL, NULL, NULL),
('5d033ee3-b83e-4d62-8537-f168b3a94461', 'Hematology', 'Hematología', NULL, NULL, NULL, NULL, NULL, NULL),
('7935b35a-fcbf-4493-b52a-6e669e9ec31f', 'abcxyz', 'abcxyz', '2025-07-11 17:54:53', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL, '2025-07-11 17:55:01', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('81ae92d5-3620-46d1-ac21-7badb0d15cad', 'Rheumatology', 'Reumatología', NULL, NULL, NULL, NULL, NULL, NULL),
('83e72807-bba8-4b9a-b43d-c67088adea6b', 'Cardiology', 'Cardiología', NULL, NULL, '2025-07-05 07:44:08', '1', NULL, NULL),
('88220b6e-dd96-4789-8d59-d9578629dafa', 'Endocrinology', 'Endocrinología', NULL, NULL, NULL, NULL, NULL, NULL),
('89cb9de3-0ad9-4b55-80be-80da4a8c2c49', 'Ophthalmology', 'Oftalmología', NULL, NULL, NULL, NULL, NULL, NULL),
('91d51396-7b03-4d7a-837b-1d3280f6cb1a', 'Urology', 'Urología', NULL, NULL, NULL, NULL, NULL, NULL),
('aae4a566-1a0d-4b65-a487-f4c2ac86d6bd', 'Neurology', 'Neurología', NULL, NULL, NULL, NULL, NULL, NULL),
('abcedc27-5960-4112-b7d7-7a34528e872f', 'Gastroenterology', 'Gastroenterología', NULL, NULL, NULL, NULL, NULL, NULL),
('b32d75ae-065c-4edf-81a4-40cf841646fe', 'Anesthesiology', 'Anestesiología', NULL, NULL, NULL, NULL, NULL, NULL),
('b92b91c8-20cb-46e8-8590-1c6e14bf2cd3', 'Pulmonology', 'Neumología', NULL, NULL, NULL, NULL, NULL, NULL),
('cbce97b4-4cf5-4dac-8593-5e07882a0745', 'prueba hoy', 'prueba hoys', '2025-07-15 17:29:23', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-15 15:31:07', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('d5efea3c-624e-4918-9015-741fdbbfb058', 'Pediatrics', 'Pediatría', NULL, NULL, NULL, NULL, NULL, NULL),
('dc8cff74-248a-4199-9f0d-3994e6d80653', 'Oncology', 'Oncología', NULL, NULL, NULL, NULL, NULL, NULL),
('edd7697e-7544-4c4e-8269-8b77a055fbe6', 'Psychiatry', 'Psiquiatría', NULL, NULL, NULL, NULL, NULL, NULL),
('fc6c4516-935b-4879-b62d-f68d0334d24b', 'xyzabc EN', 'xyzabc ES', '2025-07-15 14:50:56', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-15 15:02:57', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', '2025-07-15 15:04:06', '3a3963c7-a08e-44b9-9a89-7081a04b2c42');

--
-- Disparadores `specialty`
--
DELIMITER $$
CREATE TRIGGER `trg_specialty_delete` BEFORE DELETE ON `specialty` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialty', OLD.specialty_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'specialty_id', OLD.specialty_id,
      'name_en', OLD.name_en,
      'name_es', OLD.name_es,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialty_delete_logical` AFTER UPDATE ON `specialty` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialty', OLD.specialty_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'specialty_id', OLD.specialty_id,
        'name_en', OLD.name_en,
        'name_es', OLD.name_es,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialty_insert` AFTER INSERT ON `specialty` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'specialty', NEW.specialty_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'specialty_id', NEW.specialty_id,
      'name_en', NEW.name_en,
      'name_es', NEW.name_es,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_specialty_update` AFTER UPDATE ON `specialty` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.name_en <> NEW.name_en THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"name_en":{"old":"', escape_json(OLD.name_en), '","new":"', escape_json(NEW.name_en), '"}'
    );
  END IF;

  IF OLD.name_es <> NEW.name_es THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"name_es":{"old":"', escape_json(OLD.name_es), '","new":"', escape_json(NEW.name_es), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'specialty', OLD.specialty_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `states`
--

CREATE TABLE `states` (
  `state_id` char(36) NOT NULL,
  `country_id` char(36) NOT NULL,
  `state_name` varchar(150) NOT NULL,
  `state_code` varchar(10) DEFAULT NULL,
  `iso3166_2` varchar(12) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `states`
--

INSERT INTO `states` (`state_id`, `country_id`, `state_name`, `state_code`, `iso3166_2`, `type`, `timezone`, `latitude`, `longitude`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('004a5632-aa7e-4977-b028-b813fa64a66d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'La Guaira', 'X', 'VE-X', 'state', 'America/Caracas', 10.60003840, -66.92964050, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('0a3d7f91-c258-4ced-ac8a-d38ad5766265', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Falcón', 'I', 'VE-I', 'state', 'America/Caracas', 11.27394600, -69.58342050, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('1035c65e-5189-4021-9245-7ffa7c421795', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Nueva Esparta', 'O', 'VE-O', 'state', 'America/Caracas', 10.96454150, -64.09754470, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('137a2652-c0e5-4684-b658-f48e51ece6a0', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Cojedes', 'H', 'VE-H', 'state', 'America/Caracas', 9.30944570, -68.35905460, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('34e52a5f-9593-4582-bc92-bb42da70ed88', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Lara', 'K', 'VE-K', 'state', 'America/Caracas', 10.07142660, -70.01632410, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('351ac13e-05f8-40f9-adf8-4a74db40338d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Guárico', 'J', 'VE-J', 'state', 'America/Caracas', 8.83785850, -66.38210000, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('3cedd23a-c38f-477c-9bdb-5bc78875b137', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Distrito Capital', 'A', 'VE-A', 'capital district', 'America/Caracas', 10.47495430, -66.97080410, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('4128d25f-f85e-4b98-bf88-7ce264f0d323', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Monagas', 'N', 'VE-N', 'state', 'America/Caracas', 9.34378460, -63.15893180, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('532c8da4-cd4b-4d07-b0d4-c3b69f7c262d', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Delta Amacuro', 'Y', 'VE-Y', 'state', 'America/Caracas', 8.94145990, -61.34032730, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('58c0ed8b-cf6e-4212-a6e2-96d44f80b19c', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Miranda', 'M', 'VE-M', 'state', 'America/Caracas', 10.32335100, -66.48421210, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('680549a3-58ea-4b1b-8b5d-2215528c10c4', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Apure', 'C', 'VE-C', 'state', 'America/Caracas', 7.07357810, -68.82020250, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('71784f75-9dc0-47a6-8393-600db1ac2348', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Mérida', 'L', 'VE-L', 'state', 'America/Caracas', 8.49117050, -71.30434390, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('76ee1322-fe05-407c-996c-16dbd9c10005', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Portuguesa', 'P', 'VE-P', 'state', 'America/Caracas', 8.96748080, -69.39152050, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('7abf6658-5211-4408-9ddb-7700df01bdf8', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Táchira', 'S', 'VE-S', 'state', 'America/Caracas', 8.02197480, -72.02057630, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('8925b8fc-efb4-4fdb-95bf-b6145d1a665e', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Barinas', 'E', 'VE-E', 'state', 'America/Caracas', 8.61827370, -70.22740650, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('8bdaed5a-61a8-4d42-8c46-8f70602a0677', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Yaracuy', 'U', 'VE-U', 'state', 'America/Caracas', 10.30403120, -68.70140180, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('968d1ef5-ed08-4f80-8f75-bcc47ce448b9', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Carabobo', 'G', 'VE-G', 'state', 'America/Caracas', 10.21372580, -68.03963450, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('9d5806a9-a597-4331-975f-d94d14d0c34f', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Sucre', 'R', 'VE-R', 'state', 'America/Caracas', 10.42901500, -63.56499990, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('9e45be24-0aa1-48f9-a323-1c7349c9baa7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Venezuela', 'W', 'VE-W', 'federal dependency', 'America/Caracas', 8.00187090, -66.11093180, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('abfbfa73-b6c4-4e68-b79f-0a3a94e73bb7', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Bolívar', 'F', 'VE-F', 'state', 'America/Caracas', 8.00187090, -66.11093180, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('acd519b6-c8dc-4396-ba5a-569080144320', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Anzoátegui', 'B', 'VE-B', 'state', 'America/Caracas', 9.01979250, -64.21680010, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL),
('c9b25672-41ee-4426-9217-94757da31990', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Zulia', 'V', 'VE-V', 'state', 'America/Caracas', 10.10650990, -71.86413070, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('cff0a434-036e-4f6b-a753-2329741ddb26', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Trujillo', 'T', 'VE-T', 'state', 'America/Caracas', 9.49581290, -70.76889070, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('e0d9b0e3-fc0c-4d29-ad23-29791ef3e2ff', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Aragua', 'D', 'VE-D', 'state', 'America/Caracas', 9.98975640, -67.08392570, '2025-09-17 16:43:44', NULL, NULL, NULL, NULL, NULL),
('fede7151-db16-4607-bb26-f1c83ee32c1a', 'aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'Amazonas', 'Z', 'VE-Z', 'state', 'America/Caracas', 3.42257500, -65.72323100, '2025-09-17 16:43:43', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `test_documents`
--

CREATE TABLE `test_documents` (
  `test_documents_id` char(36) NOT NULL,
  `id_test_panel` char(36) NOT NULL,
  `id_test` int(255) NOT NULL,
  `name_image` longtext NOT NULL,
  `description` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `test_documents`
--

INSERT INTO `test_documents` (`test_documents_id`, `id_test_panel`, `id_test`, `name_image`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('1', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 9017, 'img_685c36411e161.pdf', 'Test', '2025-06-25 10:47:45', '211', NULL, NULL, '2025-06-25 10:48:11', '211'),
('18d9ab4f-4482-448a-a116-fe3727d69090', 'e6861593-7327-4f63-9511-11d56f5398dc', 0, 'img_6876b3def3d55.jpg', 'daasfa', '2025-07-15 13:02:29', '3072b979-43a9-4640-a473-5650c4a82d54', '2025-07-15 13:02:39', '3072b979-43a9-4640-a473-5650c4a82d54', '2025-07-15 13:02:42', '3072b979-43a9-4640-a473-5650c4a82d54'),
('3', '60819af9-0533-472c-9d5a-24a5df5a83f7', 1, 'img_68697ef84f2e4.jpg', 'fsaf', '2025-07-05 12:37:28', '201', NULL, NULL, '2025-07-05 12:40:26', '201'),
('4', '60819af9-0533-472c-9d5a-24a5df5a83f7', 1, 'img_6869804c53705.jpg', 'fsaf', '2025-07-05 12:40:18', '201', '2025-07-05 12:43:08', '201', '2025-07-05 12:43:14', '201'),
('8294a204-9603-45f8-bcc2-a317a135f631', '81054d57-92c9-4df8-a6dc-51334c1d82c4', 9012, 'img_68646f43e4f95.jpg', 'ok', '2025-07-01 16:29:08', '201', NULL, NULL, NULL, NULL);

--
-- Disparadores `test_documents`
--
DELIMITER $$
CREATE TRIGGER `trg_test_documents_delete` BEFORE DELETE ON `test_documents` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'test_documents', OLD.test_documents_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'test_documents_id', OLD.test_documents_id,
      'id_test_panel', OLD.id_test_panel,
      'id_test', OLD.id_test,
      'name_image', OLD.name_image,
      'description', OLD.description,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_documents_delete_logical` AFTER UPDATE ON `test_documents` FOR EACH ROW BEGIN
  -- DECLARE al inicio
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'test_documents', OLD.test_documents_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'test_documents_id', OLD.test_documents_id,
        'id_test_panel', OLD.id_test_panel,
        'id_test', OLD.id_test,
        'name_image', OLD.name_image,
        'description', OLD.description,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_documents_insert` AFTER INSERT ON `test_documents` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, v_geo_ip_timezone
  ) VALUES (
    'test_documents', NEW.test_documents_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'test_documents_id', NEW.test_documents_id,
      'id_test_panel', NEW.id_test_panel,
      'id_test', NEW.id_test,
      'name_image', NEW.name_image,
      'description', NEW.description,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_documents_update` AFTER UPDATE ON `test_documents` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.id_test_panel <> NEW.id_test_panel THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"id_test_panel":{"old":"', escape_json(OLD.id_test_panel), '","new":"', escape_json(NEW.id_test_panel), '"}'
    );
  END IF;

  IF OLD.id_test <> NEW.id_test THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"id_test":{"old":"', escape_json(OLD.id_test), '","new":"', escape_json(NEW.id_test), '"}'
    );
  END IF;

  IF OLD.name_image <> NEW.name_image THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"name_image":{"old":"', escape_json(OLD.name_image), '","new":"', escape_json(NEW.name_image), '"}'
    );
  END IF;

  IF OLD.description <> NEW.description THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"description":{"old":"', escape_json(OLD.description), '","new":"', escape_json(NEW.description), '"}'
    );
  END IF;

  -- (opcional) updated_at/updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'test_documents', OLD.test_documents_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `test_panels`
--

CREATE TABLE `test_panels` (
  `panel_id` char(36) NOT NULL,
  `panel_name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `display_name_es` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `test_panels`
--

INSERT INTO `test_panels` (`panel_id`, `panel_name`, `display_name`, `display_name_es`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('60819af9-0533-472c-9d5a-24a5df5a83f7', 'renal_function', 'Renal Function', 'Funcion Renal', '2025-06-23 09:56:57', '1', '2025-07-05 08:39:58', '1', NULL, NULL),
('7ff39dd8-01e9-443c-b8e6-0d6b429e63a6', 'energy_metabolism', 'Energy Metabolism', 'Energia Metabolica', NULL, NULL, '2025-06-23 09:56:13', '1', NULL, NULL),
('81054d57-92c9-4df8-a6dc-51334c1d82c4', 'body_composition', 'Body Composition', 'Composición Corporal', NULL, NULL, '2025-09-12 17:07:39', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('e6861593-7327-4f63-9511-11d56f5398dc', 'lipid_profile_record', 'Lipid Profile', 'Perfil Lipídico', NULL, NULL, '2025-06-23 09:56:35', '1', NULL, NULL);

--
-- Disparadores `test_panels`
--
DELIMITER $$
CREATE TRIGGER `trg_test_panels_delete` BEFORE DELETE ON `test_panels` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'test_panels', OLD.panel_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'panel_id', OLD.panel_id,
      'panel_name', OLD.panel_name,
      'display_name', OLD.display_name,
      'display_name_es', OLD.display_name_es,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_panels_delete_logical` AFTER UPDATE ON `test_panels` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'test_panels', OLD.panel_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'panel_id', OLD.panel_id,
        'panel_name', OLD.panel_name,
        'display_name', OLD.display_name,
        'display_name_es', OLD.display_name_es,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_panels_insert` AFTER INSERT ON `test_panels` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'test_panels', NEW.panel_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'panel_id', NEW.panel_id,
      'panel_name', NEW.panel_name,
      'display_name', NEW.display_name,
      'display_name_es', NEW.display_name_es,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_test_panels_update` AFTER UPDATE ON `test_panels` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.panel_name <> NEW.panel_name THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"panel_name":{"old":"', escape_json(OLD.panel_name), '","new":"', escape_json(NEW.panel_name), '"}'
    );
  END IF;

  IF OLD.display_name <> NEW.display_name THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"display_name":{"old":"', escape_json(OLD.display_name), '","new":"', escape_json(NEW.display_name), '"}'
    );
  END IF;

  IF OLD.display_name_es <> NEW.display_name_es THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"display_name_es":{"old":"', escape_json(OLD.display_name_es), '","new":"', escape_json(NEW.display_name_es), '"}'
    );
  END IF;

  -- (opcional) updated_at / updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'test_panels', OLD.panel_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` char(36) NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pricing_id` char(36) DEFAULT NULL,
  `amount_usd` decimal(10,2) DEFAULT NULL,
  `type` enum('CONSULTATION','SUBSCRIPTION','VERIFICATION') DEFAULT NULL,
  `platform_fee` decimal(10,2) DEFAULT NULL,
  `status` enum('PENDING','COMPLETED','FAILED','REFUNDED') DEFAULT 'PENDING',
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Disparadores `transactions`
--
DELIMITER $$
CREATE TRIGGER `trg_transactions_delete` BEFORE DELETE ON `transactions` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'transactions', OLD.transaction_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'transaction_id', OLD.transaction_id,
      'user_id', OLD.user_id,
      'specialist_id', OLD.specialist_id,
      'pricing_id', OLD.pricing_id,
      'amount_usd', OLD.amount_usd,
      'type', OLD.type,
      'platform_fee', OLD.platform_fee,
      'status', OLD.status,
      'payment_reference', OLD.payment_reference,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_transactions_delete_logical` AFTER UPDATE ON `transactions` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'transactions', OLD.transaction_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'transaction_id', OLD.transaction_id,
        'user_id', OLD.user_id,
        'specialist_id', OLD.specialist_id,
        'pricing_id', OLD.pricing_id,
        'amount_usd', OLD.amount_usd,
        'type', OLD.type,
        'platform_fee', OLD.platform_fee,
        'status', OLD.status,
        'payment_reference', OLD.payment_reference,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_transactions_insert` AFTER INSERT ON `transactions` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'transactions', NEW.transaction_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'transaction_id', NEW.transaction_id,
      'user_id', NEW.user_id,
      'specialist_id', NEW.specialist_id,
      'pricing_id', NEW.pricing_id,
      'amount_usd', NEW.amount_usd,
      'type', NEW.type,
      'platform_fee', NEW.platform_fee,
      'status', NEW.status,
      'payment_reference', NEW.payment_reference,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_transactions_update` AFTER UPDATE ON `transactions` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usa escape_json en todos los valores)
  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}'
    );
  END IF;

  IF OLD.specialist_id <> NEW.specialist_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"specialist_id":{"old":"', escape_json(OLD.specialist_id), '","new":"', escape_json(NEW.specialist_id), '"}'
    );
  END IF;

  IF OLD.pricing_id <> NEW.pricing_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"pricing_id":{"old":"', escape_json(OLD.pricing_id), '","new":"', escape_json(NEW.pricing_id), '"}'
    );
  END IF;

  IF OLD.amount_usd <> NEW.amount_usd THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"amount_usd":{"old":"', escape_json(OLD.amount_usd), '","new":"', escape_json(NEW.amount_usd), '"}'
    );
  END IF;

  IF OLD.type <> NEW.type THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"type":{"old":"', escape_json(OLD.type), '","new":"', escape_json(NEW.type), '"}'
    );
  END IF;

  IF OLD.platform_fee <> NEW.platform_fee THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"platform_fee":{"old":"', escape_json(OLD.platform_fee), '","new":"', escape_json(NEW.platform_fee), '"}'
    );
  END IF;

  IF OLD.status <> NEW.status THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"status":{"old":"', escape_json(OLD.status), '","new":"', escape_json(NEW.status), '"}'
    );
  END IF;

  IF OLD.payment_reference <> NEW.payment_reference THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"payment_reference":{"old":"', escape_json(OLD.payment_reference), '","new":"', escape_json(NEW.payment_reference), '"}'
    );
  END IF;

  -- (opcional) updated_at / updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'transactions', OLD.transaction_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `height` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `system_type` varchar(255) NOT NULL,
  `timezone` varchar(255) DEFAULT 'America/Los_Angeles',
  `status` int(255) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `sex`, `birthday`, `height`, `email`, `password`, `telephone`, `system_type`, `timezone`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('00828a05-0dcd-4b07-ac5f-a1781431f76d', 'Jesus', 'Zapata', 'm', '2001-11-17', '5\'08\"', 'jesusnbz22@gmail.com', '$2y$12$xcc33DsduAcbSUyGMcaGC.XKUHUfzah6IpxuDmijADMEaqPv0lDIC', '(+58) 4249541159', 'EU', 'America/Los_Angeles', 1, '2025-06-16 09:19:32', '210', '2025-06-18 16:19:42', '210', '2025-07-11 20:35:09', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('00ea255b-b402-43f0-92ac-3ffdac1a76d1', 'Stephen', 'Lawson', 'm', '1985-07-09', '6\'03\"', 'jrichards@ramirez.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) (626)423-8682', 'US', 'America/Los_Angeles', 1, NULL, NULL, '2025-07-08 05:57:11', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('0ed9b500-aa8f-4029-8a28-3f79e851ef32', 'Kristen', 'Duran', 'f', '1985-07-09', '6\'06\"', 'vclements@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('1051752d-e401-4ced-9c0e-c929253dc36a', 'Ruben', 'Morrison', 'm', '1985-07-09', '4\'06\"', 'gvaldez@li-murphy.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('12a2b39a-308e-4548-8b6f-a77cbadd1a3a', 'Brittany', 'Williams', 'f', '1985-07-09', '4\'10\"', 'lori61@jordan-barton.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('17743c1b-35c8-4952-8269-9debd28eeb36', 'Sandy', 'Morrow', 'f', '1985-07-09', '6\'05\"', 'adamsdaniel@johnson-potter.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('1c459926-7abb-4a63-84bb-43d04d8931db', 'William', 'Johnson', 'm', '1985-07-09', '5\'01\"', 'alison86@whitaker-tapia.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('209', 'fsafafaff', 'ffff', 'm', '2025-06-03', '0', 'fsaf2f2fasfazxzvzvzvxz@gmail.com', '$2y$12$eCyu4Rx/fN8PJG1hUNiveOY6tDy6SCnh8.6o40R2l2ZXCMOtlPmX2', '(+291) 4546546541', 'US', 'America/Los_Angeles', 1, '2025-06-16 06:43:10', '209', NULL, NULL, '2025-07-05 08:38:22', '1'),
('2e040ee0-8ed4-43db-920a-dc648345cdc4', 'Susan', 'Mcdonald', 'f', '1985-07-09', '5\'09\"', 'mackenzie23@fletcher.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('2ea94ca9-90b0-40b4-a119-a1dd60154828', 'Jesus', 'Zapatin', 'f', '2001-11-17', '5\'08\"', 'jesusnbz22@gmail.com', '$2y$12$I4WcQeXi3tV8fqvnz/xQse3qXRPd/haXv.HweGCEdgf/8mabXGmnW', '(+58) 4249541159', 'US', 'America/Los_Angeles', 1, '2025-06-25 09:57:08', '211', '2025-09-15 09:43:13', '1ec9501f-047f-469c-af5d-a71ce4a121bb', NULL, NULL),
('33a74bbe-5ff7-45ad-ace8-4b64206626e6', 'Kari', 'Richardson', 'f', '1985-07-09', '4\'11\"', 'jacksontaylor@clarke.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('34e023d4-5339-41bc-a6ee-ed8cfcbebb77', 'Jesús', 'Zapata', 'm', '2001-11-17', '05\'68\"', 'jesuszapata@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('4569d591-cf20-4403-9b48-9cfeeee755f8', 'Moises', 'Celis', 'm', '2000-02-07', '5\'07\"', 'moisescelis2fsafafaf1@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'EU', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('481d6f3d-d4f2-4238-b028-c1d3eb680316', 'Jesús', 'del Barrio', 'f', '2001-11-11', '5\'08\"', 'jesustest@gmail.com', '$2y$12$Yx7z.YDmK2VkeVCi3YmQdOxKHHIvHvIaX8GS2PdD.4dQd0x49uKVe', '(+58) 1231231231', 'EU', 'America/Los_Angeles', 1, '2025-06-25 13:42:39', '212', '2025-06-25 13:43:16', '212', NULL, NULL),
('4bd0b254-0555-4e41-aa6c-50019e3d44e0', 'Moises', 'Celis', 'm', '2000-02-07', '04\'10\"', 'moisescfsafaelis21@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('52c0cd24-6a7a-42bb-88f9-f1211da1cfe1', 'William', 'Snyder', 'm', '1985-07-09', '5\'11\"', 'matthewbutler@dalton.org', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('5362cba8-5207-4be7-aff8-eea3d6555b02', 'John', 'Thompson', 'm', '1985-07-09', '4\'02\"', 'russell35@mora.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('5c5434da-06cc-42a0-8b52-bacbb5ee93b2', 'Melissa', 'Rogers', 'f', '1985-07-09', '6\'07\"', 'john44@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('6147c291-2612-4851-b199-5abd0c273500', 'Margaret', 'Spears', 'f', '1985-07-09', '4\'03\"', 'danielbrown@brown.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('655cf322-3bae-4b1d-b246-4ea0697a2524', 'Raymond', 'Jacobson', 'm', '1985-07-09', '4\'10\"', 'garciajohn@dean.org', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('65a8c55e-8342-45b5-87f6-cd99de660548', 'Craig', 'Nichols', 'm', '1985-07-09', '6\'03\"', 'ashleygreene@davidson-kelly.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('6813f303-1a67-4723-9588-38c2faa63503', 'Elizabeth', 'Clark', 'f', '1985-07-09', '4\'09\"', 'bradleybrandon@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('6b323fec-ab7e-48f9-a5d9-aa3c98df28d7', 'Joshua', 'Peters', 'm', '1985-07-09', '6\'06\"', 'arivas@moore.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('6d7ba45e-ec1d-4e2a-93d0-5b3ccb76d4bd', 'Eric', 'Fox', 'm', '1985-07-09', '5\'04\"', 'kevin50@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('70d56569-183b-43a9-a361-f367b32cfba8', 'Ryan', 'Tate', 'm', '1985-07-09', '4\'11\"', 'owenskaren@harvey.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('77eb9309-5203-4057-8f7e-8ee813a4601a', 'Catherine', 'Matthews', 'f', '1985-07-09', '5\'08\"', 'munozdanielle@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('7e2f64b0-413b-422e-9801-a7ecba3ad3d1', 'Cheryl', 'Reyes', 'f', '1985-07-09', '6\'00\"', 'tiffany27@lewis-harrington.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('82f54b3c-cdcd-42ac-9e75-0ba90064d2af', 'Stephanie', 'Mercado', 'f', '1985-07-09', '5\'06\"', 'alexis77@thomas.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('83351c6c-9e66-4e6d-a74e-d0b9b26f1a9e', 'Dustin', 'Watson', 'm', '1985-07-09', '6\'06\"', 'lindsay10@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('8589d751-e49b-4d83-b5e2-b0277cffe40f', 'Becky', 'Carr', 'f', '1985-07-09', '4\'04\"', 'nicholasball@clark-harris.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('85e54eb2-0375-4dfe-a86d-3b54a2a19bc5', 'Mary', 'King', 'f', '1985-07-09', '6\'07\"', 'nelsonfrank@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('86b79148-eaac-4061-aa08-0ca7633149b8', 'Michelle', 'Snow', 'f', '1985-07-09', '6\'03\"', 'holmestodd@smith.net', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('88efa3de-bf25-417c-ab7c-e2bfae80502a', 'Christopher', 'Murphy', 'm', '1985-07-09', '6\'07\"', 'julie63@rogers.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('8a5a1ab0-6237-409a-8b70-82e39b852e8f', 'Jason', 'Walker', 'm', '1985-07-09', '4\'10\"', 'mjohnson@ortiz.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('8fd95f14-c885-4f81-a0a6-9d7ecd9762f9', 'fsafasf2f2', 'f2f2f2f2', 'm', '2025-07-08', '0', 'fsafaf2f2safa@gmail.com', '$2y$10$YXgPqgo6e7ksgxfRfLzw0OggVqIqFcDgMsZ.w4BMRLkqf.jdbDy52', '(+58) 2525446464', 'US', 'America/Los_Angeles', 1, '2025-07-08 05:52:00', '8fd95f14-c885-4f81-a0a6-9d7ecd9762f9', NULL, NULL, '2025-07-08 05:58:36', '3a3963c7-a08e-44b9-9a89-7081a04b2c42'),
('9107c06a-e1ea-4e9a-8fb2-2ac2f112f51f', 'Michelle', 'Ochoa', 'f', '1985-07-09', '5\'10\"', 'jimmymadden@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('92222f9d-d2a0-4c66-8113-022e4af8a99a', 'Elizabeth', 'King', 'f', '1985-07-09', '6\'00\"', 'teresa81@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('9e19e272-d2fa-4ed8-90b0-efb758f8c5ce', 'Danielle', 'Miller', 'f', '1985-07-09', '4\'07\"', 'jhunter@forbes.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('a0148571-340f-4f7e-8fad-787f587b63ca', 'Moises', 'Celis', 'm', '2000-02-07', '5\'07\"', 'holiwis@gmail.com', '$2y$12$3tTveMbxM4INdZQW3RYsM.DzNg2rTP/F.aOesKzH.c4N64l/.iibW', '(+58) 4249173468', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('a265369b-a8cf-449f-a8f2-fd2f2575f12b', 'Jessica', 'Ray', 'f', '1985-07-09', '4\'11\"', 'montgomerykatie@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('a6fa120d-61c7-4c36-af2e-5ad97996c5a3', 'Ellen', 'Woods', 'f', '1985-07-09', '4\'01\"', 'belinda23@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('a9db0294-b75c-4f4f-a27d-fdb818e80265', 'Craig', 'Sanchez', 'm', '1985-07-09', '4\'02\"', 'lkline@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('acfa725e-561d-4911-afeb-0f35b7323b6d', 'Alejandro', 'S', 'm', '1990-05-16', '05\'07\"', 'marcelrojas@hotmail.es', '$2y$12$Xs91ga0pQgJk5ZrJn8flx.4ngNkBNeKZuZKBi.TOUkQUiYptwwKjq', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('affbc36f-8892-4b5f-8bc7-1467b00f9e90', 'Michael', 'Richmond', 'm', '1985-07-09', '4\'05\"', 'patelheather@jones-houston.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('b224a054-521e-4c36-b336-61fbdafebdb5', 'Edward', 'Myers', 'm', '1985-07-09', '4\'04\"', 'timothy80@white.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('b88bf242-b659-441f-bfff-4ae202569d29', 'Kimberly', 'Smith', 'f', '1985-07-09', '4\'00\"', 'qjordan@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('ba96a841-2ac8-4052-ac2a-71dc159cecc2', 'Ian', 'Howard', 'm', '1985-07-09', '4\'08\"', 'belljasmine@weaver.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('bf8c7301-a811-4801-a181-ab5559c8cde1', 'Christina', 'Rodriguez', 'f', '1985-07-09', '6\'08\"', 'joe43@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('bfb1f1cd-b387-462b-ad09-648a2e138268', 'Breanna', 'Hodge', 'f', '1985-07-09', '5\'03\"', 'jordanarcher@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('c03502af-2092-452c-83a9-c8edb55890ec', 'Ashley', 'Parsons', 'f', '1985-07-09', '6\'01\"', 'harrisstephanie@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('c4e80077-f10e-4051-a267-6313de1e9128', 'Elaine', 'Trevino', 'f', '1985-07-09', '6\'09\"', 'schneidervirginia@thompson-ferguson.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('d2b645c0-a593-4b4e-a3a1-44851d5668f8', 'Nicole', 'Wilson', 'f', '1985-07-09', '4\'01\"', 'nathan57@bennett-newman.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('d3aa1ffb-7dd6-4397-a1ae-38798890a585', 'Alejandro', 'Rojas', 'm', '1985-07-09', '5\'07\"', 'marcel85rs@gmail.com', '$2y$12$Yx7lYM/QOY6pknRZha72F.maEDVDXJkFtbKJ4G.WHVX0Sw2ohs7j.', '(+1) (626)423-8682', 'US', 'America/Los_Angeles', 1, NULL, NULL, '2025-09-03 18:43:43', 'd3aa1ffb-7dd6-4397-a1ae-38798890a585', NULL, NULL),
('db54c96c-1024-4576-9e72-769cea613f68', 'Eric', 'Lawrence', 'm', '1985-07-09', '4\'01\"', 'cmichael@bates-reeves.info', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('dff19db7-367f-449a-86c4-8a33f7e49062', 'Tonya', 'Thompson', 'f', '1985-07-09', '4\'02\"', 'stephanielee@rodgers-barnett.org', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('dff775cf-a2dc-45ce-aa50-cfad1f5a077e', 'Alex', 'Sanders', 'm', '1985-07-09', '4\'04\"', 'matthew28@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('e3357e12-7a73-49c3-b51f-6dfe34151fb5', 'Moises Francisco', 'Celis Salazar', 'm', '2000-02-07', '5\'08\"', 'moisescelis21@gmail.com', '$2y$12$82uhupzQEM5874.WgWSYr.JuRDjn05WdDNgpoeP0wQ1NQlPWkmTcS', '(+58) 4249173469', 'US', 'America/Los_Angeles', 1, '2025-09-12 15:34:45', 'e3357e12-7a73-49c3-b51f-6dfe34151fb5', '2025-09-15 09:59:13', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('eda0164b-fa36-4748-8ee5-f8138988d17e', 'Kelly', 'Bates', 'f', '1985-07-09', '5\'00\"', 'pamela81@hotmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('f540d82a-776b-48fd-a5da-2914227f6dfc', 'Samantha', 'Cortez', 'f', '1985-07-09', '5\'07\"', 'sotobrent@gutierrez.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) (626)423-8682', 'US', 'America/Los_Angeles', 1, NULL, NULL, '2025-06-18 16:32:14', '1', NULL, NULL),
('f5b62915-5fdc-4191-8e27-50fb568922b6', 'Kerry', 'Wyatt', 'f', '1985-07-09', '5\'10\"', 'jonathan06@yahoo.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('feaf882f-7acf-494d-9ca6-d0a23eeaa872', 'Lisa', 'Kim', 'f', '1985-07-09', '6\'04\"', 'david73@turner-richardson.biz', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL),
('ff9f4ffe-a637-47bf-b247-71392a50ad29', 'Betty', 'Cisneros', 'f', '1985-07-09', '4\'09\"', 'bgomez@jennings.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'US', 'America/Los_Angeles', 1, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `users`
--
DELIMITER $$
CREATE TRIGGER `trg_users_delete` BEFORE DELETE ON `users` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'users', OLD.user_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'user_id', OLD.user_id,
      'first_name', OLD.first_name,
      'last_name', OLD.last_name,
      'sex', OLD.sex,
      'birthday', OLD.birthday,
      'height', OLD.height,
      'email', OLD.email,
      'password', OLD.password,
      'telephone', OLD.telephone,
      'system_type', OLD.system_type,
      'timezone', OLD.timezone,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_delete_logical` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'users', OLD.user_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'user_id', OLD.user_id,
        'first_name', OLD.first_name,
        'last_name', OLD.last_name,
        'sex', OLD.sex,
        'birthday', OLD.birthday,
        'height', OLD.height,
        'email', OLD.email,
        'password', OLD.password,
        'telephone', OLD.telephone,
        'system_type', OLD.system_type,
        'timezone', OLD.timezone,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'users', NEW.user_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'user_id', NEW.user_id,
      'first_name', NEW.first_name,
      'last_name', NEW.last_name,
      'sex', NEW.sex,
      'birthday', NEW.birthday,
      'height', NEW.height,
      'email', NEW.email,
      'password', NEW.password,
      'telephone', NEW.telephone,
      'system_type', NEW.system_type,
      'timezone', NEW.timezone,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_users_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.first_name <> NEW.first_name THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"first_name":{"old":"', escape_json(OLD.first_name), '","new":"', escape_json(NEW.first_name), '"}'
    );
  END IF;

  IF OLD.last_name <> NEW.last_name THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"last_name":{"old":"', escape_json(OLD.last_name), '","new":"', escape_json(NEW.last_name), '"}'
    );
  END IF;

  IF OLD.sex <> NEW.sex THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"sex":{"old":"', escape_json(OLD.sex), '","new":"', escape_json(NEW.sex), '"}'
    );
  END IF;

  IF OLD.birthday <> NEW.birthday THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"birthday":{"old":"', escape_json(OLD.birthday), '","new":"', escape_json(NEW.birthday), '"}'
    );
  END IF;

  IF OLD.height <> NEW.height THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"height":{"old":"', escape_json(OLD.height), '","new":"', escape_json(NEW.height), '"}'
    );
  END IF;

  IF OLD.email <> NEW.email THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"email":{"old":"', escape_json(OLD.email), '","new":"', escape_json(NEW.email), '"}'
    );
  END IF;

  IF OLD.telephone <> NEW.telephone THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"telephone":{"old":"', escape_json(OLD.telephone), '","new":"', escape_json(NEW.telephone), '"}'
    );
  END IF;

  IF OLD.system_type <> NEW.system_type THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"system_type":{"old":"', escape_json(OLD.system_type), '","new":"', escape_json(NEW.system_type), '"}'
    );
  END IF;

  IF OLD.timezone <> NEW.timezone THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"timezone":{"old":"', escape_json(OLD.timezone), '","new":"', escape_json(NEW.timezone), '"}'
    );
  END IF;

  -- (opcional) updated_at / updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'users', OLD.user_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video_calls`
--

CREATE TABLE `video_calls` (
  `video_call_id` char(36) NOT NULL,
  `request_id` char(36) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 30,
  `meeting_url` varchar(255) DEFAULT NULL,
  `meeting_token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Disparadores `video_calls`
--
DELIMITER $$
CREATE TRIGGER `trg_video_calls_delete` BEFORE DELETE ON `video_calls` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'video_calls', OLD.video_call_id, 'DELETE_PHYSICAL', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'video_call_id', OLD.video_call_id,
      'request_id', OLD.request_id,
      'scheduled_at', OLD.scheduled_at,
      'duration_minutes', OLD.duration_minutes,
      'meeting_url', OLD.meeting_url,
      'meeting_token', OLD.meeting_token,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'updated_at', OLD.updated_at,
      'updated_by', OLD.updated_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_video_calls_delete_logical` AFTER UPDATE ON `video_calls` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'video_calls', OLD.video_call_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'video_call_id', OLD.video_call_id,
        'request_id', OLD.request_id,
        'scheduled_at', OLD.scheduled_at,
        'duration_minutes', OLD.duration_minutes,
        'meeting_url', OLD.meeting_url,
        'meeting_token', OLD.meeting_token,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_video_calls_insert` AFTER INSERT ON `video_calls` FOR EACH ROW BEGIN
  -- Defaults seguros
  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'video_calls', NEW.video_call_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'video_call_id', NEW.video_call_id,
      'request_id', NEW.request_id,
      'scheduled_at', NEW.scheduled_at,
      'duration_minutes', NEW.duration_minutes,
      'meeting_url', NEW.meeting_url,
      'meeting_token', NEW.meeting_token,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    v_client_ip, v_client_hostname, v_user_agent,
    v_client_os, v_client_browser,
    v_domain_name, v_request_uri, v_server_hostname,
    v_client_country, v_client_region, v_client_city,
    v_client_zipcode, v_client_coordinates,
    v_geo_ip_timestamp, v_geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_video_calls_update` AFTER UPDATE ON `video_calls` FOR EACH ROW BEGIN
  -- Todas las DECLARE al inicio
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)           DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)       DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)        DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64)  DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)        DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT              DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)        DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)   DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)     DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)     DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)   DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)   DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)   DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)   DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME    DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  -- JSON de cambios (usar escape_json en todos los valores)
  IF OLD.request_id <> NEW.request_id THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"request_id":{"old":"', escape_json(OLD.request_id), '","new":"', escape_json(NEW.request_id), '"}'
    );
  END IF;

  IF OLD.scheduled_at <> NEW.scheduled_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"scheduled_at":{"old":"', escape_json(OLD.scheduled_at), '","new":"', escape_json(NEW.scheduled_at), '"}'
    );
  END IF;

  IF OLD.duration_minutes <> NEW.duration_minutes THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"duration_minutes":{"old":"', escape_json(OLD.duration_minutes), '","new":"', escape_json(NEW.duration_minutes), '"}'
    );
  END IF;

  IF OLD.meeting_url <> NEW.meeting_url THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"meeting_url":{"old":"', escape_json(OLD.meeting_url), '","new":"', escape_json(NEW.meeting_url), '"}'
    );
  END IF;

  IF OLD.meeting_token <> NEW.meeting_token THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"meeting_token":{"old":"', escape_json(OLD.meeting_token), '","new":"', escape_json(NEW.meeting_token), '"}'
    );
  END IF;

  -- (opcional) updated_at / updated_by si cambian
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'
    );
  END IF;

  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'
    );
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
      geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'video_calls', OLD.video_call_id, 'UPDATE', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      change_data, NULL,
      v_client_ip, v_client_hostname, v_user_agent,
      v_client_os, v_client_browser,
      v_domain_name, v_request_uri, v_server_hostname,
      v_client_country, v_client_region, v_client_city,
      v_client_zipcode, v_client_coordinates,
      v_geo_ip_timestamp, v_geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`administrator_id`);

--
-- Indices de la tabla `admin_commissions`
--
ALTER TABLE `admin_commissions`
  ADD PRIMARY KEY (`admin_commission_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indices de la tabla `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`backup_id`);

--
-- Indices de la tabla `biomarkers`
--
ALTER TABLE `biomarkers`
  ADD PRIMARY KEY (`biomarker_id`),
  ADD KEY `idx_panel_name` (`panel_id`,`name`),
  ADD KEY `idx_panel_id` (`panel_id`);

--
-- Indices de la tabla `body_composition`
--
ALTER TABLE `body_composition`
  ADD PRIMARY KEY (`body_composition_id`),
  ADD KEY `idx_user_date` (`user_id`,`composition_date`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `idx_cities_state` (`state_id`),
  ADD KEY `idx_cities_country` (`country_id`),
  ADD KEY `idx_cities_name` (`city_name`);

--
-- Indices de la tabla `comment_biomarker`
--
ALTER TABLE `comment_biomarker`
  ADD PRIMARY KEY (`comment_biomarker_id`),
  ADD KEY `idx_id_test_panel` (`id_test_panel`),
  ADD KEY `idx_id_biomarker` (`id_biomarker`),
  ADD KEY `idx_id_specialist` (`id_specialist`);

--
-- Indices de la tabla `contact_emails`
--
ALTER TABLE `contact_emails`
  ADD PRIMARY KEY (`contact_email_id`),
  ADD UNIQUE KEY `uk_email_entity` (`entity_type`,`entity_id`,`email`),
  ADD KEY `ix_email_lookup` (`email`);

--
-- Indices de la tabla `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD PRIMARY KEY (`contact_phone_id`),
  ADD KEY `ix_phone_entity` (`entity_type`,`entity_id`),
  ADD KEY `ix_phone_lookup` (`phone_number`);

--
-- Indices de la tabla `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indices de la tabla `energy_metabolism`
--
ALTER TABLE `energy_metabolism`
  ADD PRIMARY KEY (`energy_metabolism_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_user_energy` (`user_id`,`energy_date`);

--
-- Indices de la tabla `lipid_profile_record`
--
ALTER TABLE `lipid_profile_record`
  ADD PRIMARY KEY (`lipid_profile_record_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_user_date` (`user_id`,`lipid_profile_date`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_id_panel` (`id_panel`),
  ADD KEY `idx_id_biomarker` (`id_biomarker`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `renal_function`
--
ALTER TABLE `renal_function`
  ADD PRIMARY KEY (`renal_function_id`),
  ADD KEY `fk_user_renal` (`user_id`),
  ADD KEY `idx_user_date` (`user_id`,`renal_date`);

--
-- Indices de la tabla `second_opinion_data`
--
ALTER TABLE `second_opinion_data`
  ADD PRIMARY KEY (`second_opinion_data_id`),
  ADD KEY `fk_second_opinion_data_request` (`second_opinion_id`),
  ADD KEY `second_opinion_data_id` (`second_opinion_data_id`);

--
-- Indices de la tabla `second_opinion_requests`
--
ALTER TABLE `second_opinion_requests`
  ADD PRIMARY KEY (`second_opinion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `security_questions`
--
ALTER TABLE `security_questions`
  ADD PRIMARY KEY (`security_question_id`),
  ADD KEY `idx_user_id_user` (`user_id_user`),
  ADD KEY `idx_user_id_admin` (`user_id_admin`),
  ADD KEY `idx_user_id_specialist` (`user_id_specialist`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indices de la tabla `session_config`
--
ALTER TABLE `session_config`
  ADD PRIMARY KEY (`config_id`);

--
-- Indices de la tabla `session_management`
--
ALTER TABLE `session_management`
  ADD PRIMARY KEY (`session_id`);

--
-- Indices de la tabla `specialists`
--
ALTER TABLE `specialists`
  ADD PRIMARY KEY (`specialist_id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_phone` (`phone`),
  ADD KEY `specialty_id` (`specialty_id`),
  ADD KEY `title_id` (`title_id`);

--
-- Indices de la tabla `specialists_titles`
--
ALTER TABLE `specialists_titles`
  ADD PRIMARY KEY (`title_id`),
  ADD UNIQUE KEY `name_en` (`name_en`),
  ADD UNIQUE KEY `name_es` (`name_es`);

--
-- Indices de la tabla `specialist_availability`
--
ALTER TABLE `specialist_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `specialist_certifications`
--
ALTER TABLE `specialist_certifications`
  ADD PRIMARY KEY (`certification_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `specialist_locations`
--
ALTER TABLE `specialist_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `specialist_id` (`specialist_id`),
  ADD KEY `idx_spec_loc_state` (`state_id`),
  ADD KEY `idx_spec_loc_city` (`city_id`),
  ADD KEY `idx_spec_loc_country` (`country_id`) USING BTREE;

--
-- Indices de la tabla `specialist_pricing`
--
ALTER TABLE `specialist_pricing`
  ADD PRIMARY KEY (`pricing_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `specialist_reviews`
--
ALTER TABLE `specialist_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `specialist_id` (`specialist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `specialist_social_links`
--
ALTER TABLE `specialist_social_links`
  ADD PRIMARY KEY (`social_link_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `specialist_verification_requests`
--
ALTER TABLE `specialist_verification_requests`
  ADD PRIMARY KEY (`verification_request_id`),
  ADD KEY `specialist_id` (`specialist_id`);

--
-- Indices de la tabla `specialty`
--
ALTER TABLE `specialty`
  ADD PRIMARY KEY (`specialty_id`),
  ADD UNIQUE KEY `name_en` (`name_en`),
  ADD UNIQUE KEY `name_es` (`name_es`);

--
-- Indices de la tabla `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`),
  ADD UNIQUE KEY `uk_states_country_iso3166_2` (`country_id`,`iso3166_2`),
  ADD KEY `idx_states_country` (`country_id`),
  ADD KEY `idx_states_name` (`state_name`);

--
-- Indices de la tabla `test_documents`
--
ALTER TABLE `test_documents`
  ADD PRIMARY KEY (`test_documents_id`),
  ADD KEY `idx_id_test_panel` (`id_test_panel`);

--
-- Indices de la tabla `test_panels`
--
ALTER TABLE `test_panels`
  ADD PRIMARY KEY (`panel_id`);

--
-- Indices de la tabla `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `specialist_id` (`specialist_id`),
  ADD KEY `pricing_id` (`pricing_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indices de la tabla `video_calls`
--
ALTER TABLE `video_calls`
  ADD PRIMARY KEY (`video_call_id`),
  ADD KEY `request_id` (`request_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `session_config`
--
ALTER TABLE `session_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `admin_commissions`
--
ALTER TABLE `admin_commissions`
  ADD CONSTRAINT `admin_commissions_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `biomarkers`
--
ALTER TABLE `biomarkers`
  ADD CONSTRAINT `fk_biomarkers_test_panels` FOREIGN KEY (`panel_id`) REFERENCES `test_panels` (`panel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `body_composition`
--
ALTER TABLE `body_composition`
  ADD CONSTRAINT `fk_body_composition_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `fk_cities_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cities_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`state_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `comment_biomarker`
--
ALTER TABLE `comment_biomarker`
  ADD CONSTRAINT `fk_cb_biomarker` FOREIGN KEY (`id_biomarker`) REFERENCES `biomarkers` (`biomarker_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cb_specialist` FOREIGN KEY (`id_specialist`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cb_test_panel` FOREIGN KEY (`id_test_panel`) REFERENCES `test_panels` (`panel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comment_biomarker_specialist` FOREIGN KEY (`id_specialist`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `energy_metabolism`
--
ALTER TABLE `energy_metabolism`
  ADD CONSTRAINT `energy_metabolism_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lipid_profile_record`
--
ALTER TABLE `lipid_profile_record`
  ADD CONSTRAINT `lipid_profile_record_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_biomarker` FOREIGN KEY (`id_biomarker`) REFERENCES `biomarkers` (`biomarker_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_test_panel` FOREIGN KEY (`id_panel`) REFERENCES `test_panels` (`panel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `renal_function`
--
ALTER TABLE `renal_function`
  ADD CONSTRAINT `fk_renal_function_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `second_opinion_data`
--
ALTER TABLE `second_opinion_data`
  ADD CONSTRAINT `fk_second_opinion_data_request` FOREIGN KEY (`second_opinion_id`) REFERENCES `second_opinion_requests` (`second_opinion_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `second_opinion_requests`
--
ALTER TABLE `second_opinion_requests`
  ADD CONSTRAINT `fk_second_opinion_requests_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `security_questions`
--
ALTER TABLE `security_questions`
  ADD CONSTRAINT `fk_sq_admin` FOREIGN KEY (`user_id_admin`) REFERENCES `administrators` (`administrator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sq_specialist` FOREIGN KEY (`user_id_specialist`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sq_user` FOREIGN KEY (`user_id_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialists`
--
ALTER TABLE `specialists`
  ADD CONSTRAINT `fk_specialists_specialty` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`specialty_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_specialists_title` FOREIGN KEY (`title_id`) REFERENCES `specialists_titles` (`title_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_availability`
--
ALTER TABLE `specialist_availability`
  ADD CONSTRAINT `fk_specialist_availability_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_certifications`
--
ALTER TABLE `specialist_certifications`
  ADD CONSTRAINT `fk_specialist_certifications_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_locations`
--
ALTER TABLE `specialist_locations`
  ADD CONSTRAINT `fk_spec_loc_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spec_loc_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spec_loc_state` FOREIGN KEY (`state_id`) REFERENCES `states` (`state_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_specialist_locations_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_pricing`
--
ALTER TABLE `specialist_pricing`
  ADD CONSTRAINT `fk_specialist_pricing_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_reviews`
--
ALTER TABLE `specialist_reviews`
  ADD CONSTRAINT `fk_specialist_reviews_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_specialist_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_social_links`
--
ALTER TABLE `specialist_social_links`
  ADD CONSTRAINT `fk_specialist_social_links_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `specialist_verification_requests`
--
ALTER TABLE `specialist_verification_requests`
  ADD CONSTRAINT `fk_specialist_verification_requests_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `fk_states_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `test_documents`
--
ALTER TABLE `test_documents`
  ADD CONSTRAINT `fk_test_documents_test_panel` FOREIGN KEY (`id_test_panel`) REFERENCES `test_panels` (`panel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`pricing_id`) REFERENCES `specialist_pricing` (`pricing_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `video_calls`
--
ALTER TABLE `video_calls`
  ADD CONSTRAINT `fk_video_calls_request` FOREIGN KEY (`request_id`) REFERENCES `second_opinion_requests` (`second_opinion_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
