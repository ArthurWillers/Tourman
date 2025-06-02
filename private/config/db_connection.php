<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

/**
 * Opens a new database connection using mysqli procedural style
 * 
 * @return mysqli Database connection resource
 * @throws Exception if connection fails
 */
function open_connection() {
    global $host, $username, $password, $database;
    
    // Create connection
    $connection = mysqli_connect($host, $username, $password, $database);
    
    // Check connection
    if (!$connection) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset
    mysqli_set_charset($connection, "utf8mb4");
    
    return $connection;
}

/**
 * Closes the database connection
 * 
 * @param mysqli $connection The connection to close
 * @return void
 */
function close_connection(&$connection) {
    if ($connection) {
        mysqli_close($connection);
        $connection = null;
    }
}