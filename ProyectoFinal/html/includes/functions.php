<?php
function start_session_safe() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in() {
    start_session_safe();
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function get_user_name() {
    start_session_safe();
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Invitado';
}

// --- Funciones del Carrito ---

function obtener_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT c.id_carrito, c.cantidad, c.precio_unitario, p.nombre, p.imagen, p.id_producto 
            FROM carrito_compras c
            JOIN productos p ON c.id_producto = p.id_producto
            WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result();
}

function agregar_al_carrito($id_usuario, $id_producto, $cantidad) {
    global $conn;
    
    // Verificar si ya existe en el carrito
    $check_sql = "SELECT id_carrito, cantidad FROM carrito_compras WHERE id_usuario = ? AND id_producto = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $id_usuario, $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar cantidad
        $row = $result->fetch_assoc();
        $new_quantity = $row['cantidad'] + $cantidad;
        $update_sql = "UPDATE carrito_compras SET cantidad = ? WHERE id_carrito = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $row['id_carrito']);
        return $update_stmt->execute();
    } else {
        // Insertar nuevo
        // Obtener precio actual
        $price_sql = "SELECT precio FROM productos WHERE id_producto = ?";
        $price_stmt = $conn->prepare($price_sql);
        $price_stmt->bind_param("i", $id_producto);
        $price_stmt->execute();
        $price_res = $price_stmt->get_result();
        if ($price_res->num_rows > 0) {
            $price = $price_res->fetch_assoc()['precio'];
            $insert_sql = "INSERT INTO carrito_compras (id_usuario, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iiid", $id_usuario, $id_producto, $cantidad, $price);
            return $insert_stmt->execute();
        }
    }
    return false;
}

function eliminar_del_carrito($id_carrito) {
    global $conn;
    $sql = "DELETE FROM carrito_compras WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carrito);
    return $stmt->execute();
}

function vaciar_carrito($id_usuario) {
    global $conn;
    $sql = "DELETE FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    return $stmt->execute();
}

function total_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT SUM(cantidad * precio_unitario) as total FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ? $row['total'] : 0;
}

function contar_items_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT SUM(cantidad) as total_items FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_items'] ? $row['total_items'] : 0;
}

// --- Funciones de Reseñas ---

function agregar_resena($id_producto, $id_usuario, $calificacion, $comentario) {
    global $conn;
    
    // Verificar que el usuario haya comprado el producto
    $sql = "SELECT COUNT(*) as count FROM detalle_pedidos dp
            JOIN pedidos p ON dp.id_pedido = p.id_pedido
            WHERE dp.id_producto = ? AND p.id_usuario = ? AND p.estado != 'cancelado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_producto, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] == 0) {
        return ['success' => false, 'message' => 'Debes haber comprado este producto para poder reseñarlo.'];
    }
    
    // Verificar que no haya reseñado antes
    $check_sql = "SELECT COUNT(*) as count FROM resenas WHERE id_producto = ? AND id_usuario = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_producto, $id_usuario);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    
    if ($check_result['count'] > 0) {
        return ['success' => false, 'message' => 'Ya has reseñado este producto anteriormente.'];
    }
    
    // Validar calificación
    if ($calificacion < 1 || $calificacion > 5) {
        return ['success' => false, 'message' => 'La calificación debe estar entre 1 y 5 estrellas.'];
    }
    
    // Agregar reseña
    $sql = "INSERT INTO resenas (id_producto, id_usuario, calificacion, comentario) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_producto, $id_usuario, $calificacion, $comentario);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => '¡Gracias por tu reseña!'];
    } else {
        return ['success' => false, 'message' => 'Error al guardar la reseña.'];
    }
}

function obtener_resenas($id_producto) {
    global $conn;
    $sql = "SELECT r.*, u.nombre as usuario_nombre 
            FROM resenas r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            WHERE r.id_producto = ?
            ORDER BY r.fecha_resena DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    return $stmt->get_result();
}

