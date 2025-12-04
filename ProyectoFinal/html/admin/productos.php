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
        $descripcion = trim(strip_tags($_POST['descripcion']));
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $id_categoria = intval($_POST['id_categoria']);
        $estado = $_POST['estado'];
        
        // Procesar imagen
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../assets/img/' . $new_filename;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                    $imagen = $new_filename;
                }
            }
        }
        
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, id_categoria, imagen, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisss", $nombre, $descripcion, $precio, $stock, $id_categoria, $imagen, $estado);
        
        if ($stmt->execute()) {
            $mensaje = "Producto agregado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al agregar el producto.";
            $tipo_mensaje = 'danger';
        }
        
    } elseif ($_POST['action'] == 'editar') {
        $id_producto = intval($_POST['id_producto']);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim(strip_tags($_POST['descripcion']));
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $id_categoria = intval($_POST['id_categoria']);
        $estado = $_POST['estado'];
        
        // Obtener imagen actual
        $sql = "SELECT imagen FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $imagen_actual = $stmt->get_result()->fetch_assoc()['imagen'];
        
        $imagen = $imagen_actual;
        
        // Procesar nueva imagen si se sube
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $upload_path = '../assets/img/' . $new_filename;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                    // Eliminar imagen anterior si existe
                    if ($imagen_actual && file_exists('../assets/img/' . $imagen_actual)) {
                        unlink('../assets/img/' . $imagen_actual);
                    }
                    $imagen = $new_filename;
                }
            }
        }
        
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, 
                id_categoria = ?, imagen = ?, estado = ? WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisssi", $nombre, $descripcion, $precio, $stock, $id_categoria, $imagen, $estado, $id_producto);
        
        if ($stmt->execute()) {
            $mensaje = "Producto actualizado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al actualizar el producto.";
            $tipo_mensaje = 'danger';
        }
        
    } elseif ($_POST['action'] == 'eliminar') {
        $id_producto = intval($_POST['id_producto']);
        
        // Obtener imagen para eliminarla
        $sql = "SELECT imagen FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $imagen = $stmt->get_result()->fetch_assoc()['imagen'];
        
        $sql = "DELETE FROM productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        
        if ($stmt->execute()) {
            // Eliminar imagen si existe
            if ($imagen && file_exists('../assets/img/' . $imagen)) {
                unlink('../assets/img/' . $imagen);
            }
            $mensaje = "Producto eliminado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al eliminar el producto.";
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener filtros
$filtro_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_imagen = isset($_GET['imagen']) ? $_GET['imagen'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Obtener productos
$sql = "SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        ORDER BY p.fecha_creacion DESC";
$productos = $conn->query($sql);

// Obtener categorías para los filtros y formularios
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");

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
                        <a class="nav-link active" href="productos.php">
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
                    <h1 class="h2 mb-0"><i class="bi bi-box-seam"></i> Gestión de Productos</h1>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                </button>
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
                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="0">Todas las categorías</option>
                                <?php 
                                $categorias->data_seek(0);
                                while($cat = $categorias->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cat['id_categoria']; ?>" 
                                            <?php echo $filtro_categoria == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos</option>
                                <option value="activo" <?php echo $filtro_estado == 'activo' ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo $filtro_estado == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Imagen</label>
                            <select name="imagen" class="form-select">
                                <option value="">Todas</option>
                                <option value="sin_foto" <?php echo $filtro_imagen == 'sin_foto' ? 'selected' : ''; ?>>Sin foto</option>
                                <option value="con_foto" <?php echo $filtro_imagen == 'con_foto' ? 'selected' : ''; ?>>Con foto</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="busqueda" class="form-control" 
                                   placeholder="Nombre del producto..." 
                                   value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista de Productos</h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($productos->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $productos->data_seek(0);
                                    while($producto = $productos->fetch_assoc()): 
                                        // Aplicar filtros
                                        if ($filtro_categoria > 0 && $producto['id_categoria'] != $filtro_categoria) continue;
                                        if ($filtro_estado && $producto['estado'] != $filtro_estado) continue;
                                        if ($filtro_imagen == 'sin_foto' && !empty($producto['imagen'])) continue;
                                        if ($filtro_imagen == 'con_foto' && empty($producto['imagen'])) continue;
                                        if ($busqueda && stripos($producto['nombre'], $busqueda) === false) continue;
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $producto['id_producto']; ?></strong></td>
                                            <td>
                                                <?php if($producto['imagen']): ?>
                                                    <img src="../assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                                         alt="" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         class="rounded">
                                                <?php else: ?>
                                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                                                         style="width: 50px; height: 50px;">
                                                        <small>N/A</small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                                            <td><strong>$<?php echo number_format($producto['precio'], 2); ?></strong></td>
                                            <td>
                                                <?php if($producto['stock'] <= 0): ?>
                                                    <span class="badge bg-danger"><?php echo $producto['stock']; ?></span>
                                                <?php elseif($producto['stock'] < 10): ?>
                                                    <span class="badge bg-warning"><?php echo $producto['stock']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $producto['stock']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $producto['estado'] == 'activo' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($producto['estado']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditar<?php echo $producto['id_producto']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('¿Eliminar este producto?');">
                                                    <input type="hidden" name="action" value="eliminar">
                                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Editar -->
                                        <div class="modal fade" id="modalEditar<?php echo $producto['id_producto']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <div class="modal-header bg-warning">
                                                            <h5 class="modal-title">Editar Producto</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="editar">
                                                            <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Nombre *</label>
                                                                    <input type="text" name="nombre" class="form-control" 
                                                                           value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Categoría *</label>
                                                                    <select name="id_categoria" class="form-select" required>
                                                                        <option value="">Selecciona...</option>
                                                                        <?php 
                                                                        $categorias->data_seek(0);
                                                                        while($cat = $categorias->fetch_assoc()): 
                                                                        ?>
                                                                            <option value="<?php echo $cat['id_categoria']; ?>"
                                                                                    <?php echo $producto['id_categoria'] == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                                                                <?php echo htmlspecialchars($cat['nombre']); ?>
                                                                            </option>
                                                                        <?php endwhile; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Descripción</label>
                                                                <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Precio *</label>
                                                                    <input type="number" name="precio" class="form-control" 
                                                                           step="0.01" min="0" 
                                                                           value="<?php echo $producto['precio']; ?>" required>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Stock *</label>
                                                                    <input type="number" name="stock" class="form-control" 
                                                                           min="0" 
                                                                           value="<?php echo $producto['stock']; ?>" required>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Estado *</label>
                                                                    <select name="estado" class="form-select" required>
                                                                        <option value="activo" <?php echo $producto['estado'] == 'activo' ? 'selected' : ''; ?>>Activo</option>
                                                                        <option value="inactivo" <?php echo $producto['estado'] == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Imagen</label>
                                                                <?php if($producto['imagen']): ?>
                                                                    <div class="mb-2">
                                                                        <img src="../assets/img/<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                                                             alt="" 
                                                                             style="max-width: 200px;" 
                                                                             class="img-thumbnail">
                                                                    </div>
                                                                <?php endif; ?>
                                                                <input type="file" name="imagen" class="form-control" accept="image/*">
                                                                <small class="text-muted">Deja en blanco para mantener la imagen actual</small>
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
                            <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No hay productos registrados</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Agregar Nuevo Producto -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="agregar">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" 
                                   placeholder="Nombre del producto" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría *</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="">Selecciona una categoría...</option>
                                <?php 
                                $categorias->data_seek(0);
                                while($cat = $categorias->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cat['id_categoria']; ?>">
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" 
                                  placeholder="Describe el producto..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" class="form-control" 
                                       step="0.01" min="0" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock Inicial *</label>
                            <input type="number" name="stock" class="form-control" 
                                   min="0" value="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado *</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Imagen del Producto</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP (Opcional)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Agregar Producto
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
