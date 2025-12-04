<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Obtener categorías
$categorias_sql = "SELECT * FROM categorias ORDER BY nombre ASC";
$categorias_result = $conn->query($categorias_sql);

// Obtener filtros
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';

// Construir consulta
$sql = "SELECT * FROM productos WHERE estado = 'activo'";

if ($categoria_id > 0) {
    $sql .= " AND id_categoria = $categoria_id";
}

if ($search) {
    $sql .= " AND (nombre LIKE '%$search%' OR descripcion LIKE '%$search%')";
}

// Ordenamiento
switch($orden) {
    case 'precio_asc':
        $sql .= " ORDER BY precio ASC";
        break;
    case 'precio_desc':
        $sql .= " ORDER BY precio DESC";
        break;
    case 'nombre_desc':
        $sql .= " ORDER BY nombre DESC";
        break;
    default:
        $sql .= " ORDER BY nombre ASC";
}

$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Nuestros Productos</h1>
    
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['cart_message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['cart_message']; 
            unset($_SESSION['cart_message']);
            unset($_SESSION['cart_message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Sidebar de filtros -->
        <div class="col-md-3">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="productos.php">
                        <!-- Búsqueda -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Buscar</label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Nombre del producto..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- Categorías -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="0">Todas las categorías</option>
                                <?php 
                                $categorias_result->data_seek(0); // Reset pointer
                                while($cat = $categorias_result->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cat['id_categoria']; ?>"
                                            <?php echo $categoria_id == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Ordenamiento -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ordenar por</label>
                            <select name="orden" class="form-select">
                                <option value="nombre_asc" <?php echo $orden == 'nombre_asc' ? 'selected' : ''; ?>>
                                    Nombre (A-Z)
                                </option>
                                <option value="nombre_desc" <?php echo $orden == 'nombre_desc' ? 'selected' : ''; ?>>
                                    Nombre (Z-A)
                                </option>
                                <option value="precio_asc" <?php echo $orden == 'precio_asc' ? 'selected' : ''; ?>>
                                    Precio (Menor a Mayor)
                                </option>
                                <option value="precio_desc" <?php echo $orden == 'precio_desc' ? 'selected' : ''; ?>>
                                    Precio (Mayor a Menor)
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-check-circle"></i> Aplicar Filtros
                        </button>
                        <a href="productos.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <!-- Categorías como lista (alternativa visual) -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Categorías Rápidas</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="productos.php" 
                       class="list-group-item list-group-item-action <?php echo $categoria_id == 0 ? 'active' : ''; ?>">
                        Todas
                    </a>
                    <?php 
                    $categorias_result->data_seek(0); // Reset pointer
                    while($cat = $categorias_result->fetch_assoc()): 
                    ?>
                        <a href="productos.php?categoria=<?php echo $cat['id_categoria']; ?>" 
                           class="list-group-item list-group-item-action <?php echo $categoria_id == $cat['id_categoria'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Grid de productos -->
        <div class="col-md-9">
            <!-- Información de resultados -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">
                    <strong><?php echo $result->num_rows; ?></strong> productos encontrados
                    <?php if($search): ?>
                        para "<strong><?php echo htmlspecialchars($search); ?></strong>"
                    <?php endif; ?>
                </p>
            </div>

            <!-- Grid de productos -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm product-card border-0">
                                <div class="overflow-hidden position-relative">
                                    <?php if($row['imagen']): ?>
                                        <img src="assets/img/<?php echo htmlspecialchars($row['imagen']); ?>" 
                                             class="card-img-top" 
                                             alt="<?php echo htmlspecialchars($row['nombre']); ?>" 
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <span>Sin Imagen</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge de stock -->
                                    <?php if($row['stock'] < 10 && $row['stock'] > 0): ?>
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                                            ¡Últimas unidades!
                                        </span>
                                    <?php elseif($row['stock'] == 0): ?>
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                            Agotado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                                    <p class="card-text text-muted text-truncate">
                                        <?php echo htmlspecialchars(substr($row['descripcion'], 0, 60)) . '...'; ?>
                                    </p>
                                    <h4 class="product-price mb-3">$<?php echo number_format($row['precio'], 2); ?></h4>
                                    
                                    <div class="mt-auto">
                                        <?php if($row['stock'] > 0): ?>
                                            <form action="carrito.php" method="POST" class="mb-2">
                                                <input type="hidden" name="action" value="add">
                                                <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text bg-light border-0">Cant.</span>
                                                    <input type="number" 
                                                           name="cantidad" 
                                                           class="form-control text-center" 
                                                           value="1" 
                                                           min="1" 
                                                           max="<?php echo $row['stock']; ?>">
                                                    <button class="btn btn-success" type="submit">
                                                        <i class="bi bi-cart-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                        <a href="producto.php?id=<?php echo $row['id_producto']; ?>" 
                                           class="btn btn-outline-secondary w-100">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">No se encontraron productos</h4>
                            <p>Intenta con otros filtros o búsqueda.</p>
                            <a href="productos.php" class="btn btn-primary">Ver todos los productos</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
