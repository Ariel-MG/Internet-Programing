<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

start_session_safe();
requerir_admin();

$mensaje = '';
$tipo_mensaje = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'cambiar_rol') {
        $id_usuario = (int)$_POST['id_usuario'];
        $nuevo_rol = $_POST['rol'];
        
        if (actualizar_rol_usuario($id_usuario, $nuevo_rol)) {
            $mensaje = "Rol actualizado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al actualizar el rol.";
            $tipo_mensaje = 'danger';
        }
    } elseif ($_POST['action'] == 'eliminar') {
        $id_usuario = (int)$_POST['id_usuario'];
        
        if (eliminar_usuario($id_usuario)) {
            $mensaje = "Usuario eliminado correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al eliminar el usuario. No puedes eliminar tu propia cuenta.";
            $tipo_mensaje = 'danger';
        }
    }
}

$usuarios = obtener_todos_usuarios();

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
                        <a class="nav-link active" href="usuarios.php">
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
                    <h1 class="h2 mb-0"><i class="bi bi-people"></i> Gestión de Usuarios</h1>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabla de Usuarios -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lista de Usuarios Registrados</h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($usuarios->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($usuario = $usuarios->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $usuario['id_usuario']; ?></strong></td>
                                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="cambiar_rol">
                                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                                    <select name="rol" 
                                                            class="form-select form-select-sm" 
                                                            onchange="if(confirm('¿Cambiar rol de este usuario?')) this.form.submit();"
                                                            <?php echo $usuario['id_usuario'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                        <option value="cliente" <?php echo $usuario['tipo_usuario'] == 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                                                        <option value="admin" <?php echo $usuario['tipo_usuario'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td><small><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></small></td>
                                            <td>
                                                <?php if ($usuario['id_usuario'] != $_SESSION['user_id']): ?>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.');">
                                                        <input type="hidden" name="action" value="eliminar">
                                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Tu cuenta</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-5">
                            <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">No hay usuarios registrados</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Información</h6>
                        <ul class="mb-0">
                            <li><strong>Cliente:</strong> Puede comprar productos y dejar reseñas.</li>
                            <li><strong>Admin:</strong> Tiene acceso completo al panel de administración.</li>
                            <li>No puedes eliminar tu propia cuenta ni cambiar tu propio rol.</li>
                        </ul>
                    </div>
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
