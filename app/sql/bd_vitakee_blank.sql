-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2026 a las 16:06:30
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
-- Base de datos: `bd_vitakee_developer`
--

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
-- Disparadores `administrators`
--
DELIMITER $$
CREATE TRIGGER `trg_administrators_delete` BEFORE DELETE ON `administrators` FOR EACH ROW BEGIN
  
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
-- Disparadores `backups`
--
DELIMITER $$
CREATE TRIGGER `trg_backups_delete` BEFORE DELETE ON `backups` FOR EACH ROW BEGIN
  
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

  
  IF NOT (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) THEN
    
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
  `description` longtext NOT NULL,
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
-- Disparadores `biomarkers`
--
DELIMITER $$
CREATE TRIGGER `trg_biomarkers_delete` BEFORE DELETE ON `biomarkers` FOR EACH ROW BEGIN
  
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

  
  IF OLD.updated_at <> NEW.updated_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}');
  END IF;
  IF OLD.updated_by <> NEW.updated_by THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}');
  END IF;

  
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
-- Disparadores `comment_biomarker`
--
DELIMITER $$
CREATE TRIGGER `trg_comment_biomarker_delete` BEFORE DELETE ON `comment_biomarker` FOR EACH ROW BEGIN
  
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
-- Disparadores `countries`
--
DELIMITER $$
CREATE TRIGGER `trg_countries_delete` BEFORE DELETE ON `countries` FOR EACH ROW BEGIN
  
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
  `hba1c` decimal(6,2) DEFAULT NULL,
  `hba1c_target` decimal(6,2) DEFAULT NULL,
  `derived_value` decimal(6,2) DEFAULT NULL,
  `derived_unit` varchar(10) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `energy_metabolism`
--
DELIMITER $$
CREATE TRIGGER `trg_energy_metabolism_delete` BEFORE DELETE ON `energy_metabolism` FOR EACH ROW BEGIN
  
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
-- Disparadores `lipid_profile_record`
--
DELIMITER $$
CREATE TRIGGER `trg_lipid_profile_record_delete` BEFORE DELETE ON `lipid_profile_record` FOR EACH ROW BEGIN
  
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
  `notifications_id` char(36) NOT NULL,
  `template_key` varchar(255) NOT NULL,
  `template_params` longtext DEFAULT NULL COMMENT 'JSON object with template variables',
  `route` varchar(255) DEFAULT NULL,
  `module` varchar(255) NOT NULL,
  `rol` varchar(255) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `new` tinyint(1) NOT NULL DEFAULT 1,
  `read_unread` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `renal_function`
--

