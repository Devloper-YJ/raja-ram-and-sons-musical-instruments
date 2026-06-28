<?php
/**
 * Database Connection File
 * This script connects to the Aiven MySQL database using PDO.
 */

// Define the absolute path to the .env file in the project root
$envPath = '/storage/emulated/0/Download/raja-ram-and-sons-musical-instruments/.env';

// Check if .env file exists
if (!file_exists($envPath)) {
    die("ERROR: .env file not found at: " . $envPath);
}

// Parse the .env file into an associative array
$env = parse_ini_file($envPath);

if (!$env) {
    die("ERROR: Failed to parse .env file. Please check the file format.");
}

// Extract variables for easier access
$host = $env['DB_HOST'];
$port = $env['DB_PORT'];
$db   = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];

try {
    // Construct the DSN (Data Source Name) for MySQL
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    
    // Connection options for SSL/Security
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // SSL configuration to bypass invalid response issues
    ];

    // Create the PDO connection instance
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Success message for debugging
    // echo "Successfully connected to the database!";

} catch (PDOException $e) {
    // Handle connection failures and display the error
    die("Connection Failed: " . $e->getMessage());
}
?>
