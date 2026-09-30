<?php

// Simple database setup using direct SQL
require __DIR__.'/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$db = $_ENV['DB_DATABASE'] ?? 'clinicflow';

try {
    $pdo = new PDO("mysql:host=$host:$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to database: $db\n\n";
    
    // SQL for creating all tables
    $sql = <<<'SQL'
    
    -- Users table
    CREATE TABLE IF NOT EXISTS `users` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
        `email_verified_at` timestamp NULL,
        `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `role` enum('admin','reception','doctor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reception',
        `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `users_role_index` (`role`),
        KEY `users_created_at_index` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Doctor profiles table
    CREATE TABLE IF NOT EXISTS `doctor_profiles` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` bigint unsigned NOT NULL,
        `specialization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `qualification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `license_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
        `experience_years` int NOT NULL DEFAULT 0,
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        UNIQUE KEY `doctor_profiles_user_id_unique` (`user_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Doctor charges table
    CREATE TABLE IF NOT EXISTS `doctor_charges` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `doctor_id` bigint unsigned NOT NULL,
        `visit_type` enum('consultation','follow-up','emergency') COLLATE utf8mb4_unicode_ci NOT NULL,
        `amount` decimal(8,2) NOT NULL,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `doctor_charges_doctor_id_index` (`doctor_id`),
        FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Patients table
    CREATE TABLE IF NOT EXISTS `patients` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `mrn` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
        `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `email` varchar(255) COLLATE utf8mb4_unicode_ci,
        `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
        `date_of_birth` date,
        `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci,
        `blood_group` varchar(5) COLLATE utf8mb4_unicode_ci,
        `address` text COLLATE utf8mb4_unicode_ci,
        `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        UNIQUE KEY `patients_mrn_unique` (`mrn`),
        KEY `patients_phone_index` (`phone`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Visits table
    CREATE TABLE IF NOT EXISTS `visits` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `patient_id` bigint unsigned NOT NULL,
        `doctor_id` bigint unsigned NOT NULL,
        `visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `visit_type` enum('consultation','follow-up','emergency') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'consultation',
        `chief_complaint` text COLLATE utf8mb4_unicode_ci,
        `diagnosis` text COLLATE utf8mb4_unicode_ci,
        `treatment_notes` text COLLATE utf8mb4_unicode_ci,
        `status` enum('scheduled','in-progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `visits_patient_id_index` (`patient_id`),
        KEY `visits_doctor_id_index` (`doctor_id`),
        KEY `visits_status_index` (`status`),
        FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Medicines table
    CREATE TABLE IF NOT EXISTS `medicines` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `generic_name` varchar(255) COLLATE utf8mb4_unicode_ci,
        `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `strength` varchar(100) COLLATE utf8mb4_unicode_ci,
        `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tablet',
        `description` text COLLATE utf8mb4_unicode_ci,
        `price` decimal(8,2) NOT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        UNIQUE KEY `medicines_name_strength_unique` (`name`,`strength`),
        KEY `medicines_category_index` (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Medicine stock table
    CREATE TABLE IF NOT EXISTS `medicine_stock` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `medicine_id` bigint unsigned NOT NULL,
        `qty` int NOT NULL DEFAULT 0,
        `expiry_date` date,
        `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci,
        `manufactured_date` date,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        UNIQUE KEY `medicine_stock_medicine_id_unique` (`medicine_id`),
        FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Stock transactions table
    CREATE TABLE IF NOT EXISTS `stock_transactions` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `medicine_id` bigint unsigned NOT NULL,
        `transaction_type` enum('inbound','outbound','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
        `quantity` int NOT NULL,
        `reference_id` varchar(255) COLLATE utf8mb4_unicode_ci,
        `notes` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `stock_transactions_medicine_id_index` (`medicine_id`),
        KEY `stock_transactions_created_at_index` (`created_at`),
        FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Prescriptions table
    CREATE TABLE IF NOT EXISTS `prescriptions` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `visit_id` bigint unsigned NOT NULL,
        `notes` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `prescriptions_visit_id_index` (`visit_id`),
        FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Prescription items table
    CREATE TABLE IF NOT EXISTS `prescription_items` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `prescription_id` bigint unsigned NOT NULL,
        `medicine_id` bigint unsigned NOT NULL,
        `quantity` int NOT NULL,
        `frequency` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `duration_days` int NOT NULL,
        `instructions` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `prescription_items_prescription_id_index` (`prescription_id`),
        KEY `prescription_items_medicine_id_index` (`medicine_id`),
        FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Bills table
    CREATE TABLE IF NOT EXISTS `bills` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `patient_id` bigint unsigned NOT NULL,
        `visit_id` bigint unsigned,
        `bill_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
        `total_amount` decimal(10,2) NOT NULL,
        `paid_amount` decimal(10,2) NOT NULL DEFAULT 0,
        `status` enum('draft','issued','paid','partial','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
        `bill_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `due_date` date,
        `notes` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        UNIQUE KEY `bills_bill_number_unique` (`bill_number`),
        KEY `bills_patient_id_index` (`patient_id`),
        KEY `bills_status_index` (`status`),
        FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Bill items table
    CREATE TABLE IF NOT EXISTS `bill_items` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `bill_id` bigint unsigned NOT NULL,
        `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `quantity` int NOT NULL DEFAULT 1,
        `created_at` timestamp NULL,
        `updated_at` timestamp NULL,
        KEY `bill_items_bill_id_index` (`bill_id`),
        FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Notifications table
    CREATE TABLE IF NOT EXISTS `notifications` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `notifiable_id` bigint unsigned NOT NULL,
        `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
        `read_at` timestamp NULL,
        `created_at` timestamp NULL,
        KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
        KEY `notifications_read_at_index` (`read_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Audit logs table
    CREATE TABLE IF NOT EXISTS `audit_logs` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` bigint unsigned,
        `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `model_type` varchar(255) COLLATE utf8mb4_unicode_ci,
        `model_id` bigint unsigned,
        `old_values` longtext COLLATE utf8mb4_unicode_ci,
        `new_values` longtext COLLATE utf8mb4_unicode_ci,
        `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci,
        `user_agent` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL,
        KEY `audit_logs_user_id_index` (`user_id`),
        KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
        KEY `audit_logs_created_at_index` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    -- Migrations table
    CREATE TABLE IF NOT EXISTS `migrations` (
        `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `batch` int NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    SQL;
    
    // Split SQL statements and execute
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $tables_created = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                // Extract table name from CREATE TABLE statement
                if (preg_match('/CREATE TABLE.*?`(\w+)`/i', $statement, $m)) {
                    echo "✓ Table created/verified: {$m[1]}\n";
                    $tables_created++;
                }
            } catch (PDOException $e) {
                // Ignore "already exists" errors
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }
    
    echo "\n✓ Database setup complete! ($tables_created tables)\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
