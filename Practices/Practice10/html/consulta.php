<?php
require_once 'config.php';

// Obtener todos los libros de la base de datos
$sql = "SELECT id, autor, titulo, fecha_publicacion, imagen, fecha_registro FROM libros ORDER BY fecha_registro DESC";
$resultado = $conexion->query($sql);

$libros = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $libros[] = $fila;
    }
} else {
    $error = 'Error al consultar los libros: ' . $conexion->error;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Libros - Librería</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .libro-imagen {
            max-width: 100px;
            max-height: 150px;
            object-fit: cover;
        }
        .card-libro {
            transition: transform 0.2s;
        }
        .card-libro:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-book"></i> Librería
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
                        <a class="nav-link active" href="consulta.php">
                            <i class="bi bi-search"></i> Consultar Libros
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <!-- Título de la página -->
        <div class="text-center mb-4">
            <h1 class="display-5 text-primary">
                <i class="bi bi-search"></i> Biblioteca de Libros
            </h1>
            <p class="lead text-muted">
                Explora todos los libros registrados en el sistema
            </p>
        </div>

        <!-- Mostrar error si existe -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-book-fill text-primary me-2" style="font-size: 2rem;"></i>
                                    <div>
                                        <h4 class="mb-0"><?php echo count($libros); ?></h4>
                                        <small class="text-muted">Libros Registrados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-fill text-success me-2" style="font-size: 2rem;"></i>
                                    <div>
                                        <h4 class="mb-0"><?php echo count(array_unique(array_column($libros, 'autor'))); ?></h4>
                                        <small class="text-muted">Autores Únicos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-images text-info me-2" style="font-size: 2rem;"></i>
                                    <div>
                                        <h4 class="mb-0"><?php echo count(array_filter($libros, function($libro) { return !empty($libro['imagen']); })); ?></h4>
                                        <small class="text-muted">Con Portada</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Herramientas de administración -->
        <?php if (count($libros) > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-warning mb-1">
                                    <i class="bi bi-tools"></i> Herramientas de Administración
                                </h6>
                                <small class="text-muted">Gestiona la base de datos de libros</small>
                            </div>
                            <a href="limpiar_bd.php" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Limpiar Base de Datos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de libros -->
        <?php if (empty($libros)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-book text-muted" style="font-size: 5rem;"></i>
                </div>
                <h3 class="text-muted">No hay libros registrados</h3>
                <p class="text-muted">¡Comienza agregando tu primer libro!</p>
                <a href="registro.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Registrar Primer Libro
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($libros as $libro): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card card-libro h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-book"></i> Libro #<?php echo $libro['id']; ?>
                                </h6>
                            </div>
                            
                            <?php if (!empty($libro['imagen'])): ?>
                                <div class="text-center p-3">
                                    <img 
                                        src="imagen.php?id=<?php echo $libro['id']; ?>" 
                                        alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>"
                                        class="libro-imagen rounded shadow-sm"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                                    >
                                </div>
                            <?php else: ?>
                                <div class="text-center p-3">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 150px; margin: 0 auto;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <?php echo htmlspecialchars($libro['titulo']); ?>
                                </h5>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> Autor:
                                    </small>
                                    <div><?php echo htmlspecialchars($libro['autor']); ?></div>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> Publicación:
                                    </small>
                                    <div><?php echo date('d/m/Y', strtotime($libro['fecha_publicacion'])); ?></div>
                                </div>
                                
                                <div class="mb-0">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> Registrado:
                                    </small>
                                    <div>
                                        <small><?php echo date('d/m/Y H:i', strtotime($libro['fecha_registro'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Botón para agregar más libros -->
            <div class="text-center mt-4">
                <a href="registro.php" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Registrar Nuevo Libro
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; 2024 Sistema de Librería - Práctica 10
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>