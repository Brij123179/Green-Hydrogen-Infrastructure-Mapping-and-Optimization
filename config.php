<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'green_hydrogen_gis');
define('DB_USER', 'root');
define('DB_PASS', '');

// Map configuration
define('DEFAULT_LAT', 37.0902);
define('DEFAULT_LNG', -95.7129);
define('DEFAULT_ZOOM', 4);
define('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4M29iazA2Z2gycXA4N2pmbDZmangifQ.-g_vE53SD2WrJ6tFX7QHmA');

// Application settings
define('MAX_FILE_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['geojson', 'json', 'csv']);

// Error reporting
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Start session
session_start();

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Database connection function
function getDBConnection() {
    static $conn;
    if (!$conn) {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER, 
                DB_PASS
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection error");
        }
    }
    return $conn;
}
?>