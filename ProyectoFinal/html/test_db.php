<?php
require_once 'config/db.php';

if ($conn) {
    echo "Conexión exitosa a la base de datos '$database' en el host '$host'.\n";
    echo "Versión del servidor: " . $conn->server_info . "\n";
} else {
    echo "Error en la conexión.\n";
}
$conn->close();
?>
