<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aproximación de Pi</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <div class="container">
        <h1>Aproximación del Número π (Pi)</h1>
        
        <form action="pi.php" method="POST">
            <label for="limite_n">Introduce el número de iteraciones (n):</label>
            <input type="number" id="limite_n" name="limite_n" min="1" required>
            <button type="submit">Calcular Aproximación</button>
        </form>

        <?php
     
        function aproximarPi($n) {
            echo '<h2>Resultados de la aproximación para n = ' . htmlspecialchars($n) . '</h2>';
            echo '<table>';
            echo '<tr><th>Iteración (n)</th><th>Valor Aproximado de π (x)</th></tr>';
            
            $suma = 0.0;
            for ($i = 0; $i <= $n; $i++) {
                $termino = pow(-1, $i) / (2 * $i + 1);
                $suma += $termino;
                
                $aproximacion_actual = 4 * $suma;
                
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $aproximacion_actual . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $n = isset($_POST['limite_n']) ? (int)$_POST['limite_n'] : 0;
            
            if ($n > 0) {
                aproximarPi($n);
            } else {
                echo '<p style="color: red;">Por favor, introduce un número mayor que cero.</p>';
            }
        }
        ?>
    </div>

</body>
</html>