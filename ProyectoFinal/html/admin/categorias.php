<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

start_session_safe();
requerir_admin();

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'agregar') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (empty($nombre)) {
            $mensaje = "El nombre de la categoría es obligatorio.";
            $tipo_mensaje = 'warning';
        } else {
            if (agregar_categoria($nombre, $descripcion)) {
                $mensaje = "Categoría agregada correctamente.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al agregar la categoría.";
                $tipo_mensaje = 'danger';
            }
        }
    } elseif ($_POST['action'] == 'editar') {
        $id_categoria = (int)$_POST['id_categoria'];
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        
        if (empty($nombre)) {
            $mensaje = "El nombre de la categoría es obligatorio.";
            $tipo_mensaje = 'warning';
        } else {
            if (actualizar_categoria($id_categoria, $nombre, $descripcion)) {
                $mensaje = "Categoría actualizada correctamente.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al actualizar la categoría.";
                $tipo_mensaje = 'danger';
            }
        }
    } elseif ($_POST['action'] == 'eliminar') {
        $id_categoria = (int)$_POST['id_categoria'];
        $resultado = eliminar_categoria($id_categoria);
        $mensaje = $resultado['message'];
        $tipo_mensaje = $resultado['success'] ? 'success' : 'danger';
    }
}

$categorias = obtener_todas_categorias();

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
                        <a class="nav-link active" href="categorias.php">
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
                    <h1 class="h2 mb-0"><i class="bi bi-tags"></i> Gestión de Categorías</h1>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-plus-circle"></i> Nueva Categoría
                </button>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabla de Categorías -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista de Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($categorias->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Productos</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($categoria = $categorias->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $categoria['id_categoria']; ?></strong></td>
                                            <td><strong><?php echo htmlspecialchars($categoria['nombre']); ?></strong></td>
                                            <td><?php echo htmlspecialchars(substr($categoria['descripcion'] ?? 'Sin descripción', 0, 50)); ?>...</td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $categoria['total_productos']; ?> producto<?php echo $categoria['total_productos'] != 1 ? 's' : ''; ?></span>
                                            </td>
                                            <td><small><?php echo date('d/m/Y', strtotime($categoria['fecha_creacion'])); ?></small></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditar<?php echo $categoria['id_categoria']; ?>">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </button>
                                                
                                                <?php if ($categoria['total_productos'] == 0): ?>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                                        <input type="hidden" name="action" value="eliminar">
                                                        <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="No se puede eliminar porque tiene productos">
                                                        <i class="bi bi-lock"></i> Bloqueada
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Modal Editar -->
                                        <div class="modal fade" id="modalEditar<?php echo $categoria['id_categoria']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <div class="modal-header bg-warning">
                                                            <h5 class="modal-title">Editar Categoría</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="editar">
                                                            <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Nombre *</label>
                                                                <input type="text" 
                                                                       name="nombre" 
                                                                       class="form-control" 
                                                                       value="<?php echo htmlspecialchars($categoria['nombre']); ?>"
                                                                       required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Descripción</label>
                                                                <textarea name="descripcion" 
                                                                          class="form-control" 
                                                                          rows="3"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-warning">
                                                                <i class="bi bi-save"></i> Guardar Cambios
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No hay categorías registradas</h5>
                            <p class="text-muted">Comienza agregando tu primera categoría</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Agregar Nueva Categoría -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nueva Categoría</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="agregar">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" 
                               name="nombre" 
                               class="form-control" 
                               placeholder="Ej: Electrónica, Ropa, Deportes..."
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Describe brevemente esta categoría..."></textarea>
                        <small class="text-muted">Opcional</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Categoría
                    </button>
                </div>
            </form>
        </div>
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
