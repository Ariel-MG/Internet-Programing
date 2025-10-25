<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice 8 - Consulta de Base de Datos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Practice 8 - Consulta de Base de Datos</h1>
        </nav>
    </header>

    <main>
        <section class="seccion-contenido">
            <h2>Últimas 5 Compras de Autos</h2>
            
            <?php
            $host = 'db';
            $dbname = 'bd';
            $username = 'amg';
            $password = 'amg';
            $port = 3306;

            $mysqli = new mysqli($host, $username, $password, $dbname, $port);

            if ($mysqli->connect_error) {
                echo '<div class="error-message">Error de conexión: ' . htmlspecialchars($mysqli->connect_error) . '</div>';
            } else {
                echo '<div class="info-message">Conexión exitosa a la base de datos</div>';

                $mysqli->set_charset("utf8mb4");

                $sql = "SELECT 
                    c.folio,
                    u.nombre AS cliente,
                    f.nombre AS fabricante,
                    m.nombre AS modelo,
                    m.anio,
                    m.tipo,
                    c.precio_final,
                    DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') AS fecha_compra
                FROM compra c 
                JOIN modelo m ON c.idModelo = m.id 
                JOIN fabricante f ON m.idFab = f.id 
                JOIN usuarios u ON c.idUsuario = u.id 
                ORDER BY c.fecha_compra DESC 
                LIMIT 5";
                
                $result = $mysqli->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo '<div class="table-container">';
                    echo '<table>';
                    echo '<thead>';
                    echo '<tr>';
                    
                    $fields = $result->fetch_fields();
                    foreach ($fields as $field) {
                        echo '<th>' . htmlspecialchars($field->name) . '</th>';
                    }
                    
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    $count = 0;
                    while (($row = $result->fetch_assoc()) && $count < 5) {
                        echo '<tr>';
                        foreach ($row as $value) {
                            echo '<td>' . htmlspecialchars($value) . '</td>';
                        }
                        echo '</tr>';
                        $count++;
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    
                    if ($result->num_rows > 5) {
                        echo '<div class="info-message">Mostrando los primeros 5 de ' . $result->num_rows . ' resultados totales</div>';
                    }
                    
                    $result->free();
                } else {
                    if ($mysqli->error) {
                        echo '<div class="error-message">Error en la consulta: ' . htmlspecialchars($mysqli->error) . '</div>';
                    } else {
                        echo '<div class="info-message">No se encontraron resultados para la consulta</div>';
                    }
                }

                $mysqli->close();
            }
            ?>
        </section>

        <section class="seccion-contenido">
            <h2>Accesos</h2>
            <ul>
                <li>Aplicación web: <a href="http://localhost:8080" target="_blank" style="color: #667eea;">http://localhost:8080</a></li>
                <li>PHPMyAdmin: <a href="http://localhost:8081" target="_blank" style="color: #667eea;">http://localhost:8081</a> (usuario: amg, contraseña: amg)</li>
            </ul>
        </section>
    </main>
</body>
</html>