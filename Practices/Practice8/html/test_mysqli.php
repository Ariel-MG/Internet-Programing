<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión MySQLi</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Prueba de Conexión MySQLi</h1>
        </nav>
    </header>

    <main>
        <section class="seccion-contenido">
            <h2>Verificación de Conexión con MySQLi</h2>
            
            <?php
            $host = 'db';
            $dbname = 'bd';
            $username = 'amg';
            $password = 'amg';
            $port = 3306;

            echo "<h3>Parámetros de conexión:</h3>";
            echo "<ul>";
            echo "<li><strong>Host:</strong> $host</li>";
            echo "<li><strong>Base de datos:</strong> $dbname</li>";
            echo "<li><strong>Usuario:</strong> $username</li>";
            echo "<li><strong>Puerto:</strong> $port</li>";
            echo "</ul>";

            $mysqli = new mysqli($host, $username, $password, $dbname, $port);

            if ($mysqli->connect_error) {
                echo '<div class="error-message"> <strong>Error de conexión:</strong> ' . htmlspecialchars($mysqli->connect_error) . '</div>';
                echo '<div class="error-message"><strong>Código de error:</strong> ' . $mysqli->connect_errno . '</div>';
            } else {
                echo '<div class="info-message"> <strong>Conexión exitosa con MySQLi!</strong></div>';
                
                $mysqli->set_charset("utf8mb4");
                echo '<div class="info-message"> Charset establecido a utf8mb4</div>';
                
                $version = $mysqli->server_info;
                echo "<p><strong>Versión de MySQL:</strong> $version</p>";
                
                $client_info = $mysqli->client_info;
                echo "<p><strong>Versión del cliente MySQL:</strong> $client_info</p>";
                
                echo "<h3>Prueba de consulta:</h3>";
                $result = $mysqli->query("SELECT DATABASE() as database_name, NOW() as current_time");
                
                if ($result) {
                    echo '<div class="table-container">';
                    echo '<table>';
                    echo '<thead><tr><th>Base de Datos Actual</th><th>Hora Actual</th></tr></thead>';
                    echo '<tbody>';
                    
                    if ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['database_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['current_time']) . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    
                    $result->free();
                } else {
                    echo '<div class="error-message">Error en consulta de prueba: ' . htmlspecialchars($mysqli->error) . '</div>';
                }
                
                echo "<h3>Verificación de tablas:</h3>";
                $tables_query = "SHOW TABLES";
                $tables_result = $mysqli->query($tables_query);
                
                if ($tables_result && $tables_result->num_rows > 0) {
                    echo '<div class="info-message">Tablas encontradas:</div>';
                    echo '<ul>';
                    while ($table_row = $tables_result->fetch_array()) {
                        echo '<li>' . htmlspecialchars($table_row[0]) . '</li>';
                    }
                    echo '</ul>';
                    $tables_result->free();
                } else {
                    echo '<div class="error-message">No se encontraron tablas o error: ' . htmlspecialchars($mysqli->error) . '</div>';
                    echo '<p><strong>Nota:</strong> Asegúrate de haber importado el archivo init_database.sql</p>';
                }
                
                $mysqli->close();
                echo '<div class="info-message">Conexión cerrada correctamente</div>';
            }
            ?>
        </section>

        <section class="seccion-contenido">
            <h2>Información sobre MySQLi</h2>
            <p><strong>MySQLi (MySQL Improved)</strong> es una extensión de PHP que permite el acceso a bases de datos MySQL.</p>
            
            <h3>Métodos principales utilizados:</h3>
            <ul>
                <li><code>new mysqli()</code> - Constructor para crear conexión</li>
                <li><code>query()</code> - Ejecutar consultas SQL</li>
                <li><code>fetch_assoc()</code> - Obtener fila como array asociativo</li>
                <li><code>fetch_fields()</code> - Obtener información de columnas</li>
                <li><code>free()</code> - Liberar memoria del resultado</li>
                <li><code>close()</code> - Cerrar conexión</li>
            </ul>
        </section>
    </main>
</body>
</html>