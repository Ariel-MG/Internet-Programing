<?php
/**
 * Prueba de conexión a la base de datos MySQL
 * Credenciales obtenidas del docker-compose.yml
 */

// Configuración de la base de datos
$host = 'db';        // Nombre del servicio en docker-compose
$dbname = 'bd';      // Nombre de la base de datos
$username = 'amg';   // Usuario de la base de datos
$password = 'amg';   // Contraseña de la base de datos
$port = 3306;        // Puerto por defecto de MySQL

echo "<h1>Prueba de Conexión a la Base de Datos</h1>";
echo "<h2>Parámetros de conexión:</h2>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Base de datos:</strong> $dbname</li>";
echo "<li><strong>Usuario:</strong> $username</li>";
echo "<li><strong>Puerto:</strong> $port</li>";
echo "</ul>";

echo "<hr>";

// Método 2: Usando MySQLi (Alternativo)
echo "<h2>Método 2: Conexión con MySQLi</h2>";
$mysqli = new mysqli($host, $username, $password, $dbname, $port);

if ($mysqli->connect_error) {
    echo "<p style='color: red;'>❌ <strong>Error de conexión con MySQLi:</strong> " . $mysqli->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✅ <strong>Conexión exitosa con MySQLi!</strong></p>";
    
    // Obtener información del servidor
    $version = $mysqli->server_info;
    echo "<p><strong>Versión de MySQL:</strong> $version</p>";
    
    // Obtener información del cliente
    $client_info = $mysqli->client_info;
    echo "<p><strong>Versión del cliente MySQL:</strong> $client_info</p>";
    
    $mysqli->close(); // Cerrar conexión
}

echo "<hr>";


?>