CREATE TABLE `renal_function` (
  `renal_function_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `renal_date` date NOT NULL,
  `renal_time` time NOT NULL,
  `albumin` enum('N','1+','2+','3+') DEFAULT NULL,
  `creatinine` enum('1+','2+','3+') DEFAULT NULL,
  `urine_result` enum('N','A') GENERATED ALWAYS AS (case when `albumin` is null or `creatinine` is null then NULL when `albumin` = 'N' then 'N' when `albumin` = '1+' then case when `creatinine` = '3+' then 'N' else 'A' end when `albumin` in ('2+','3+') then 'A' else NULL end) VIRTUAL,
  `serum_creatinine` decimal(5,2) DEFAULT NULL,
  `uric_acid_blood` decimal(5,2) DEFAULT NULL,
  `bun_blood` decimal(5,2) DEFAULT NULL,
  `egfr` decimal(6,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `renal_function`
--
DELIMITER $$
CREATE TRIGGER `trg_renal_function_delete` BEFORE DELETE ON `renal_function` FOR EACH ROW BEGIN
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
      'urine_result', OLD.urine_result,
      'serum_creatinine', OLD.serum_creatinine,
      'uric_acid_blood', OLD.uric_acid_blood,
      'bun_blood', OLD.bun_blood,
      'egfr', OLD.egfr,
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
        'urine_result', OLD.urine_result,
        'serum_creatinine', OLD.serum_creatinine,
        'uric_acid_blood', OLD.uric_acid_blood,
        'bun_blood', OLD.bun_blood,
        'egfr', OLD.egfr,
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
      'urine_result', NEW.urine_result,
      'serum_creatinine', NEW.serum_creatinine,
      'uric_acid_blood', NEW.uric_acid_blood,
      'bun_blood', NEW.bun_blood,
      'egfr', NEW.egfr,
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

  
  SET change_data = IF(OLD.user_id <> NEW.user_id,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"user_id":{"old":"', escape_json(OLD.user_id), '","new":"', escape_json(NEW.user_id), '"}'),
    change_data);

  SET change_data = IF(OLD.renal_date <> NEW.renal_date,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"renal_date":{"old":"', escape_json(OLD.renal_date), '","new":"', escape_json(NEW.renal_date), '"}'),
    change_data);

  SET change_data = IF(OLD.renal_time <> NEW.renal_time,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"renal_time":{"old":"', escape_json(OLD.renal_time), '","new":"', escape_json(NEW.renal_time), '"}'),
    change_data);

  SET change_data = IF(OLD.albumin <> NEW.albumin,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"albumin":{"old":"', escape_json(OLD.albumin), '","new":"', escape_json(NEW.albumin), '"}'),
    change_data);

  SET change_data = IF(OLD.creatinine <> NEW.creatinine,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"creatinine":{"old":"', escape_json(OLD.creatinine), '","new":"', escape_json(NEW.creatinine), '"}'),
    change_data);

  SET change_data = IF(OLD.urine_result <> NEW.urine_result,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"urine_result":{"old":"', escape_json(OLD.urine_result), '","new":"', escape_json(NEW.urine_result), '"}'),
    change_data);

  SET change_data = IF(OLD.serum_creatinine <> NEW.serum_creatinine,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"serum_creatinine":{"old":"', escape_json(OLD.serum_creatinine), '","new":"', escape_json(NEW.serum_creatinine), '"}'),
    change_data);

  SET change_data = IF(OLD.uric_acid_blood <> NEW.uric_acid_blood,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"uric_acid_blood":{"old":"', escape_json(OLD.uric_acid_blood), '","new":"', escape_json(NEW.uric_acid_blood), '"}'),
    change_data);

  SET change_data = IF(OLD.bun_blood <> NEW.bun_blood,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"bun_blood":{"old":"', escape_json(OLD.bun_blood), '","new":"', escape_json(NEW.bun_blood), '"}'),
    change_data);

  SET change_data = IF(OLD.egfr <> NEW.egfr,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"egfr":{"old":"', escape_json(OLD.egfr), '","new":"', escape_json(NEW.egfr), '"}'),
    change_data);

  SET change_data = IF(OLD.updated_at <> NEW.updated_at,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_at":{"old":"', escape_json(OLD.updated_at), '","new":"', escape_json(NEW.updated_at), '"}'),
    change_data);

  SET change_data = IF(OLD.updated_by <> NEW.updated_by,
    CONCAT(change_data, IF(change_data = '{', '', ','), '"updated_by":{"old":"', escape_json(OLD.updated_by), '","new":"', escape_json(NEW.updated_by), '"}'),
    change_data);

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
  `pricing_id` char(36) DEFAULT NULL,
  `service_type` enum('CONSULTATION','FOLLOW_UP','REVIEW') DEFAULT NULL COMMENT 'Tipo de servicio del pricing',
  `status` enum('pending','awaiting_payment','upcoming','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `type_request` enum('document_review','appointment_request','block') NOT NULL,
  `scope_request` enum('share_none','share_all','share_custom') DEFAULT NULL,
  `cost_request` decimal(10,2) DEFAULT NULL,
  `duration_request` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reject_message` text DEFAULT NULL,
  `shared_until` datetime DEFAULT NULL,
  `request_date_to` datetime DEFAULT NULL,
  `request_date_end` datetime DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `google_event_id` varchar(255) DEFAULT NULL COMMENT 'ID del evento en Google Calendar',
  `meet_link` text DEFAULT NULL COMMENT 'Link de Google Meet para la videollamada',
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
  
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)         DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255)  DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)         DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)    DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)      DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)      DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255)  DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)    DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)    DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)    DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)    DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME     DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64)  DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

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
CREATE TRIGGER `trg_second_opinion_requests_delete_logical` AFTER UPDATE ON `second_opinion_requests` FOR EACH ROW BEGIN
  
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)         DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255)  DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)         DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)    DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)      DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)      DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255)  DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)    DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)    DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)    DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)    DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME     DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64)  DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

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
CREATE TRIGGER `trg_second_opinion_requests_insert` AFTER INSERT ON `second_opinion_requests` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)         DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255)  DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)         DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)    DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)      DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)      DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255)  DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)    DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)    DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)    DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)    DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME     DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64)  DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

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
CREATE TRIGGER `trg_second_opinion_requests_update` AFTER UPDATE ON `second_opinion_requests` FOR EACH ROW BEGIN
  
  DECLARE change_data TEXT DEFAULT '{';

  DECLARE v_action_by CHAR(36)          DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255)      DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50)       DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);

  DECLARE v_client_ip VARCHAR(64)         DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255)  DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT             DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64)         DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64)    DEFAULT COALESCE(@client_browser, 'phpMyAdmin');

  DECLARE v_domain_name VARCHAR(255)      DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255)      DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255)  DEFAULT COALESCE(@server_hostname, @@hostname);

  DECLARE v_client_country VARCHAR(64)    DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region  VARCHAR(64)    DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city    VARCHAR(64)    DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32)    DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');

  DECLARE v_geo_ip_timestamp DATETIME     DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone  VARCHAR(64)  DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  
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
-- Disparadores `security_questions`
--
DELIMITER $$
CREATE TRIGGER `trg_security_questions_delete` BEFORE DELETE ON `security_questions` FOR EACH ROW BEGIN
  
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
  `birthday_min` int(11) NOT NULL DEFAULT 18 COMMENT 'Minimum age required for user registration',
  `allow_ip_change` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `verified_status` enum('PENDING','AWAITING_PAYMENT','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
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
-- Disparadores `specialists`
--
DELIMITER $$
CREATE TRIGGER `trg_specialists_delete` BEFORE DELETE ON `specialists` FOR EACH ROW BEGIN
  
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
-- Disparadores `specialists_titles`
--
DELIMITER $$
CREATE TRIGGER `trg_specialists_titles_delete` BEFORE DELETE ON `specialists_titles` FOR EACH ROW BEGIN
  
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

  
  IF OLD.name_en <> NEW.name_en THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name_en":{"old":"', escape_json(OLD.name_en), '","new":"', escape_json(NEW.name_en), '"}');
  END IF;
  IF OLD.name_es <> NEW.name_es THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"name_es":{"old":"', escape_json(OLD.name_es), '","new":"', escape_json(NEW.name_es), '"}');
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
  `buffer_time_minutes` varchar(255) DEFAULT NULL,
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
-- Disparadores `specialist_certifications`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_certifications_delete` BEFORE DELETE ON `specialist_certifications` FOR EACH ROW BEGIN
  
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
-- Estructura de tabla para la tabla `specialist_google_tokens`
--

CREATE TABLE `specialist_google_tokens` (
  `token_id` char(36) NOT NULL,
  `specialist_id` char(36) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_type` varchar(50) DEFAULT 'Bearer',
  `expires_at` datetime NOT NULL,
  `scope` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `specialist_google_tokens`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_google_tokens_delete_logical` AFTER UPDATE ON `specialist_google_tokens` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36) DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  
  DECLARE v_client_ip VARCHAR(64) DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64) DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  
  DECLARE v_domain_name VARCHAR(255) DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255) DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64) DEFAULT COALESCE(@client_city, '');
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
      'specialist_google_tokens', OLD.token_id, 'DELETE_LOGICAL', v_action_by,
      v_full_name, v_user_type, NOW(), v_action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'token_id', OLD.token_id,
        'specialist_id', OLD.specialist_id,
        'token_type', OLD.token_type,
        'expires_at', OLD.expires_at,
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
CREATE TRIGGER `trg_specialist_google_tokens_insert` AFTER INSERT ON `specialist_google_tokens` FOR EACH ROW BEGIN
  DECLARE v_action_by CHAR(36) DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  
  DECLARE v_client_ip VARCHAR(64) DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64) DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  
  DECLARE v_domain_name VARCHAR(255) DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255) DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64) DEFAULT COALESCE(@client_city, '');
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
    'specialist_google_tokens', NEW.token_id, 'INSERT', v_action_by,
    v_full_name, v_user_type, NOW(), v_action_timezone,
    NULL,
    JSON_OBJECT(
      'token_id', NEW.token_id,
      'specialist_id', NEW.specialist_id,
      'token_type', NEW.token_type,
      'expires_at', NEW.expires_at,
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
CREATE TRIGGER `trg_specialist_google_tokens_update` AFTER UPDATE ON `specialist_google_tokens` FOR EACH ROW BEGIN
  DECLARE change_data TEXT DEFAULT '{';
  
  DECLARE v_action_by CHAR(36) DEFAULT COALESCE(@user_id, 0);
  DECLARE v_full_name VARCHAR(255) DEFAULT COALESCE(@full_name, 'phpMyAdmin');
  DECLARE v_user_type VARCHAR(50) DEFAULT COALESCE(@user_type, 'system');
  DECLARE v_action_timezone VARCHAR(64) DEFAULT COALESCE(@action_timezone, @@session.time_zone);
  
  DECLARE v_client_ip VARCHAR(64) DEFAULT COALESCE(@client_ip, '127.0.0.1');
  DECLARE v_client_hostname VARCHAR(255) DEFAULT COALESCE(@client_hostname, 'localhost');
  DECLARE v_user_agent TEXT DEFAULT COALESCE(@user_agent, 'phpMyAdmin');
  DECLARE v_client_os VARCHAR(64) DEFAULT COALESCE(@client_os, 'unknown');
  DECLARE v_client_browser VARCHAR(64) DEFAULT COALESCE(@client_browser, 'phpMyAdmin');
  
  DECLARE v_domain_name VARCHAR(255) DEFAULT COALESCE(@domain_name, '');
  DECLARE v_request_uri VARCHAR(255) DEFAULT COALESCE(@request_uri, '');
  DECLARE v_server_hostname VARCHAR(255) DEFAULT COALESCE(@server_hostname, @@hostname);
  
  DECLARE v_client_country VARCHAR(64) DEFAULT COALESCE(@client_country, '');
  DECLARE v_client_region VARCHAR(64) DEFAULT COALESCE(@client_region, '');
  DECLARE v_client_city VARCHAR(64) DEFAULT COALESCE(@client_city, '');
  DECLARE v_client_zipcode VARCHAR(32) DEFAULT COALESCE(@client_zipcode, '');
  DECLARE v_client_coordinates VARCHAR(64) DEFAULT COALESCE(@client_coordinates, '');
  
  DECLARE v_geo_ip_timestamp DATETIME DEFAULT COALESCE(@geo_ip_timestamp, NOW());
  DECLARE v_geo_ip_timezone VARCHAR(64) DEFAULT COALESCE(@geo_ip_timezone, @@session.time_zone);

  IF OLD.is_active <> NEW.is_active THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"is_active":{"old":', OLD.is_active, ',"new":', NEW.is_active, '}');
  END IF;
  
  IF OLD.expires_at <> NEW.expires_at THEN
    SET change_data = CONCAT(change_data, IF(change_data = '{', '', ','), '"expires_at":{"old":"', escape_json(OLD.expires_at), '","new":"', escape_json(NEW.expires_at), '"}');
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
      'specialist_google_tokens', OLD.token_id, 'UPDATE', v_action_by,
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
-- Disparadores `specialist_locations`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_locations_delete` BEFORE DELETE ON `specialist_locations` FOR EACH ROW BEGIN
  
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
  `duration_services` varchar(255) DEFAULT NULL,
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
-- Disparadores `specialist_pricing`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_pricing_delete` BEFORE DELETE ON `specialist_pricing` FOR EACH ROW BEGIN
  
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
  `second_opinion_id` char(36) DEFAULT NULL,
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
-- Disparadores `specialist_reviews`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_reviews_delete` BEFORE DELETE ON `specialist_reviews` FOR EACH ROW BEGIN
  
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
-- Disparadores `specialist_social_links`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_social_links_delete` BEFORE DELETE ON `specialist_social_links` FOR EACH ROW BEGIN
  
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
  `status` enum('PENDING','AWAITING_PAYMENT','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
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
-- Disparadores `specialist_verification_requests`
--
DELIMITER $$
CREATE TRIGGER `trg_specialist_verification_requests_delete` BEFORE DELETE ON `specialist_verification_requests` FOR EACH ROW BEGIN
  
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
-- Disparadores `specialty`
--
DELIMITER $$
CREATE TRIGGER `trg_specialty_delete` BEFORE DELETE ON `specialty` FOR EACH ROW BEGIN
  
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `test_documents`
--

CREATE TABLE `test_documents` (
  `test_documents_id` char(36) NOT NULL,
  `id_test_panel` char(36) NOT NULL,
  `id_test` char(36) NOT NULL,
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
-- Disparadores `test_documents`
--
DELIMITER $$
CREATE TRIGGER `trg_test_documents_delete` BEFORE DELETE ON `test_documents` FOR EACH ROW BEGIN
  
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
    geo_ip_timestamp, geo_ip_timezone
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
-- Disparadores `test_panels`
--
DELIMITER $$
CREATE TRIGGER `trg_test_panels_delete` BEFORE DELETE ON `test_panels` FOR EACH ROW BEGIN
  
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
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `specialist_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pricing_id` char(36) DEFAULT NULL,
  `verification_request_id` char(36) DEFAULT NULL,
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
  `sex_biological` varchar(255) NOT NULL,
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
  `deleted_by` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `users`
--
DELIMITER $$
CREATE TRIGGER `trg_users_delete` BEFORE DELETE ON `users` FOR EACH ROW BEGIN
  
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
      'sex_biological', OLD.sex_biological,
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
        'sex_biological', OLD.sex_biological,
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
      'sex_biological', NEW.sex_biological,
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

  IF OLD.sex_biological <> NEW.sex_biological THEN
    SET change_data = CONCAT(
      change_data, IF(change_data = '{', '', ','),
      '"sex_biological":{"old":"', escape_json(OLD.sex_biological), '","new":"', escape_json(NEW.sex_biological), '"}'
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
  ADD PRIMARY KEY (`notifications_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_module` (`module`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

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
  ADD KEY `specialist_id` (`specialist_id`),
  ADD KEY `idx_sor_pricing_id` (`pricing_id`),
  ADD KEY `idx_google_event_id` (`google_event_id`);

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
-- Indices de la tabla `specialist_google_tokens`
--
ALTER TABLE `specialist_google_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `unique_specialist` (`specialist_id`,`deleted_at`),
  ADD KEY `idx_specialist_google_tokens_active` (`specialist_id`,`is_active`,`deleted_at`);

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_specialist_reviews_second_opinion_id` (`second_opinion_id`);

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
  ADD KEY `pricing_id` (`pricing_id`),
  ADD KEY `fk_transactions_verification_request` (`verification_request_id`);

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
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `session_config`
--
ALTER TABLE `session_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_second_opinion_requests_specialist` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sor_pricing` FOREIGN KEY (`pricing_id`) REFERENCES `specialist_pricing` (`pricing_id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Filtros para la tabla `specialist_google_tokens`
--
ALTER TABLE `specialist_google_tokens`
  ADD CONSTRAINT `specialist_google_tokens_ibfk_1` FOREIGN KEY (`specialist_id`) REFERENCES `specialists` (`specialist_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_transactions_verification_request` FOREIGN KEY (`verification_request_id`) REFERENCES `specialist_verification_requests` (`verification_request_id`) ON DELETE SET NULL ON UPDATE CASCADE,
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
