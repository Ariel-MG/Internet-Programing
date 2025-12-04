<?php
// Script para limpiar HTML de las descripciones de productos
require_once 'config/db.php';

echo "<h1>Limpieza de HTML en Descripciones</h1>";

// Obtener todos los productos
$sql = "SELECT id_producto, nombre, descripcion FROM productos";
$result = $conn->query($sql);

$total = 0;
$actualizados = 0;

echo "<table border='1' cellpadding='10' style='width: 100%; border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Producto</th><th>Antes</th><th>Después</th><th>Estado</th></tr>";

while ($producto = $result->fetch_assoc()) {
    $total++;
    $descripcion_original = $producto['descripcion'];
    
    // Limpiar HTML
    $descripcion_limpia = strip_tags($descripcion_original);
    
    // Solo actualizar si hay diferencia
    if ($descripcion_original !== $descripcion_limpia) {
        $stmt = $conn->prepare("UPDATE productos SET descripcion = ? WHERE id_producto = ?");
        $stmt->bind_param("si", $descripcion_limpia, $producto['id_producto']);
        
        if ($stmt->execute()) {
            $actualizados++;
            $estado = "<span style='color: green;'>✓ Actualizado</span>";
        } else {
            $estado = "<span style='color: red;'>✗ Error</span>";
        }
        
        echo "<tr>";
        echo "<td>" . $producto['id_producto'] . "</td>";
        echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
        echo "<td><pre>" . htmlspecialchars(substr($descripcion_original, 0, 100)) . "...</pre></td>";
        echo "<td><pre>" . htmlspecialchars(substr($descripcion_limpia, 0, 100)) . "...</pre></td>";
        echo "<td>" . $estado . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<div style='margin-top: 20px; padding: 20px; background: #e8f5e9; border-radius: 5px;'>";
echo "<h2>Resumen</h2>";
echo "<p><strong>Total de productos:</strong> $total</p>";
echo "<p><strong>Productos actualizados:</strong> $actualizados</p>";
echo "<p><strong>Sin cambios:</strong> " . ($total - $actualizados) . "</p>";
echo "</div>";

echo "<hr>";
echo "<p><a href='productos.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ver Catálogo</a></p>";
echo "<p><a href='admin/productos.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Panel Admin</a></p>";
?>
