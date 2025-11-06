<?php
require_once 'config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Consultar la imagen del libro
    $sql = "SELECT imagen FROM libros WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($fila = $resultado->fetch_assoc()) {
            $imagen = $fila['imagen'];
            
            if ($imagen) {
                // Detectar el tipo de imagen
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $tipo_mime = $finfo->buffer($imagen);
                
                // Verificar que sea una imagen válida
                if (strpos($tipo_mime, 'image/') === 0) {
                    // Establecer las cabeceras correctas
                    header('Content-Type: ' . $tipo_mime);
                    header('Content-Length: ' . strlen($imagen));
                    header('Cache-Control: public, max-age=3600'); // Cache por 1 hora
                    
                    // Mostrar la imagen
                    echo $imagen;
                    exit;
                }
            }
        }
        
        $stmt->close();
    }
}

// Si no se encuentra la imagen, mostrar imagen por defecto
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="150" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="#f8f9fa"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="12" fill="#6c757d" text-anchor="middle" dy=".3em">Sin imagen</text>
</svg>';
?>