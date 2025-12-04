<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Verificar login
if (!is_logged_in()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para ver tus pedidos.";
    redirect('login.php');
}

$id_usuario = $_SESSION['user_id'];
$pedidos = obtener_pedidos_usuario($id_usuario);

include 'includes/header.php';
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-clock-history"></i> Mis Pedidos</h1>
    
    <?php if ($pedidos->num_rows == 0): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aún no has realizado ningún pedido.
            <a href="productos.php" class="alert-link">¡Comienza a comprar!</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Pedido #<?php echo $pedido['id_pedido']; ?></strong>
                                <span class="text-muted ms-2">
                                    <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                </span>
                            </div>
                            <div>
                                <?php
                                $badge_class = 'secondary';
                                switch($pedido['estado']) {
                                    case 'pendiente': $badge_class = 'warning'; break;
                                    case 'procesando': $badge_class = 'info'; break;
                                    case 'enviado': $badge_class = 'primary'; break;
                                    case 'entregado': $badge_class = 'success'; break;
                                    case 'cancelado': $badge_class = 'danger'; break;
                                }
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>">
                                    <?php echo ucfirst($pedido['estado']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-3">Productos:</h6>
                                    <?php
                                    $detalles = obtener_detalle_pedido($pedido['id_pedido']);
                                    while ($detalle = $detalles->fetch_assoc()):
                                    ?>
                                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                            <?php if (!empty($detalle['producto_imagen'])): ?>
                                                <img src="assets/img/<?php echo htmlspecialchars($detalle['producto_imagen']); ?>" 
                                                     alt="<?php echo htmlspecialchars($detalle['producto_nombre'] ?? 'Producto'); ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                                     class="rounded me-3">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded me-3" 
                                                     style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">
                                                    <?php echo htmlspecialchars($detalle['producto_nombre'] ?? 'Producto no disponible'); ?>
                                                </div>
                                                <small class="text-muted">
                                                    Cantidad: <?php echo $detalle['cantidad']; ?> × 
                                                    $<?php echo number_format($detalle['precio_unitario'], 2); ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <strong>$<?php echo number_format($detalle['subtotal'], 2); ?></strong>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Resumen</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Total:</span>
                                                <strong class="text-primary">$<?php echo number_format($pedido['total'], 2); ?></strong>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-credit-card"></i> 
                                                    <?php echo ucfirst($pedido['metodo_pago']); ?>
                                                </small>
                                            </div>
                                            <?php if ($pedido['direccion_entrega']): ?>
                                                <hr>
                                                <small>
                                                    <strong>Dirección:</strong><br>
                                                    <?php echo nl2br(htmlspecialchars($pedido['direccion_entrega'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($pedido['estado'] == 'entregado'): ?>
                                        <button class="btn btn-sm btn-outline-primary w-100 mt-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalComprarOtraVez<?php echo $pedido['id_pedido']; ?>">
                                            <i class="bi bi-arrow-repeat"></i> Comprar otra vez
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
