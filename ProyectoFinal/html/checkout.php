<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

if (!is_logged_in()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión para proceder al pago.";
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
$id_pedido_creado = 0;

// Procesar pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $metodo_pago = $conn->real_escape_string($_POST['metodo_pago']);
    $notas = isset($_POST['notas']) ? $conn->real_escape_string($_POST['notas']) : '';
    
    if (empty($direccion) || empty($telefono) || empty($metodo_pago)) {
        $error = "Todos los campos marcados con * son requeridos.";
    } else {
        // Iniciar transacción
        $conn->begin_transaction();
        
        try {
            // Crear pedido
            $sql = "INSERT INTO pedidos (id_usuario, total, metodo_pago, direccion_entrega) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $direccion_completa = $direccion . "\nTeléfono: " . $telefono;
            if ($notas) {
                $direccion_completa .= "\nNotas: " . $notas;
            }
            $stmt->bind_param("idss", $id_usuario, $total, $metodo_pago, $direccion_completa);
            $stmt->execute();
            $id_pedido_creado = $conn->insert_id;
            
            // Agregar detalles del pedido y actualizar stock
            $cart_items = obtener_carrito($id_usuario);
            while ($item = $cart_items->fetch_assoc()) {
                // Verificar stock disponible
                $check_stock = "SELECT stock FROM productos WHERE id_producto = ?";
                $stmt_check = $conn->prepare($check_stock);
                $stmt_check->bind_param("i", $item['id_producto']);
                $stmt_check->execute();
                $stock_actual = $stmt_check->get_result()->fetch_assoc()['stock'];
                
                if ($stock_actual < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para " . $item['nombre']);
                }
                
                // Insertar detalle del pedido
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $sql = "INSERT INTO detalle_pedidos 
                        (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiidd", $id_pedido_creado, $item['id_producto'], 
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
    <h1 class="mb-4"><i class="bi bi-credit-card"></i> Finalizar Compra</h1>
    
    <?php if ($success): ?>
        <!-- Mensaje de éxito -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-success">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="text-success mb-3">¡Pedido Realizado con Éxito!</h2>
                        <p class="lead">Tu pedido <strong>#<?php echo $id_pedido_creado; ?></strong> ha sido procesado correctamente.</p>
                        <p class="text-muted">Recibirás un correo de confirmación con los detalles de tu pedido.</p>
                        
                        <div class="alert alert-info mt-4">
                            <strong>Total pagado:</strong> $<?php echo number_format($total, 2); ?>
                        </div>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary btn-lg me-2">
                                <i class="bi bi-house"></i> Volver al Inicio
                            </a>
                            <a href="productos.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-bag"></i> Seguir Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Formulario de checkout -->
            <div class="col-md-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Información de Envío</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre Completo</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
                                           readonly>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           value="<?php echo htmlspecialchars($usuario['email']); ?>" 
                                           readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono de Contacto *</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono" 
                                       placeholder="Ej: 555-1234"
                                       value="<?php echo htmlspecialchars($usuario['telefono']); ?>"
                                       required>
                                <small class="text-muted">Para coordinar la entrega</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección de Envío *</label>
                                <textarea class="form-control" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="3" 
                                          placeholder="Calle, número, colonia, ciudad, código postal"
                                          required><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas adicionales (opcional)</label>
                                <textarea class="form-control" 
                                          id="notas" 
                                          name="notas" 
                                          rows="2" 
                                          placeholder="Referencias, instrucciones especiales, etc."></textarea>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-3"><i class="bi bi-wallet2"></i> Método de Pago *</h5>
                            
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="metodo_pago" 
                                           id="tarjeta" 
                                           value="tarjeta" 
                                           required>
                                    <label class="form-check-label" for="tarjeta">
                                        <i class="bi bi-credit-card"></i> Tarjeta de Crédito/Débito
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="metodo_pago" 
                                           id="paypal" 
                                           value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="bi bi-paypal"></i> PayPal
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="metodo_pago" 
                                           id="transferencia" 
                                           value="transferencia">
                                    <label class="form-check-label" for="transferencia">
                                        <i class="bi bi-bank"></i> Transferencia Bancaria
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="metodo_pago" 
                                           id="efectivo" 
                                           value="efectivo">
                                    <label class="form-check-label" for="efectivo">
                                        <i class="bi bi-cash"></i> Efectivo contra entrega
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Confirmar Pedido
                                </button>
                                <a href="carrito.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al Carrito
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Resumen del pedido -->
            <div class="col-md-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted mb-3">Productos:</h6>
                            <?php 
                            $cart_items = obtener_carrito($id_usuario);
                            while ($item = $cart_items->fetch_assoc()): 
                            ?>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <div class="flex-grow-1">
                                        <small class="d-block"><?php echo htmlspecialchars($item['nombre']); ?></small>
                                        <small class="text-muted">Cantidad: <?php echo $item['cantidad']; ?></small>
                                    </div>
                                    <div class="text-end">
                                        <small class="fw-bold">$<?php echo number_format($item['cantidad'] * $item['precio_unitario'], 2); ?></small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span class="text-success">GRATIS</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <strong class="fs-5">Total:</strong>
                            <strong class="fs-4 text-success">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="bi bi-shield-check"></i> 
                                Compra 100% segura
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
