<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

start_session_safe();
requerir_admin();

$mensaje = '';
$tipo_mensaje = '';

// Procesar actualización de estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'actualizar_estado') {
        $id_pedido = (int)$_POST['id_pedido'];
        $nuevo_estado = $_POST['estado'];
        
        if (actualizar_estado_pedido($id_pedido, $nuevo_estado)) {
            $mensaje = "Estado del pedido #$id_pedido actualizado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al actualizar el estado del pedido.";
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Obtener pedidos
$pedidos = obtener_todos_pedidos();

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
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">
                            <i class="bi bi-box-seam"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pedidos.php">
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
                    <h1 class="h2 mb-0"><i class="bi bi-receipt"></i> Gestión de Pedidos</h1>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filtrar por Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" <?php echo $filtro_estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="procesando" <?php echo $filtro_estado == 'procesando' ? 'selected' : ''; ?>>Procesando</option>
                                <option value="enviado" <?php echo $filtro_estado == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                <option value="entregado" <?php echo $filtro_estado == 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                                <option value="cancelado" <?php echo $filtro_estado == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Buscar Cliente</label>
                            <input type="text" name="busqueda" class="form-control" placeholder="Nombre o email..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Pedidos -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista de Pedidos</h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($pedidos->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Email</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($pedido = $pedidos->fetch_assoc()): 
                                        // Aplicar filtros
                                        if ($filtro_estado && $pedido['estado'] != $filtro_estado) continue;
                                        if ($busqueda && stripos($pedido['usuario_nombre'], $busqueda) === false && stripos($pedido['usuario_email'], $busqueda) === false) continue;
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $pedido['id_pedido']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                                            <td><small><?php echo htmlspecialchars($pedido['usuario_email']); ?></small></td>
                                            <td><strong>$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="actualizar_estado">
                                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                                    <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                        <option value="procesando" <?php echo $pedido['estado'] == 'procesando' ? 'selected' : ''; ?>>Procesando</option>
                                                        <option value="enviado" <?php echo $pedido['estado'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                                        <option value="entregado" <?php echo $pedido['estado'] == 'entregado' ? 'selected' : ''; ?>>Entregado</option>
                                                        <option value="cancelado" <?php echo $pedido['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td><small><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></small></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalDetalle<?php echo $pedido['id_pedido']; ?>">
                                                    <i class="bi bi-eye"></i> Ver
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Detalle del Pedido -->
                                        <div class="modal fade" id="modalDetalle<?php echo $pedido['id_pedido']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Detalle del Pedido #<?php echo $pedido['id_pedido']; ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6>Información del Cliente</h6>
                                                                <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['usuario_nombre']); ?></p>
                                                                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($pedido['usuario_email']); ?></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Información del Pedido</h6>
                                                                <p class="mb-1"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                                                <p class="mb-1"><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido['metodo_pago']); ?></p>
                                                                <p class="mb-1"><strong>Estado:</strong> <span class="badge bg-info"><?php echo ucfirst($pedido['estado']); ?></span></p>
                                                            </div>
                                                        </div>

                                                        <?php if ($pedido['direccion_entrega']): ?>
                                                            <div class="mb-3">
                                                                <h6>Dirección de Entrega</h6>
                                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($pedido['direccion_entrega'])); ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <h6>Productos del Pedido</h6>
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Producto</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Precio Unit.</th>
                                                                    <th>Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $detalle = obtener_detalle_pedido($pedido['id_pedido']);
                                                                while($item = $detalle->fetch_assoc()):
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($item['producto_nombre']); ?></td>
                                                                        <td><?php echo $item['cantidad']; ?></td>
                                                                        <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                                                        <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                                                                    </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                                    <td><strong class="text-success">$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No hay pedidos registrados</h5>
                        </div>
                    <?php endif; ?>
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
</style>

<?php include '../includes/footer.php'; ?>
