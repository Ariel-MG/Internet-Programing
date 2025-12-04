<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Obtener productos destacados (últimos 6 productos)
$sql_destacados = "SELECT * FROM productos WHERE estado = 'activo' ORDER BY fecha_creacion DESC LIMIT 6";
$productos_destacados = $conn->query($sql_destacados);

include 'includes/header.php';
?>

<div class="container mt-5">
    <!-- Hero Section -->
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Bienvenido a Mi Tienda</h1>
            <p class="col-md-8 fs-4">Explora nuestros productos y encuentra las mejores ofertas.</p>
            
            <?php if (is_logged_in()): ?>
                <p class="lead">Hola, <strong><?php echo get_user_name(); ?></strong>. ¡Qué bueno verte de nuevo!</p>
                <a class="btn btn-primary btn-lg" href="productos.php" role="button">
                    <i class="bi bi-bag"></i> Ver Productos
                </a>
                <a class="btn btn-outline-success btn-lg" href="carrito.php" role="button">
                    <i class="bi bi-cart"></i> Mi Carrito
                </a>
            <?php else: ?>
                <p class="lead">Regístrate para comenzar a comprar.</p>
                <a class="btn btn-primary btn-lg" href="registro.php" role="button">
                    <i class="bi bi-person-plus"></i> Registrarse
                </a>
                <a class="btn btn-outline-secondary btn-lg" href="login.php" role="button">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Productos Destacados -->
    <?php if ($productos_destacados->num_rows > 0): ?>
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-star-fill text-warning"></i> Productos Destacados</h2>
                <a href="productos.php" class="btn btn-outline-primary">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while($producto = $productos_destacados->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm product-card border-0">
                            <div class="overflow-hidden position-relative">
                                <?php if($producto['imagen']): ?>
                                    <img src="assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <span>Sin Imagen</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if($producto['stock'] < 10 && $producto['stock'] > 0): ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                                        ¡Últimas unidades!
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="card-text text-muted text-truncate">
                                    <?php echo htmlspecialchars(substr($producto['descripcion'], 0, 60)) . '...'; ?>
                                </p>
                                <h4 class="product-price mb-3">$<?php echo number_format($producto['precio'], 2); ?></h4>
                                
                                <div class="mt-auto">
                                    <a href="producto.php?id=<?php echo $producto['id_producto']; ?>" 
                                       class="btn btn-primary w-100">
                                        Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Características -->
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <i class="bi bi-truck" style="font-size: 3rem; color: var(--color-primary);"></i>
                    <h5 class="mt-3">Envío Gratis</h5>
                    <p class="text-muted">En compras mayores a $500</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--color-primary);"></i>
                    <h5 class="mt-3">Compra Segura</h5>
                    <p class="text-muted">Protegemos tus datos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <i class="bi bi-headset" style="font-size: 3rem; color: var(--color-primary);"></i>
                    <h5 class="mt-3">Soporte 24/7</h5>
                    <p class="text-muted">Estamos para ayudarte</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
