-- ============================================================
-- Sozo Car Rental - Clean Database Schema
-- Multi-tenant Car Rental Management System
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================
-- Table: admin_users
-- For admin/manager login to the system
-- ============================================================
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL COMMENT 'NULL for superadmin',
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_company` (`company_id`),
  KEY `fk_user_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample admin users (password: password123)
INSERT INTO `admin_users` (`id`, `company_id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `is_superadmin`, `is_active`, `created_at`) VALUES
(1, NULL, 1, 'Super', 'Admin', 'admin@sozo.com', '$2y$10$uC8EQlzwnefMtN/t3amInOSPKOSZqPy.JwLaqMen/hOT9rpCdqAlS', NULL, 1, 1, NOW()),
(2, NULL, 2, 'Company', 'Admin', 'admin@company.com', '$2y$10$6adfKN99R7CU2BJXoBgM9e8ajtmVg/.QdSaQVGGjS/NedMfVkVd1e', '12345678', 0, 1, NOW());

-- ============================================================
-- Table: roles
-- User roles (Super Admin, Admin, Manager)
-- ============================================================
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_slug` varchar(50) NOT NULL,
  `role_description` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_slug` (`role_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `role_name`, `role_slug`, `role_description`, `created_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full access to all features and companies', NOW()),
(2, 'Admin', 'admin', 'Company admin with full access to their company', NOW()),
(3, 'Manager', 'manager', 'Limited access to manage vehicles and customers', NOW());

-- ============================================================
-- Table: permission_groups
-- Groups for organizing permissions
-- ============================================================
CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perm_group_name` varchar(100) NOT NULL,
  `perm_group_slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `perm_group_slug` (`perm_group_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permission_groups` (`id`, `perm_group_name`, `perm_group_slug`, `created_at`) VALUES
(1, 'Dashboard', 'dashboard', NOW()),
(2, 'Users', 'users', NOW()),
(3, 'Roles', 'roles', NOW()),
(4, 'Companies', 'companies', NOW()),
(5, 'Settings', 'settings', NOW()),
(7, 'Vehicles', 'vehicles', NOW()),
(8, 'Customers', 'customers', NOW()),
(9, 'Agreement Templates', 'agreement-templates', NOW()),
(10, 'Rental Agreements', 'rental-agreements', NOW());

-- ============================================================
-- Table: permissions
-- Individual permissions for the system
-- ============================================================
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_group_id` int(11) NOT NULL,
  `perm_name` varchar(100) NOT NULL,
  `perm_slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `perm_slug` (`perm_slug`),
  KEY `fk_perm_group` (`permission_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permissions` (`id`, `permission_group_id`, `perm_name`, `perm_slug`, `created_at`) VALUES
-- Dashboard
(1, 1, 'View Dashboard', 'dashboard.view', NOW()),
-- Users
(2, 2, 'View Users', 'users.view', NOW()),
(3, 2, 'Create Users', 'users.create', NOW()),
(4, 2, 'Edit Users', 'users.edit', NOW()),
(5, 2, 'Delete Users', 'users.delete', NOW()),
-- Roles
(6, 3, 'View Roles', 'roles.view', NOW()),
(7, 3, 'Create Roles', 'roles.create', NOW()),
(8, 3, 'Edit Roles', 'roles.edit', NOW()),
(9, 3, 'Delete Roles', 'roles.delete', NOW()),
-- Companies
(10, 4, 'View Companies', 'companies.view', NOW()),
(11, 4, 'Create Companies', 'companies.create', NOW()),
(12, 4, 'Edit Companies', 'companies.edit', NOW()),
(13, 4, 'Delete Companies', 'companies.delete', NOW()),
-- Settings
(14, 5, 'View Settings', 'settings.view', NOW()),
(15, 5, 'Edit Settings', 'settings.edit', NOW()),
-- Vehicles
(17, 7, 'View Vehicles', 'vehicles.view', NOW()),
(18, 7, 'Create Vehicles', 'vehicles.create', NOW()),
(19, 7, 'Edit Vehicles', 'vehicles.edit', NOW()),
(20, 7, 'Delete Vehicles', 'vehicles.delete', NOW()),
(21, 7, 'Import Vehicles', 'vehicles.import', NOW()),
(22, 7, 'Export Vehicles', 'vehicles.export', NOW()),
-- Customers
(23, 8, 'View Customers', 'customers.view', NOW()),
(24, 8, 'Create Customers', 'customers.create', NOW()),
(25, 8, 'Edit Customers', 'customers.edit', NOW()),
(26, 8, 'Delete Customers', 'customers.delete', NOW()),
-- Agreement Templates
(27, 9, 'View Agreement Templates', 'agreement-templates.view', NOW()),
(28, 9, 'Create Agreement Templates', 'agreement-templates.create', NOW()),
(29, 9, 'Edit Agreement Templates', 'agreement-templates.edit', NOW()),
(30, 9, 'Delete Agreement Templates', 'agreement-templates.delete', NOW()),
-- Rental Agreements
(31, 10, 'View Rental Agreements', 'rental-agreements.view', NOW()),
(32, 10, 'Create Rental Agreements', 'rental-agreements.create', NOW()),
(33, 10, 'Approve Rental Agreements', 'rental-agreements.approve', NOW()),
(34, 10, 'Reject Rental Agreements', 'rental-agreements.reject', NOW()),
(35, 10, 'Delete Rental Agreements', 'rental-agreements.delete', NOW());

-- ============================================================
-- Table: role_permissions
-- Links roles to permissions
-- ============================================================
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_perm` (`role_id`,`permission_id`),
  KEY `fk_rp_perm` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Super Admin gets all permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions;

-- Admin gets most permissions (except company delete for now)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM permissions WHERE perm_slug NOT IN ('companies.delete', 'settings.view', 'settings.edit');

-- Manager gets limited permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1),   -- Dashboard
(3, 17),  -- View Vehicles
(3, 18),  -- Create Vehicles
(3, 19),  -- Edit Vehicles
(3, 20),  -- Delete Vehicles
(3, 21),  -- Import Vehicles
(3, 22),  -- Export Vehicles
(3, 23),  -- View Customers
(3, 24),  -- Create Customers
(3, 25),  -- Edit Customers
(3, 26),  -- Delete Customers
(3, 27),  -- View Agreement Templates
(3, 28),  -- Create Agreement Templates
(3, 29),  -- Edit Agreement Templates
(3, 31),  -- View Rental Agreements
(3, 32);  -- Create Rental Agreements


-- ============================================================
-- Foreign Key Constraints
-- ============================================================
ALTER TABLE `admin_users`
  ADD CONSTRAINT `fk_user_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);


ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_perm_group` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE;


ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;


COMMIT;
