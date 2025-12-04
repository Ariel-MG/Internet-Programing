# Ejemplos Prácticos de Implementación

Esta guía contiene ejemplos concretos de cómo implementar funcionalidades comunes en el proyecto.

---

## 📋 Índice de Ejemplos

1. [Crear Página de Detalle de Producto](#1-crear-página-de-detalle-de-producto)
2. [Implementar Sistema de Búsqueda](#2-implementar-sistema-de-búsqueda)
3. [Agregar Filtros por Categoría](#3-agregar-filtros-por-categoría)
4. [Crear Panel de Administración](#4-crear-panel-de-administración)
5. [Implementar Proceso de Checkout](#5-implementar-proceso-de-checkout)
6. [Agregar Sistema de Reseñas](#6-agregar-sistema-de-reseñas)
7. [Implementar Paginación](#7-implementar-paginación)
8. [Agregar Wishlist/Favoritos](#8-agregar-wishlistfavoritos)

---

## 1. Crear Página de Detalle de Producto

### Archivo: `producto.php`

```php
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Obtener ID del producto
$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_producto <= 0) {
    redirect('productos.php');
}

// Obtener datos del producto
$sql = "SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        WHERE p.id_producto = ? AND p.estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('productos.php');
}

$producto = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <!-- Imagen del producto -->
        <div class="col-md-6">
            <?php if($producto['imagen']): ?>
                <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                     style="height: 400px;">
                    <h3>Sin Imagen</h3>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información del producto -->
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            
            <?php if($producto['categoria_nombre']): ?>
                <p class="text-muted">
                    <strong>Categoría:</strong> 
                    <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                </p>
            <?php endif; ?>

            <h2 class="product-price mb-4">
                $<?php echo number_format($producto['precio'], 2); ?>
            </h2>

            <p class="lead"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>

            <div class="mb-3">
                <strong>Disponibilidad:</strong>
                <?php if($producto['stock'] > 0): ?>
                    <span class="badge bg-success">En Stock (<?php echo $producto['stock']; ?> unidades)</span>
                <?php else: ?>
                    <span class="badge bg-danger">Agotado</span>
                <?php endif; ?>
            </div>

            <?php if($producto['stock'] > 0): ?>
                <form action="carrito.php" method="POST" class="mt-4">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" 
                                   name="cantidad" 
                                   id="cantidad" 
                                   class="form-control" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo $producto['stock']; ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-cart-plus"></i> Agregar al Carrito
                    </button>
                    <a href="productos.php" class="btn btn-outline-secondary btn-lg">
                        Volver a Productos
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
```

---

## 2. Implementar Sistema de Búsqueda

### Modificar `productos.php`

```php
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Obtener término de búsqueda
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Construir consulta SQL
if ($search) {
    $sql = "SELECT * FROM productos 
            WHERE estado = 'activo' 
            AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')
            ORDER BY nombre ASC";
} else {
    $sql = "SELECT * FROM productos WHERE estado = 'activo' ORDER BY nombre ASC";
}

$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Nuestros Productos</h1>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="productos.php" class="mb-4">
        <div class="input-group">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Buscar productos..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Buscar
            </button>
            <?php if($search): ?>
                <a href="productos.php" class="btn btn-outline-secondary">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if($search): ?>
        <p class="text-muted">
            Mostrando resultados para: <strong><?php echo htmlspecialchars($search); ?></strong>
            (<?php echo $result->num_rows; ?> productos encontrados)
        </p>
    <?php endif; ?>

    <!-- Grid de productos -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <!-- Tarjeta de producto (igual que antes) -->
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No se encontraron productos<?php echo $search ? ' con ese criterio de búsqueda' : ''; ?>.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
```

---

## 3. Agregar Filtros por Categoría

### Modificar `productos.php` con Filtros

```php
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Obtener categorías
$categorias_sql = "SELECT * FROM categorias ORDER BY nombre ASC";
$categorias_result = $conn->query($categorias_sql);

// Obtener filtros
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';

// Construir consulta
$sql = "SELECT * FROM productos WHERE estado = 'activo'";

if ($categoria_id > 0) {
    $sql .= " AND id_categoria = $categoria_id";
}

if ($search) {
    $sql .= " AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
}

// Ordenamiento
switch($orden) {
    case 'precio_asc':
        $sql .= " ORDER BY precio ASC";
        break;
    case 'precio_desc':
        $sql .= " ORDER BY precio DESC";
        break;
    case 'nombre_desc':
        $sql .= " ORDER BY nombre DESC";
        break;
    default:
        $sql .= " ORDER BY nombre ASC";
}

$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Nuestros Productos</h1>
    
    <div class="row">
        <!-- Sidebar de filtros -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="productos.php">
                        <!-- Búsqueda -->
                        <div class="mb-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- Categorías -->
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="0">Todas</option>
                                <?php while($cat = $categorias_result->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id_categoria']; ?>"
                                            <?php echo $categoria_id == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Ordenamiento -->
                        <div class="mb-3">
                            <label class="form-label">Ordenar por</label>
                            <select name="orden" class="form-select">
                                <option value="nombre_asc" <?php echo $orden == 'nombre_asc' ? 'selected' : ''; ?>>
                                    Nombre (A-Z)
                                </option>
                                <option value="nombre_desc" <?php echo $orden == 'nombre_desc' ? 'selected' : ''; ?>>
                                    Nombre (Z-A)
                                </option>
                                <option value="precio_asc" <?php echo $orden == 'precio_asc' ? 'selected' : ''; ?>>
                                    Precio (Menor a Mayor)
                                </option>
                                <option value="precio_desc" <?php echo $orden == 'precio_desc' ? 'selected' : ''; ?>>
                                    Precio (Mayor a Menor)
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                        <a href="productos.php" class="btn btn-outline-secondary w-100 mt-2">Limpiar</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grid de productos -->
        <div class="col-md-9">
            <p class="text-muted mb-3"><?php echo $result->num_rows; ?> productos encontrados</p>
            
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <!-- Tarjeta de producto -->
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No se encontraron productos.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
```

---

## 4. Crear Panel de Administración

### Archivo: `admin/index.php`

```php
<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

start_session_safe();

// Verificar que sea admin
if (!is_logged_in() || $_SESSION['user_role'] !== 'admin') {
    redirect('../index.php');
}

// Estadísticas
$stats = [];

// Total de productos
$sql = "SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'";
$stats['productos'] = $conn->query($sql)->fetch_assoc()['total'];

// Total de usuarios
$sql = "SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'cliente'";
$stats['usuarios'] = $conn->query($sql)->fetch_assoc()['total'];

// Total de pedidos
$sql = "SELECT COUNT(*) as total FROM pedidos";
$stats['pedidos'] = $conn->query($sql)->fetch_assoc()['total'];

// Ventas totales
$sql = "SELECT SUM(total) as total FROM pedidos WHERE estado != 'cancelado'";
$result = $conn->query($sql)->fetch_assoc();
$stats['ventas'] = $result['total'] ? $result['total'] : 0;

// Productos con bajo stock
$sql = "SELECT * FROM productos WHERE stock < 10 AND estado = 'activo' ORDER BY stock ASC LIMIT 5";
$bajo_stock = $conn->query($sql);

// Últimos pedidos
$sql = "SELECT p.*, u.nombre as cliente_nombre 
        FROM pedidos p 
        LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
        ORDER BY p.fecha_pedido DESC 
        LIMIT 10";
$ultimos_pedidos = $conn->query($sql);

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Panel de Administración</h1>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <h2><?php echo $stats['productos']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <h2><?php echo $stats['usuarios']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Pedidos</h5>
                    <h2><?php echo $stats['pedidos']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Ventas Totales</h5>
                    <h2>$<?php echo number_format($stats['ventas'], 2); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <a href="productos.php" class="btn btn-primary me-2">Gestionar Productos</a>
                    <a href="agregar.php" class="btn btn-success me-2">Agregar Producto</a>
                    <a href="pedidos.php" class="btn btn-info me-2">Ver Pedidos</a>
                    <a href="usuarios.php" class="btn btn-secondary">Ver Usuarios</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos con bajo stock -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">⚠️ Productos con Bajo Stock</h5>
                </div>
                <div class="card-body">
                    <?php if($bajo_stock->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($producto = $bajo_stock->fetch_assoc()): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                    <span class="badge bg-danger"><?php echo $producto['stock']; ?> unidades</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No hay productos con bajo stock.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Últimos pedidos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📦 Últimos Pedidos</h5>
                </div>
                <div class="card-body">
                    <?php if($ultimos_pedidos->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($pedido = $ultimos_pedidos->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>Pedido #<?php echo $pedido['id_pedido']; ?></strong>
                                        <span class="badge bg-<?php 
                                            echo $pedido['estado'] == 'entregado' ? 'success' : 
                                                ($pedido['estado'] == 'cancelado' ? 'danger' : 'warning'); 
                                        ?>">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $pedido['cliente_nombre']; ?> - 
                                        $<?php echo number_format($pedido['total'], 2); ?>
                                    </small>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No hay pedidos recientes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
```

---

## 5. Implementar Proceso de Checkout

### Archivo: `checkout.php`

```php
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

if (!is_logged_in()) {
    redirect('login.php');
}

$id_usuario = $_SESSION['user_id'];

// Verificar que el carrito no esté vacío
$cart_items = obtener_carrito($id_usuario);
if ($cart_items->num_rows == 0) {
    $_SESSION['flash_message'] = "Tu carrito está vacío.";
    redirect('carrito.php');
}

$total = total_carrito($id_usuario);

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

$error = '';
$success = false;

// Procesar pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);
    
    if (empty($direccion) || empty($metodo_pago)) {
        $error = "Todos los campos son requeridos.";
    } else {
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Crear pedido
            $sql = "INSERT INTO pedidos (id_usuario, total, metodo_pago, direccion_entrega) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("idss", $id_usuario, $total, $metodo_pago, $direccion);
            $stmt->execute();
            $id_pedido = $conn->insert_id;
            
            // Agregar detalles del pedido
            $cart_items = obtener_carrito($id_usuario);
            while ($item = $cart_items->fetch_assoc()) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $sql = "INSERT INTO detalle_pedidos 
                        (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiidd", $id_pedido, $item['id_producto'], 
                                 $item['cantidad'], $item['precio_unitario'], $subtotal);
                $stmt->execute();
                
                // Actualizar stock
                $sql = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $item['cantidad'], $item['id_producto']);
                $stmt->execute();
            }
            
            // Vaciar carrito
            vaciar_carrito($id_usuario);
            
            // Confirmar transacción
            $conn->commit();
            $success = true;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al procesar el pedido: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Finalizar Compra</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h4>¡Pedido realizado con éxito!</h4>
            <p>Tu pedido ha sido procesado. Recibirás un correo de confirmación pronto.</p>
            <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
            <a href="productos.php" class="btn btn-outline-primary">Seguir Comprando</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Formulario de checkout -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información de Envío</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección de Envío *</label>
                                <textarea class="form-control" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="3" 
                                          required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago *</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                    <option value="">Selecciona...</option>
                                    <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="efectivo">Efectivo contra entrega</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                Confirmar Pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Resumen del pedido -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $cart_items = obtener_carrito($id_usuario);
                        while ($item = $cart_items->fetch_assoc()): 
                        ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo htmlspecialchars($item['nombre']); ?> x<?php echo $item['cantidad']; ?></span>
                                <span>$<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?></span>
                            </div>
                        <?php endwhile; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-success">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
```

---

## 6. Agregar Sistema de Reseñas

### Paso 1: Crear tabla en `setup_db.php`

```php
"resenas" => "CREATE TABLE IF NOT EXISTS resenas (
    id_resena INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    id_usuario INT,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha_resena TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
)"
```

### Paso 2: Agregar función en `functions.php`

```php
function agregar_resena($id_producto, $id_usuario, $calificacion, $comentario) {
    global $conn;
    
    // Verificar que el usuario haya comprado el producto
    $sql = "SELECT COUNT(*) as count FROM detalle_pedidos dp
            JOIN pedidos p ON dp.id_pedido = p.id_pedido
            WHERE dp.id_producto = ? AND p.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_producto, $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['count'] == 0) {
        return false; // No ha comprado el producto
    }
    
    // Agregar reseña
    $sql = "INSERT INTO resenas (id_producto, id_usuario, calificacion, comentario) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_producto, $id_usuario, $calificacion, $comentario);
    return $stmt->execute();
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
```

### Paso 3: Mostrar en `producto.php`

```php
// Después de obtener el producto
$resenas = obtener_resenas($id_producto);
$calificacion_data = promedio_calificacion($id_producto);

// En el HTML, después de la información del producto
?>
<div class="mt-5">
    <h3>Reseñas de Clientes</h3>
    
    <?php if ($calificacion_data['total'] > 0): ?>
        <div class="mb-3">
            <strong>Calificación Promedio:</strong>
            <?php 
            $promedio = round($calificacion_data['promedio'], 1);
            for($i = 1; $i <= 5; $i++) {
                echo $i <= $promedio ? '⭐' : '☆';
            }
            ?>
            (<?php echo $promedio; ?>/5 - <?php echo $calificacion_data['total']; ?> reseñas)
        </div>
    <?php endif; ?>
    
    <?php while($resena = $resenas->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <strong><?php echo htmlspecialchars($resena['usuario_nombre']); ?></strong>
                    <span>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <?php echo $i <= $resena['calificacion'] ? '⭐' : '☆'; ?>
                        <?php endfor; ?>
                    </span>
                </div>
                <p class="mt-2"><?php echo nl2br(htmlspecialchars($resena['comentario'])); ?></p>
                <small class="text-muted"><?php echo date('d/m/Y', strtotime($resena['fecha_resena'])); ?></small>
            </div>
        </div>
    <?php endwhile; ?>
</div>
```

---

## 7. Implementar Paginación

### Función en `functions.php`

```php
function paginar_productos($page = 1, $per_page = 12, $categoria = 0, $search = '') {
    global $conn;
    
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT * FROM productos WHERE estado = 'activo'";
    
    if ($categoria > 0) {
        $sql .= " AND id_categoria = $categoria";
    }
    
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
    }
    
    $sql .= " LIMIT $per_page OFFSET $offset";
    
    return $conn->query($sql);
}

function contar_productos($categoria = 0, $search = '') {
    global $conn;
    
    $sql = "SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'";
    
    if ($categoria > 0) {
        $sql .= " AND id_categoria = $categoria";
    }
    
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
    }
    
    $result = $conn->query($sql);
    return $result->fetch_assoc()['total'];
}
```

### Usar en `productos.php`

```php
<?php
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$result = paginar_productos($page, $per_page, $categoria, $search);
$total_productos = contar_productos($categoria, $search);
$total_pages = ceil($total_productos / $per_page);

// Después del grid de productos
?>
<nav aria-label="Paginación">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page-1; ?>&categoria=<?php echo $categoria; ?>&search=<?php echo urlencode($search); ?>">
                    Anterior
                </a>
            </li>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&categoria=<?php echo $categoria; ?>&search=<?php echo urlencode($search); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page+1; ?>&categoria=<?php echo $categoria; ?>&search=<?php echo urlencode($search); ?>">
                    Siguiente
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
```

---

**Última actualización**: 2025-11-26
