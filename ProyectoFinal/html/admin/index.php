<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

start_session_safe();
requerir_admin();

$stats = obtener_estadisticas_dashboard();
$ultimos_pedidos = obtener_ultimos_pedidos(5);
$productos_bajo_stock = obtener_productos_bajo_stock(5);

include '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Panel de Administración</span>
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">
                            <i class="bi bi-box-seam"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pedidos.php">
                            <i class="bi bi-receipt"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categorias.php">
                            <i class="bi bi-tags"></i> Categorías
                        </a>
                    </li>
                </ul>
                
                <hr>
                
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="bi bi-house"></i> Volver al Sitio
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <button class="navbar-toggler d-md-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="h2 mb-0">Dashboard Administrativo</h1>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="badge bg-secondary p-2">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de Estadísticas -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-primary mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase text-white-50">Ventas Totales</h6>
                                    <h3 class="mb-0">$<?php echo number_format($stats['total_ventas'], 2); ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-success mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase text-white-50">Total Pedidos</h6>
                                    <h3 class="mb-0"><?php echo $stats['total_pedidos']; ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-info mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase text-white-50">Usuarios</h6>
                                    <h3 class="mb-0"><?php echo $stats['total_usuarios']; ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card text-white bg-warning mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase text-dark">Productos</h6>
                                    <h3 class="mb-0 text-dark"><?php echo $stats['total_productos']; ?></h3>
                                </div>
                                <div>
                                    <i class="bi bi-box-seam text-dark" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas Adicionales -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Pedidos Pendientes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['pedidos_pendientes']; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Bajo Stock
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['productos_bajo_stock']; ?> productos
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-box text-warning" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Ventas del Mes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?php echo number_format($stats['ventas_mes'], 2); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-graph-up text-success" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Últimos Pedidos -->
                <div class="col-md-7">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-receipt"></i> Últimos Pedidos</h5>
                            <a href="pedidos.php" class="btn btn-sm btn-light">Ver Todos</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($ultimos_pedidos->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($pedido = $ultimos_pedidos->fetch_assoc()): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $pedido['id_pedido']; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                                                    <td><strong>$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                                                    <td>
                                                        <?php
                                                        $badge_class = [
                                                            'pendiente' => 'warning',
                                                            'procesando' => 'info',
                                                            'enviado' => 'primary',
                                                            'entregado' => 'success',
                                                            'cancelado' => 'danger'
                                                        ];
                                                        $class = $badge_class[$pedido['estado']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?php echo $class; ?>">
                                                            <?php echo ucfirst($pedido['estado']); ?>
                                                        </span>
                                                    </td>
                                                    <td><small><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></small></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted p-4">No hay pedidos recientes</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Productos con Bajo Stock -->
                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Bajo Stock</h5>
                            <a href="productos.php" class="btn btn-sm btn-dark">Ver Todos</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($productos_bajo_stock->num_rows > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php while($producto = $productos_bajo_stock->fetch_assoc()): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?php echo $producto['id_producto']; ?></small>
                                            </div>
                                            <span class="badge bg-danger rounded-pill">
                                                <?php echo $producto['stock']; ?> unidades
                                            </span>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-center text-muted p-4">Stock suficiente en todos los productos</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="mb-3">Accesos Rápidos</h4>
                </div>
                <div class="col-md-3">
                    <a href="productos.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Gestionar Productos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="pedidos.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <i class="bi bi-receipt text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Ver Pedidos</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="usuarios.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Gestionar Usuarios</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="categorias.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <i class="bi bi-tags text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Gestionar Categorías</h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-heading {
    font-size: .75rem;
    color: #ecf0f1 !important;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #ecf0f1;
    padding: 12px 20px;
    border-radius: 0;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(52, 152, 219, 0.2);
    color: #fff;
    padding-left: 25px;
}

.sidebar .nav-link.active {
    color: #fff;
    background-color: #3498db;
    border-left: 4px solid #2ecc71;
    font-weight: 600;
}

.sidebar .nav-link i {
    margin-right: 10px;
    font-size: 1.1rem;
}

.sidebar hr {
    border-color: rgba(236, 240, 241, 0.2);
    margin: 1rem 0;
}

.hover-card {
    transition: transform 0.2s;
}

.hover-card:hover {
    transform: translateY(-5px);
}

.border-left-danger {
    border-left: 4px solid #dc3545;
}

.border-left-warning {
    border-left: 4px solid #ffc107;
}

.border-left-success {
    border-left: 4px solid #28a745;
}
</style>

<?php include '../includes/footer.php'; ?>
