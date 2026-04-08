-- ============================================================
-- Generic Admin Template - Clean Database Schema
-- Reusable admin panel with RBAC
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
  KEY `fk_user_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample admin users (for admin user password: admin@123 and manager user password: manager@123)
INSERT INTO `admin_users` (`id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `phone`, `is_superadmin`, `is_active`, `created_at`) VALUES
(1, 1, 'Admin', 'User', 'admin@example.com', '$2y$10$YtojJC3YpS7D2IzhNnwVuO3PeCmKRedRWXR/TOo.2KcnvPwF0RPM2', NULL, 1, 1, NOW()),
(2, 2, 'Manager', 'User', 'manager@example.com', '$2y$10$RnNarKvLwY9f3Jt7GW.m6ur428CpXPLvxmKS0JAGPI0eU22u5.mfu', '12345678', 0, 1, NOW());

-- ============================================================
-- Table: roles
-- User roles (Admin, Manager, etc.)
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
(1, 'Admin', 'admin', 'Full access to all features', NOW()),
(2, 'Manager', 'manager', 'Limited access to manage users and settings', NOW());

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
(4, 'Permissions', 'permissions', NOW()),
(5, 'Settings', 'settings', NOW());

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
-- Permissions
(10, 4, 'View Permissions', 'permissions.view', NOW()),
(11, 4, 'Manage Permissions', 'permissions.manage', NOW()),
-- Settings
(12, 5, 'View Settings', 'settings.view', NOW()),
(13, 5, 'Edit Settings', 'settings.edit', NOW());

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

-- Admin gets all permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions;

-- Manager gets limited permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1),   -- Dashboard
(2, 2),   -- View Users
(2, 3),   -- Create Users
(2, 4),   -- Edit Users
(2, 5),   -- Delete Users
(2, 12);  -- View Settings

-- ============================================================
-- Foreign Key Constraints
-- ============================================================
ALTER TABLE `admin_users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_perm_group` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE;

ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

COMMIT;
