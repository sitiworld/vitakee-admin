-- Migración 3.6: Agregar columna de idioma de interfaz

-- Tabla users
ALTER TABLE `users` ADD COLUMN `interface_language` VARCHAR(2) DEFAULT 'EN' AFTER `timezone`;

-- Tabla specialists
ALTER TABLE `specialists` ADD COLUMN `interface_language` VARCHAR(2) DEFAULT 'EN' AFTER `timezone`;

-- Tabla administrators
ALTER TABLE `administrators` ADD COLUMN `interface_language` VARCHAR(2) DEFAULT 'EN' AFTER `timezone`;
