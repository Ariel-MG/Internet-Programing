<?php
$host = 'db';
$user = 'amg';
$password = 'amg';
$database = 'ecomerce';

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    throw new Exception("Error de conexión: " . $e->getMessage());
}
?>
