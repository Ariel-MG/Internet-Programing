<?php
require_once 'includes/functions.php';
start_session_safe();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Proyecto Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Bienvenido a Mi Tienda</h1>
                <p class="col-md-8 fs-4">Explora nuestros productos y encuentra las mejores ofertas.</p>
                <?php if (is_logged_in()): ?>
                    <p>Hola, <strong><?php echo get_user_name(); ?></strong>. ¡Qué bueno verte de nuevo!</p>
                    <a class="btn btn-primary btn-lg" href="productos.php" role="button">Ver Productos</a>
                <?php else: ?>
                    <p>Regístrate para comenzar a comprar.</p>
                    <a class="btn btn-primary btn-lg" href="registro.php" role="button">Registrarse</a>
                    <a class="btn btn-outline-secondary btn-lg" href="login.php" role="button">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
