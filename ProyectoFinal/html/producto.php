<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

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

// Procesar envío de reseña
$resena_message = '';
$resena_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_review') {
    if (is_logged_in()) {
        $calificacion = (int)$_POST['calificacion'];
        $comentario = trim($_POST['comentario']);
        
        if (empty($comentario)) {
            $resena_message = 'El comentario no puede estar vacío.';
            $resena_type = 'danger';
        } else {
            $resultado = agregar_resena($id_producto, $_SESSION['user_id'], $calificacion, $comentario);
            $resena_message = $resultado['message'];
            $resena_type = $resultado['success'] ? 'success' : 'danger';
        }
    } else {
        $resena_message = 'Debes iniciar sesión para dejar una reseña.';
        $resena_type = 'warning';
    }
}

// Obtener reseñas del producto
$resenas = obtener_resenas($id_producto);
$calificacion_data = promedio_calificacion($id_producto);

// Verificar si el usuario puede reseñar
$puede_resenar = false;
if (is_logged_in()) {
    $puede_resenar = usuario_puede_resenar($id_producto, $_SESSION['user_id']);
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="productos.php">Productos</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($producto['nombre']); ?></li>
    </nav>

    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['cart_message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['cart_message']; 
            unset($_SESSION['cart_message']);
            unset($_SESSION['cart_message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Imagen del producto -->
        <div class="col-md-6">
            <?php if($producto['imagen']): ?>
                <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                     class="img-fluid rounded shadow" 
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     style="width: 100%; max-height: 500px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded shadow" 
                     style="height: 400px;">
                    <div class="text-center">
                        <i class="bi bi-image" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">Sin Imagen</h3>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información del producto -->
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            
            <?php if($producto['categoria_nombre']): ?>
                <p class="text-muted mb-3">
                    <strong>Categoría:</strong> 
                    <a href="productos.php?categoria=<?php echo $producto['id_categoria']; ?>">
                        <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                    </a>
                </p>
            <?php endif; ?>

            <h2 class="product-price mb-4">
                $<?php echo number_format($producto['precio'], 2); ?>
            </h2>

            <?php if($producto['descripcion']): ?>
                <div class="mb-4">
                    <h5>Descripción</h5>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <strong>Disponibilidad:</strong>
                <?php if($producto['stock'] > 0): ?>
                    <span class="badge bg-success">En Stock (<?php echo $producto['stock']; ?> unidades)</span>
                <?php else: ?>
                    <span class="badge bg-danger">Agotado</span>
                <?php endif; ?>
            </div>

            <?php if($producto['stock'] > 0): ?>
                <?php if(is_logged_in()): ?>
                    <form action="carrito.php" method="POST" class="mt-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="cantidad" class="form-label fw-bold">Cantidad:</label>
                                <input type="number" 
                                       name="cantidad" 
                                       id="cantidad" 
                                       class="form-control form-control-lg" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $producto['stock']; ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                            <a href="productos.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Volver a Productos
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>Inicia sesión</strong> para agregar productos al carrito.
                        <div class="mt-2">
                            <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                            <a href="registro.php" class="btn btn-outline-primary">Registrarse</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Producto agotado.</strong> Este producto no está disponible en este momento.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sección de Reseñas -->
    <div class="mt-5">
        <hr class="my-5">
        <h3 class="mb-4"><i class="bi bi-star-fill text-warning"></i> Reseñas de Clientes</h3>
        
        <?php if ($resena_message): ?>
            <div class="alert alert-<?php echo $resena_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($resena_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Resumen de calificación -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center border-end">
                        <?php if ($calificacion_data['total'] > 0): ?>
                            <h1 class="display-4 mb-0"><?php echo number_format($calificacion_data['promedio'], 1); ?></h1>
                            <div class="text-warning fs-4">
                                <?php 
                                $promedio = round($calificacion_data['promedio']);
                                for($i = 1; $i <= 5; $i++) {
                                    echo $i <= $promedio ? '★' : '☆';
                                }
                                ?>
                            </div>
                            <p class="text-muted mb-0"><?php echo $calificacion_data['total']; ?> reseña<?php echo $calificacion_data['total'] != 1 ? 's' : ''; ?></p>
                        <?php else: ?>
                            <h5 class="text-muted">Sin reseñas aún</h5>
                            <p class="mb-0">Sé el primero en reseñar este producto</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8 ps-4">
                        <?php if ($puede_resenar): ?>
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> Has comprado este producto. ¡Comparte tu experiencia!
                            </div>
                        <?php elseif (is_logged_in()): ?>
                            <?php
                            // Verificar si ya reseñó
                            $check_sql = "SELECT COUNT(*) as count FROM resenas WHERE id_producto = ? AND id_usuario = ?";
                            $check_stmt = $conn->prepare($check_sql);
                            $check_stmt->bind_param("ii", $id_producto, $_SESSION['user_id']);
                            $check_stmt->execute();
                            $ya_reseno = $check_stmt->get_result()->fetch_assoc()['count'] > 0;
                            ?>
                            <?php if ($ya_reseno): ?>
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle"></i> Ya has reseñado este producto. ¡Gracias por tu opinión!
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-lock"></i> Debes comprar este producto para poder reseñarlo.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-secondary mb-0">
                                <i class="bi bi-person"></i> <a href="login.php">Inicia sesión</a> para dejar una reseña.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario para agregar reseña -->
        <?php if ($puede_resenar): ?>
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Escribe tu Reseña</h5>
                </div>
                <div class="card-body">
                    <form action="producto.php?id=<?php echo $id_producto; ?>" method="POST">
                        <input type="hidden" name="action" value="add_review">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Calificación *</label>
                            <div class="rating-input">
                                <div class="btn-group" role="group" aria-label="Calificación">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="calificacion" 
                                               id="rating<?php echo $i; ?>" 
                                               value="<?php echo $i; ?>" 
                                               <?php echo $i == 5 ? 'checked' : ''; ?>>
                                        <label class="btn btn-outline-warning" for="rating<?php echo $i; ?>">
                                            <?php echo $i; ?> ★
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comentario" class="form-label fw-bold">Tu Opinión *</label>
                            <textarea class="form-control" 
                                      id="comentario" 
                                      name="comentario" 
                                      rows="4" 
                                      placeholder="Comparte tu experiencia con este producto..."
                                      required></textarea>
                            <small class="text-muted">Mínimo 10 caracteres</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send"></i> Publicar Reseña
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de reseñas -->
        <?php if ($resenas->num_rows > 0): ?>
            <h4 class="mb-3">Todas las Reseñas (<?php echo $resenas->num_rows; ?>)</h4>
            <?php while($resena = $resenas->fetch_assoc()): ?>
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1">
                                    <i class="bi bi-person-circle text-primary"></i> 
                                    <?php echo htmlspecialchars($resena['usuario_nombre']); ?>
                                </h5>
                                <div class="text-warning">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php echo $i <= $resena['calificacion'] ? '★' : '☆'; ?>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-2">(<?php echo $resena['calificacion']; ?>/5)</span>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> 
                                <?php echo date('d/m/Y', strtotime($resena['fecha_resena'])); ?>
                            </small>
                        </div>
                        <p class="mb-0 mt-3"><?php echo nl2br(htmlspecialchars($resena['comentario'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php elseif ($calificacion_data['total'] == 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-chat-left-text text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">Aún no hay reseñas</h5>
                <p class="text-muted">Sé el primero en compartir tu opinión sobre este producto</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Productos relacionados -->
    <?php if($producto['id_categoria']): ?>
        <div class="mt-5">
            <h3 class="mb-4">Productos Relacionados</h3>
            <?php
            $sql_related = "SELECT * FROM productos 
                           WHERE id_categoria = ? 
                           AND id_producto != ? 
                           AND estado = 'activo' 
                           LIMIT 4";
            $stmt_related = $conn->prepare($sql_related);
            $stmt_related->bind_param("ii", $producto['id_categoria'], $id_producto);
            $stmt_related->execute();
            $related_products = $stmt_related->get_result();
            ?>
            
            <?php if($related_products->num_rows > 0): ?>
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    <?php while($related = $related_products->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm product-card border-0">
                                <div class="overflow-hidden position-relative">
                                    <?php if($related['imagen']): ?>
                                        <img src="assets/img/<?php echo htmlspecialchars($related['imagen']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($related['nombre']); ?>" 
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <span>Sin Imagen</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($related['nombre']); ?></h5>
                                    <h4 class="product-price">$<?php echo number_format($related['precio'], 2); ?></h4>
                                    <a href="producto.php?id=<?php echo $related['id_producto']; ?>" 
                                       class="btn btn-outline-primary w-100">
                                        Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
