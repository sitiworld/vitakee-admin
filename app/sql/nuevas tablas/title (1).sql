-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2025 a las 23:58:49
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `title`
--

CREATE TABLE `title` (
  `id` int(11) NOT NULL,
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
-- Volcado de datos para la tabla `title`
--

INSERT INTO `title` (`id`, `name_en`, `name_es`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'Doctor', 'Doctor', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Nurse', 'Enfermero', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Specialist', 'Especialista', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Physician', 'Médico', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Therapist', 'Terapeuta', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Surgeon', 'Cirujano', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'General Practitioner', 'Médico General', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Pediatrician', 'Pediatra', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Gynecologist', 'Ginecólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Psychologist', 'Psicólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Nutritionist', 'Nutricionista', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Physiotherapist', 'Fisioterapeuta', NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Dentist', 'Dentista', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Cardiologist', 'Cardiólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Neurologist', 'Neurólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Urologist', 'Urólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Radiologist', 'Radiólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Hematologist', 'Hematólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Endocrinologist', 'Endocrinólogo', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Ophthalmologist', 'Oftalmólogo', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `title`
--
DELIMITER $$
CREATE TRIGGER `trg_title_delete` BEFORE DELETE ON `title` FOR EACH ROW BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'title', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'name_en', OLD.name_en,
      'name_es', OLD.name_es
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_title_delete_logical` AFTER UPDATE ON `title` FOR EACH ROW BEGIN
  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'title', OLD.id, 'DELETE_LOGICAL', @user_id,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_title_update` AFTER UPDATE ON `title` FOR EACH ROW BEGIN
  DECLARE change_data TEXT;

  IF OLD.name_en <> NEW.name_en THEN
    SET change_data = JSON_OBJECT(
      'name_en', JSON_OBJECT('old', OLD.name_en, 'new', NEW.name_en)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'title', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.name_es <> NEW.name_es THEN
    SET change_data = JSON_OBJECT(
      'name_es', JSON_OBJECT('old', OLD.name_es, 'new', NEW.name_es)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'title', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `title`
--
ALTER TABLE `title`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_en` (`name_en`),
  ADD UNIQUE KEY `name_es` (`name_es`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `title`
--
ALTER TABLE `title`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
