<?php
// db.php
$host = '127.0.0.1';
$user = 'root'; // Change if different
$pass = '';     // Change if different
$dbname = 'megastar_carnival';

try {
    // First, connect without database to create it if not exists
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // Create admins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `role` ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
        PRIMARY KEY (`id`)
    )");

    // Create donations table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `donations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `area` varchar(100) DEFAULT NULL,
        `country` varchar(100) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
        `date` date NOT NULL,
        `notes` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    )");

    // Create expenses table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `expenses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(150) NOT NULL,
        `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
        `date` date NOT NULL,
        `description` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    )");

    // Create prize sponsors table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `prize_sponsors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `prize_item` varchar(200) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `date` date NOT NULL,
        `notes` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    )");

    // Seed default admin user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = 'MGsecratery'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $admin_pass = password_hash('Megastar2026EidCarnival', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES ('MGsecratery', ?, 'admin')");
        $insert->execute([$admin_pass]);
    }

    // Seed default viewer user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = 'carnivalviewer'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $viewer_pass = password_hash('megastarview', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES ('carnivalviewer', ?, 'viewer')");
        $insert->execute([$viewer_pass]);
    }

} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
?>
