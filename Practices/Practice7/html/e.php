<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aproximación de e</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <div class="container">
        <h1>Aproximación del Número e</h1>

        <form action="e.php" method="POST">
            <label for="limite_n">Introduce el número de iteraciones (n):</label>
            <input type="number" id="limite_n" name="limite_n" min="1" required>
            <button type="submit">Calcular Aproximación</button>
        </form>

        <?php
        function factorial($num) {
            if ($num < 0) {
                return 0; 
            }
            if ($num == 0) {
                return 1;
            }
            $resultado = 1;
            for ($i = 1; $i <= $num; $i++) {
                $resultado *= $i;
            }
            return $resultado;
        }

        function aproximarE($n) {
            echo '<h2>Resultados de la aproximación para n = ' . htmlspecialchars($n) . '</h2>';
            echo '<table>';
            echo '<tr><th>Iteración (n)</th><th>Valor Aproximado de e (x)</th></tr>';
            
            $suma = 0.0;
            
            for ($i = 0; $i <= $n; $i++) {

                $termino = 1 / factorial($i);
                $suma += $termino;
                
                $aproximacion_actual = $suma;
                
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
                if ($n > 170) {
                    echo '<p style="color: orange;">Advertencia: Un número tan grande puede causar un desbordamiento en el cálculo del factorial. Se limitará a 170.</p>';
                    $n = 170;
                }
                aproximarE($n);
            } else {
                echo '<p style="color: red;">Por favor, introduce un número mayor que cero.</p>';
            }
        }
        ?>
    </div>

</body>
</html>