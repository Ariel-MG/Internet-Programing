<?php
require_once 'config.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $autor = trim($_POST['autor']);
    $titulo = trim($_POST['titulo']);
    $fecha_publicacion = $_POST['fecha_publicacion'];
    
    // Validaciones básicas
    if (empty($autor) || empty($titulo) || empty($fecha_publicacion)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'danger';
    } else {
        // Procesar imagen si se subió
        $imagen_blob = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            // Verificar que sea una imagen válida
            $tipo_archivo = $_FILES['imagen']['type'];
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($tipo_archivo, $tipos_permitidos)) {
                $imagen_blob = file_get_contents($_FILES['imagen']['tmp_name']);
            } else {
                $mensaje = 'Por favor, sube solo archivos de imagen (JPEG, PNG, GIF, WebP).';
                $tipo_mensaje = 'danger';
            }
        }
        
        if ($tipo_mensaje !== 'danger') {
            // Insertar en la base de datos usando mysqli
            $sql = "INSERT INTO libros (autor, titulo, fecha_publicacion, imagen) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            
            if ($stmt) {
                if ($imagen_blob !== null) {
                    // Para datos BLOB, usar bind_param con tipo 'b'
                    $null = NULL;
                    $stmt->bind_param("sssb", $autor, $titulo, $fecha_publicacion, $null);
                    $stmt->send_long_data(3, $imagen_blob);
                } else {
                    // Sin imagen
                    $stmt->bind_param("ssss", $autor, $titulo, $fecha_publicacion, $imagen_blob);
                }
                
                if ($stmt->execute()) {
                    $mensaje = 'Libro registrado exitosamente.';
                    $tipo_mensaje = 'success';
                    
                    // Limpiar formulario
                    $autor = $titulo = $fecha_publicacion = '';
                } else {
                    $mensaje = 'Error al registrar el libro: ' . $stmt->error;
                    $tipo_mensaje = 'danger';
                }
                
                $stmt->close();
            } else {
                $mensaje = 'Error en la preparación de la consulta: ' . $conexion->error;
                $tipo_mensaje = 'danger';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Libro - Librería</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
                        <a class="nav-link active" href="registro.php">
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
                    <h1 class="display-5 text-primary">
                        <i class="bi bi-plus-circle"></i> Registrar Nuevo Libro
                    </h1>
                    <p class="lead text-muted">
                        Completa el formulario para agregar un libro a la biblioteca
                    </p>
                </div>

                <!-- Mostrar mensajes -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?php echo $tipo_mensaje == 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($mensaje); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de registro -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-form"></i> Información del Libro
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="autor" class="form-label">
                                        <i class="bi bi-person"></i> Autor *
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="autor" 
                                        name="autor" 
                                        value="<?php echo isset($autor) ? htmlspecialchars($autor) : ''; ?>"
                                        placeholder="Ingresa el nombre del autor"
                                        required
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="titulo" class="form-label">
                                        <i class="bi bi-book"></i> Título *
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="titulo" 
                                        name="titulo" 
                                        value="<?php echo isset($titulo) ? htmlspecialchars($titulo) : ''; ?>"
                                        placeholder="Ingresa el título del libro"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_publicacion" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha de Publicación *
                                    </label>
                                    <input 
                                        type="date" 
                                        class="form-control" 
                                        id="fecha_publicacion" 
                                        name="fecha_publicacion"
                                        value="<?php echo isset($fecha_publicacion) ? htmlspecialchars($fecha_publicacion) : ''; ?>"
                                        required
                                    >
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="imagen" class="form-label">
                                        <i class="bi bi-image"></i> Imagen de Portada
                                    </label>
                                    <input 
                                        type="file" 
                                        class="form-control" 
                                        id="imagen" 
                                        name="imagen"
                                        accept="image/*"
                                    >
                                    <div class="form-text">
                                        Formatos permitidos: JPG, PNG, GIF (opcional)
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.html" class="btn btn-secondary me-md-2">
                                            <i class="bi bi-arrow-left"></i> Volver
                                        </a>
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Registrar Libro
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <h6 class="alert-heading">
                                <i class="bi bi-lightbulb"></i> Consejos para el registro
                            </h6>
                            <ul class="mb-0">
                                <li>Todos los campos marcados con (*) son obligatorios</li>
                                <li>La imagen de portada es opcional pero recomendada</li>
                                <li>La fecha debe ser la fecha real de publicación del libro</li>
                                <li>Puedes consultar todos los libros registrados en la sección "Consultar Libros"</li>
                            </ul>
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
                &copy; 2025 Sistema de Librería - Práctica 10
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>