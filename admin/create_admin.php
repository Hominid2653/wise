<?php
require_once '../config/database.php';

$username = 'admin';
$password = 'WissenAdmin2024'; // This will be your new password

$database = new Database();
$db = $database->getConnection();

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Delete existing admin user first
    $stmt = $db->prepare("DELETE FROM admins WHERE username = ?");
    $stmt->execute([$username]);

    // Insert new admin user
    $stmt = $db->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);

    echo "Admin user created successfully!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage();
} 