function promedio_calificacion($id_producto) {
    global $conn;
    $sql = "SELECT AVG(calificacion) as promedio, COUNT(*) as total 
            FROM resenas WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function usuario_puede_resenar($id_producto, $id_usuario) {
    global $conn;
    
    // Verificar si ha comprado el producto
    $sql = "SELECT COUNT(*) as count FROM detalle_pedidos dp
            JOIN pedidos p ON dp.id_pedido = p.id_pedido
            WHERE dp.id_producto = ? AND p.id_usuario = ? AND p.estado != 'cancelado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_producto, $id_usuario);
    $stmt->execute();
    $compro = $stmt->get_result()->fetch_assoc()['count'] > 0;
    
    if (!$compro) {
        return false;
    }
    
    // Verificar si ya reseñó
    $check_sql = "SELECT COUNT(*) as count FROM resenas WHERE id_producto = ? AND id_usuario = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_producto, $id_usuario);
    $check_stmt->execute();
    $ya_reseno = $check_stmt->get_result()->fetch_assoc()['count'] > 0;
    
    return !$ya_reseno;
}

// --- Funciones de Administración ---

function es_admin() {
    start_session_safe();
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requerir_admin() {
    if (!is_logged_in() || !es_admin()) {
        $_SESSION['flash_message'] = "Acceso denegado. Se requieren privilegios de administrador.";
        redirect('../index.php');
    }
}

// Estadísticas del Dashboard
function obtener_estadisticas_dashboard() {
    global $conn;
    
    $stats = [];
    
    // Total de ventas
    $sql = "SELECT SUM(total) as total_ventas FROM pedidos WHERE estado != 'cancelado'";
    $result = $conn->query($sql);
    $stats['total_ventas'] = $result->fetch_assoc()['total_ventas'] ?? 0;
    
    // Total de pedidos
    $sql = "SELECT COUNT(*) as total_pedidos FROM pedidos";
    $result = $conn->query($sql);
    $stats['total_pedidos'] = $result->fetch_assoc()['total_pedidos'];
    
    // Total de usuarios
    $sql = "SELECT COUNT(*) as total_usuarios FROM usuarios WHERE tipo_usuario = 'cliente'";
    $result = $conn->query($sql);
    $stats['total_usuarios'] = $result->fetch_assoc()['total_usuarios'];
    
    // Total de productos
    $sql = "SELECT COUNT(*) as total_productos FROM productos WHERE estado = 'activo'";
    $result = $conn->query($sql);
    $stats['total_productos'] = $result->fetch_assoc()['total_productos'];
    
    // Pedidos pendientes
    $sql = "SELECT COUNT(*) as pedidos_pendientes FROM pedidos WHERE estado = 'pendiente'";
    $result = $conn->query($sql);
    $stats['pedidos_pendientes'] = $result->fetch_assoc()['pedidos_pendientes'];
    
    // Productos con bajo stock
    $sql = "SELECT COUNT(*) as productos_bajo_stock FROM productos WHERE stock < 10 AND stock > 0 AND estado = 'activo'";
    $result = $conn->query($sql);
    $stats['productos_bajo_stock'] = $result->fetch_assoc()['productos_bajo_stock'];
    
    // Ventas del mes actual
    $sql = "SELECT SUM(total) as ventas_mes FROM pedidos 
            WHERE MONTH(fecha_pedido) = MONTH(CURRENT_DATE()) 
            AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE())
            AND estado != 'cancelado'";
    $result = $conn->query($sql);
    $stats['ventas_mes'] = $result->fetch_assoc()['ventas_mes'] ?? 0;
    
    return $stats;
}

function obtener_ultimos_pedidos($limite = 10) {
    global $conn;
    $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
            FROM pedidos p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_pedido DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    return $stmt->get_result();
}

function obtener_productos_bajo_stock($limite = 10) {
    global $conn;
    $sql = "SELECT * FROM productos 
            WHERE stock < 10 AND estado = 'activo'
            ORDER BY stock ASC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    return $stmt->get_result();
}

// Gestión de Pedidos
function obtener_todos_pedidos() {
    global $conn;
    $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
            FROM pedidos p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_pedido DESC";
    return $conn->query($sql);
}

function obtener_detalle_pedido($id_pedido) {
    global $conn;
    $sql = "SELECT dp.*, pr.nombre as producto_nombre, pr.imagen as producto_imagen
            FROM detalle_pedidos dp
            JOIN productos pr ON dp.id_producto = pr.id_producto
            WHERE dp.id_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    return $stmt->get_result();
}

function actualizar_estado_pedido($id_pedido, $nuevo_estado) {
    global $conn;
    $sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id_pedido);
    return $stmt->execute();
}

// Gestión de Usuarios
function obtener_todos_usuarios() {
    global $conn;
    $sql = "SELECT id_usuario, nombre, email, telefono, tipo_usuario, fecha_registro 
            FROM usuarios 
            ORDER BY fecha_registro DESC";
    return $conn->query($sql);
}

function actualizar_rol_usuario($id_usuario, $nuevo_rol) {
    global $conn;
    $sql = "UPDATE usuarios SET tipo_usuario = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_rol, $id_usuario);
    return $stmt->execute();
}

function eliminar_usuario($id_usuario) {
    global $conn;
    // No permitir eliminar el propio usuario
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id_usuario) {
        return false;
    }
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    return $stmt->execute();
}

// Gestión de Categorías
function obtener_todas_categorias() {
    global $conn;
    $sql = "SELECT c.*, COUNT(p.id_producto) as total_productos 
            FROM categorias c
            LEFT JOIN productos p ON c.id_categoria = p.id_categoria
            GROUP BY c.id_categoria
            ORDER BY c.nombre ASC";
    return $conn->query($sql);
}

function agregar_categoria($nombre, $descripcion) {
    global $conn;
    $sql = "INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);
    return $stmt->execute();
}

function actualizar_categoria($id_categoria, $nombre, $descripcion) {
    global $conn;
    $sql = "UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id_categoria);
    return $stmt->execute();
}

function eliminar_categoria($id_categoria) {
    global $conn;
    // Verificar si hay productos en esta categoría
    $check_sql = "SELECT COUNT(*) as count FROM productos WHERE id_categoria = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] > 0) {
        return ['success' => false, 'message' => 'No se puede eliminar. Hay productos en esta categoría.'];
    }
    
    $sql = "DELETE FROM categorias WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_categoria);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Categoría eliminada correctamente.'];
    }
    return ['success' => false, 'message' => 'Error al eliminar la categoría.'];
}

// --- Funciones de Pedidos de Usuario ---

function obtener_pedidos_usuario($id_usuario) {
    global $conn;
    $sql = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result();
}
?>
