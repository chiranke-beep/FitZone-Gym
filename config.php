<?php
function getDB() {
    $host = 'localhost';
    $dbname = 'fitzone';
    $username = 'root';
    $password = '';
    try {
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        die("Database connection error: " . $e->getMessage());
    }
}
?>