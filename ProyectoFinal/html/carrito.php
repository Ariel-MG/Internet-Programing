<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Verificar login
if (!is_logged_in()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para ver tu carrito.";
    redirect('login.php');
}

$id_usuario = $_SESSION['user_id']; // Asumiendo que user_id se guarda en sesión al login
$mensaje = "";

// Manejar Acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $id_producto = $_POST['id_producto'];
                $cantidad = $_POST['cantidad'];
                if (agregar_al_carrito($id_usuario, $id_producto, $cantidad)) {
                    $_SESSION['cart_message'] = "✓ Producto agregado al carrito correctamente.";
                    $_SESSION['cart_message_type'] = "success";
                } else {
                    $_SESSION['cart_message'] = "✗ Error al agregar producto al carrito.";
                    $_SESSION['cart_message_type'] = "danger";
                }
                
                // Redirigir de vuelta a la página anterior
                $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'productos.php';
                redirect($redirect_url);
                break;

                
            case 'remove':
                $id_carrito = $_POST['id_carrito'];
                eliminar_del_carrito($id_carrito);
                $mensaje = "Producto eliminado.";
                break;
                
            case 'empty':
                vaciar_carrito($id_usuario);
                $mensaje = "Carrito vaciado.";
                break;
                
            case 'update':
                $id_carrito = $_POST['id_carrito'];
                $cantidad = $_POST['cantidad'];
                if ($cantidad > 0) {
                    // Actualización directa por simplicidad
                    $stmt = $conn->prepare("UPDATE carrito_compras SET cantidad = ? WHERE id_carrito = ?");
                    $stmt->bind_param("ii", $cantidad, $id_carrito);
                    $stmt->execute();
                } else {
                    eliminar_del_carrito($id_carrito);
                }
                $mensaje = "Carrito actualizado.";
                break;
        }
    }
}

$cart_items = obtener_carrito($id_usuario);
$total = total_carrito($id_usuario);

include 'includes/header.php';
?>


<div class="container mt-5">
    <h1 class="mb-4">Tu Carrito de Compras</h1>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($cart_items->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $cart_items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if($item['imagen']): ?>
                                        <img src="assets/img/<?php echo htmlspecialchars($item['imagen']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 rounded">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded me-3" style="width: 50px; height: 50px;">
                                            <small>N/A</small>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                            <td style="width: 150px;">
                                <form action="carrito.php" method="POST" class="d-flex">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id_carrito" value="<?php echo $item['id_carrito']; ?>">
                                    <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" class="form-control form-control-sm me-2">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Actualizar">
                                        <i class="bi bi-arrow-clockwise">↻</i>
                                    </button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?></td>
                            <td>
                                <form action="carrito.php" method="POST" onsubmit="return confirm('¿Estás seguro?');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="id_carrito" value="<?php echo $item['id_carrito']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total:</td>
                        <td class="fw-bold fs-5">$<?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="productos.php" class="btn btn-outline-primary">← Seguir Comprando</a>
            <div>
                <form action="carrito.php" method="POST" class="d-inline-block me-2" onsubmit="return confirm('¿Vaciar todo el carrito?');">
                    <input type="hidden" name="action" value="empty">
                    <button type="submit" class="btn btn-outline-danger">Vaciar Carrito</button>
                </form>
                <a href="checkout.php" class="btn btn-success btn-lg">Proceder al Pago →</a>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
            </div>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted">¡Agrega algunos productos para comenzar!</p>
            <a href="productos.php" class="btn btn-primary mt-3">Ver Productos</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
