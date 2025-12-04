<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda - Proyecto Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <?php
    // Determinar la ruta correcta para CSS según la ubicación
    $css_path = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') ? '../assets/css/styles.css' : 'assets/css/styles.css';
    ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>">
</head>
<body>
<?php
// Determinar si estamos en admin para ajustar rutas
$is_admin = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$base_path = $is_admin ? '../' : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container<?php echo $is_admin ? '-fluid' : ''; ?>">
        <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">Mi Tienda</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>index.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>productos.php">Productos</a></li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>mis_pedidos.php"><i class="bi bi-clock-history"></i> Mis Pedidos</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>carrito.php">
                        <i class="bi bi-cart"></i> Carrito 
                        <?php 
                        $cart_count = 0;
                        if (is_logged_in()) {
                            $cart_count = contar_items_carrito($_SESSION['user_id']);
                        }
                        ?>
                        <span class="badge bg-secondary"><?php echo $cart_count; ?></span>
                    </a>
                </li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> Hola, <?php echo get_user_name(); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo $is_admin ? 'index.php' : 'admin/index.php'; ?>">
                                    <i class="bi bi-speedometer2"></i> Panel Admin
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>logout.php">
                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>registro.php">Registro</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>