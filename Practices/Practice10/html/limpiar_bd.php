<?php
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';

// Verificar si se confirmó la eliminación
if (isset($_POST['confirmar_eliminacion']) && $_POST['confirmar_eliminacion'] === 'SI_ELIMINAR_TODO') {
    try {
        // Eliminar todos los registros de la tabla libros
        $sql = "DELETE FROM libros";
        $resultado = $conexion->query($sql);
        
        if ($resultado) {
            $registros_eliminados = $conexion->affected_rows;
            
            // Reiniciar el AUTO_INCREMENT para que los nuevos IDs empiecen desde 1
            $sql_reset = "ALTER TABLE libros AUTO_INCREMENT = 1";
            $conexion->query($sql_reset);
            
            $mensaje = "Se eliminaron exitosamente $registros_eliminados registros de libros. Los nuevos registros comenzarán con ID = 1.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al eliminar los registros: " . $conexion->error;
            $tipo_mensaje = 'danger';
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = 'danger';
    }
} elseif (isset($_POST['confirmar_eliminacion'])) {
    $mensaje = "Eliminación cancelada. Debes escribir exactamente 'SI_ELIMINAR_TODO' para confirmar.";
    $tipo_mensaje = 'warning';
}

// Obtener el conteo actual de libros
$sql_count = "SELECT COUNT(*) as total FROM libros";
$resultado_count = $conexion->query($sql_count);
$total_libros = 0;
if ($resultado_count) {
    $fila = $resultado_count->fetch_assoc();
    $total_libros = $fila['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limpiar Base de Datos - Librería</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-book"></i> Librería - Administración
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registro.php">
                            <i class="bi bi-plus-circle"></i> Registrar Libro
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consulta.php">
                            <i class="bi bi-search"></i> Consultar Libros
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Título de la página -->
                <div class="text-center mb-4">
                    <h1 class="display-5 text-danger">
                        <i class="bi bi-trash"></i> Limpiar Base de Datos
                    </h1>
                    <p class="lead text-muted">
                        Eliminar todos los registros de libros existentes
                    </p>
                </div>

                <!-- Mostrar mensajes -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Información actual -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Estado Actual de la Base de Datos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <h3 class="text-primary"><?php echo $total_libros; ?></h3>
                                <p class="text-muted">Libros Registrados</p>
                            </div>
                            <div class="col-md-6">
                                <h3 class="text-success">
                                    <?php echo $total_libros > 0 ? 'Activa' : 'Vacía'; ?>
                                </h3>
                                <p class="text-muted">Estado de la Base de Datos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($total_libros > 0): ?>
                <!-- Formulario de eliminación -->
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle"></i>ZONA PELIGROSA
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading">
                                <i class="bi bi-exclamation-triangle"></i> ¡ATENCIÓN!
                            </h6>
                            <p>Esta acción eliminará <strong>TODOS</strong> los registros de libros de forma <strong>PERMANENTE</strong>.</p>
                            <ul class="mb-0">
                                <li>Se perderán todas las imágenes almacenadas</li>
                                <li>Se perderá toda la información de libros</li>
                                <li>Los IDs se reiniciarán desde 1</li>
                                <li><strong>Esta acción NO se puede deshacer</strong></li>
                            </ul>
                        </div>

                        <form method="POST" onsubmit="return confirmarEliminacion()">
                            <div class="mb-3">
                                <label for="confirmar_eliminacion" class="form-label">
                                    <strong>Para confirmar, escribe exactamente:</strong> <code>SI_ELIMINAR_TODO</code>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="confirmar_eliminacion" 
                                    name="confirmar_eliminacion" 
                                    placeholder="Escribe: SI_ELIMINAR_TODO"
                                    required
                                >
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="consulta.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash-fill"></i> ELIMINAR TODOS LOS REGISTROS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <!-- Base de datos ya está vacía -->
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-circle"></i> Base de Datos Limpia
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-database-check text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-success">¡La base de datos ya está vacía!</h4>
                        <p class="text-muted">No hay libros registrados. Puedes comenzar a agregar nuevos libros.</p>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="registro.php" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Registrar Primer Libro
                            </a>
                            <a href="index.html" class="btn btn-primary">
                                <i class="bi bi-house"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Enlaces útiles -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Enlaces Útiles</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="consulta.php" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-list"></i> Ver Libros
                                    </a>
                                    <a href="registro.php" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-plus-circle"></i> Registrar Libro
                                    </a>
                                    <a href="index.html" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-house"></i> Inicio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; 2025 Sistema de Librería - Administración de Base de Datos
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmarEliminacion() {
            const confirmacion = document.getElementById('confirmar_eliminacion').value;
            
            if (confirmacion !== 'SI_ELIMINAR_TODO') {
                alert('Debes escribir exactamente "SI_ELIMINAR_TODO" para confirmar la eliminación.');
                return false;
            }
            
            return confirm('¿Estás ABSOLUTAMENTE SEGURO de que quieres eliminar TODOS los registros?\n\nEsta acción NO se puede deshacer.');
        }
    </script>
</body>
</html>

<?php
$conexion->close();
?>