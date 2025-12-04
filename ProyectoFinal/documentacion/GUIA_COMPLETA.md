# Guía Completa del Proyecto E-commerce

Esta guía documenta completamente el proyecto para facilitar el desarrollo y mantenimiento.

---

## 📋 Tabla de Contenidos

1. [Arquitectura del Proyecto](#arquitectura-del-proyecto)
2. [Configuración y Conexión](#configuración-y-conexión)
3. [Sistema de Autenticación](#sistema-de-autenticación)
4. [Sistema de Carrito](#sistema-de-carrito)
5. [Estructura de Páginas](#estructura-de-páginas)
6. [Funciones Reutilizables](#funciones-reutilizables)
7. [Estilos y Diseño](#estilos-y-diseño)
8. [Base de Datos](#base-de-datos)
9. [Cómo Implementar Cambios](#cómo-implementar-cambios)

---

## 🏗️ Arquitectura del Proyecto

### Patrón de Diseño

El proyecto sigue un patrón **MVC simplificado**:

- **Modelo**: Funciones en `includes/functions.php` + consultas SQL
- **Vista**: Archivos PHP con HTML (productos.php, carrito.php, etc.)
- **Controlador**: Lógica PHP en cada página + funciones helper

### Estructura de Archivos

```
ProyectoFinal/
├── docker-compose.yml          # Configuración Docker
├── mysql_data/                 # Datos persistentes MySQL
├── documentacion/              # Documentación del proyecto
│   ├── ESTRUCTURA.md
│   └── GUIA_COMPLETA.md
└── html/                       # CÓDIGO FUENTE
    ├── config/
    │   └── db.php              # Conexión a base de datos
    ├── includes/
    │   ├── header.php          # Encabezado HTML + Navbar
    │   ├── footer.php          # Pie de página + Scripts
    │   └── functions.php       # Funciones reutilizables
    ├── assets/
    │   ├── css/
    │   │   └── styles.css      # Estilos personalizados
    │   ├── img/                # Imágenes de productos
    │   └── js/
    │       └── main.js         # JavaScript personalizado
    ├── admin/                  # Panel de administración
    │   ├── index.php           # Dashboard admin
    │   ├── productos.php       # Gestión de productos
    │   └── agregar.php         # Agregar productos
    ├── index.php               # Página de inicio
    ├── login.php               # Inicio de sesión
    ├── registro.php            # Registro de usuarios
    ├── productos.php           # Catálogo de productos
    ├── carrito.php             # Carrito de compras
    ├── checkout.php            # Proceso de pago
    └── setup_db.php            # Inicializar base de datos
```

---

## ⚙️ Configuración y Conexión

### Docker Compose

**Archivo**: `docker-compose.yml`

```yaml
services:
  web:
    image: php:8.2-apache
    ports: ["8080:80"]
    volumes: ["./html:/var/www/html"]
  
  db:
    image: mysql:8.1.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: bd
      MYSQL_USER: amg
      MYSQL_PASSWORD: amg
    volumes: ["./mysql_data:/var/lib/mysql"]
  
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports: ["8081:80"]
    environment:
      PMA_HOST: db
```

**Comandos**:
```bash
# Iniciar servicios
docker-compose up -d

# Detener servicios
docker-compose down

# Ver logs
docker-compose logs -f web
```

**Accesos**:
- Sitio web: http://localhost:8080
- PhpMyAdmin: http://localhost:8081

### Conexión a Base de Datos

**Archivo**: `html/config/db.php`

```php
<?php
$host = 'db';              // Nombre del servicio en docker-compose
$user = 'amg';             // Usuario MySQL
$password = 'amg';         // Contraseña MySQL
$database = 'ecomerce';    // Nombre de la base de datos

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    throw new Exception("Error de conexión: " . $e->getMessage());
}
?>
```

**Uso en páginas**:
```php
require_once 'config/db.php';
// Ahora $conn está disponible para consultas
```

---

## 🔐 Sistema de Autenticación

### Flujo de Autenticación

1. **Registro** (`registro.php`):
   - Usuario completa formulario
   - Validación de contraseñas coincidentes
   - Verificación de email único
   - Hash de contraseña con `password_hash()`
   - Inserción en tabla `usuarios`

2. **Login** (`login.php`):
   - Usuario ingresa email y contraseña
   - Búsqueda en base de datos
   - Verificación con `password_verify()`
   - Creación de sesión con datos del usuario

3. **Sesión**:
   ```php
   $_SESSION['user_id']    // ID del usuario
   $_SESSION['user_name']  // Nombre completo
   $_SESSION['user_role']  // 'cliente' o 'admin'
   ```

### Funciones de Autenticación

**Archivo**: `includes/functions.php`

```php
// Iniciar sesión de forma segura
function start_session_safe() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verificar si el usuario está logueado
function is_logged_in() {
    start_session_safe();
    return isset($_SESSION['user_id']);
}

// Obtener nombre del usuario
function get_user_name() {
    start_session_safe();
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Invitado';
}

// Redireccionar
function redirect($url) {
    header("Location: $url");
    exit();
}
```

### Proteger Páginas

```php
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Verificar login
if (!is_logged_in()) {
    $_SESSION['flash_message'] = "Debes iniciar sesión.";
    redirect('login.php');
}

// Verificar rol admin
if ($_SESSION['user_role'] !== 'admin') {
    redirect('index.php');
}
?>
```

---

## 🛒 Sistema de Carrito

### Funciones del Carrito

**Archivo**: `includes/functions.php`

#### 1. Obtener Carrito
```php
function obtener_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT c.id_carrito, c.cantidad, c.precio_unitario, 
                   p.nombre, p.imagen, p.id_producto 
            FROM carrito_compras c
            JOIN productos p ON c.id_producto = p.id_producto
            WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result();
}
```

#### 2. Agregar al Carrito
```php
function agregar_al_carrito($id_usuario, $id_producto, $cantidad) {
    global $conn;
    
    // Verificar si ya existe
    $check_sql = "SELECT id_carrito, cantidad FROM carrito_compras 
                  WHERE id_usuario = ? AND id_producto = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $id_usuario, $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar cantidad
        $row = $result->fetch_assoc();
        $new_quantity = $row['cantidad'] + $cantidad;
        $update_sql = "UPDATE carrito_compras SET cantidad = ? 
                       WHERE id_carrito = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $row['id_carrito']);
        return $update_stmt->execute();
    } else {
        // Insertar nuevo
        $price_sql = "SELECT precio FROM productos WHERE id_producto = ?";
        $price_stmt = $conn->prepare($price_sql);
        $price_stmt->bind_param("i", $id_producto);
        $price_stmt->execute();
        $price_res = $price_stmt->get_result();
        
        if ($price_res->num_rows > 0) {
            $price = $price_res->fetch_assoc()['precio'];
            $insert_sql = "INSERT INTO carrito_compras 
                          (id_usuario, id_producto, cantidad, precio_unitario) 
                          VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iiid", $id_usuario, $id_producto, 
                                     $cantidad, $price);
            return $insert_stmt->execute();
        }
    }
    return false;
}
```

#### 3. Otras Funciones
```php
// Eliminar un item
function eliminar_del_carrito($id_carrito) {
    global $conn;
    $sql = "DELETE FROM carrito_compras WHERE id_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carrito);
    return $stmt->execute();
}

// Vaciar carrito completo
function vaciar_carrito($id_usuario) {
    global $conn;
    $sql = "DELETE FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    return $stmt->execute();
}

// Calcular total
function total_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT SUM(cantidad * precio_unitario) as total 
            FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ? $row['total'] : 0;
}

// Contar items
function contar_items_carrito($id_usuario) {
    global $conn;
    $sql = "SELECT SUM(cantidad) as total_items 
            FROM carrito_compras WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_items'] ? $row['total_items'] : 0;
}
```

### Flujo del Carrito

1. **Agregar Producto** (desde `productos.php`):
   ```php
   <form action="carrito.php" method="POST">
       <input type="hidden" name="action" value="add">
       <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
       <input type="number" name="cantidad" value="1" min="1">
       <button type="submit">Agregar</button>
   </form>
   ```

2. **Procesar en carrito.php**:
   ```php
   if ($_POST['action'] == 'add') {
       $id_producto = $_POST['id_producto'];
       $cantidad = $_POST['cantidad'];
       agregar_al_carrito($id_usuario, $id_producto, $cantidad);
   }
   ```

3. **Mostrar Carrito**:
   ```php
   $cart_items = obtener_carrito($id_usuario);
   while ($item = $cart_items->fetch_assoc()) {
       // Mostrar cada producto
   }
   ```

---

## 📄 Estructura de Páginas

### Patrón Estándar de Página

**IMPORTANTE**: Todas las páginas deben seguir esta estructura:

```php
<?php
// 1. Incluir configuración y funciones
require_once 'config/db.php';
require_once 'includes/functions.php';

// 2. Lógica de la página (opcional)
start_session_safe();
// ... código PHP ...

// 3. Incluir header (abre HTML, head, body)
include 'includes/header.php';
?>

<!-- 4. Contenido de la página -->
<main class="container mt-5">
    <h1>Título de la Página</h1>
    <!-- Contenido HTML -->
</main>

<?php
// 5. Incluir footer (cierra body y html)
include 'includes/footer.php';
?>
```

### Header (`includes/header.php`)

**Contenido**:
```php
<?php require_once 'includes/functions.php'; start_session_safe(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda - Proyecto Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <!-- Navegación -->
</nav>
```

**Características**:
- Inicia sesión automáticamente
- Incluye Bootstrap 5.3.0
- Incluye styles.css personalizado
- Navbar con contador de carrito dinámico
- Menú de usuario (login/logout)

### Footer (`includes/footer.php`)

**Contenido**:
```php
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> Mi Tienda Online. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
```

**Características**:
- Footer sticky (siempre al final)
- Bootstrap JS para componentes interactivos
- main.js para JavaScript personalizado

---

## 🔧 Funciones Reutilizables

### Archivo: `includes/functions.php`

| Función | Descripción | Parámetros | Retorno |
|---------|-------------|------------|---------|
| `start_session_safe()` | Inicia sesión si no está iniciada | - | void |
| `is_logged_in()` | Verifica si hay usuario logueado | - | bool |
| `get_user_name()` | Obtiene nombre del usuario | - | string |
| `redirect($url)` | Redirecciona a otra página | url: string | void |
| `obtener_carrito($id_usuario)` | Obtiene items del carrito | id_usuario: int | mysqli_result |
| `agregar_al_carrito($id_usuario, $id_producto, $cantidad)` | Agrega producto al carrito | id_usuario: int, id_producto: int, cantidad: int | bool |
| `eliminar_del_carrito($id_carrito)` | Elimina item del carrito | id_carrito: int | bool |
| `vaciar_carrito($id_usuario)` | Vacía todo el carrito | id_usuario: int | bool |
| `total_carrito($id_usuario)` | Calcula total del carrito | id_usuario: int | float |
| `contar_items_carrito($id_usuario)` | Cuenta items en carrito | id_usuario: int | int |

---

## 🎨 Estilos y Diseño

### Variables CSS

**Archivo**: `assets/css/styles.css`

```css
:root {
    --color-bg: #F2EBE5;        /* Fondo general */
    --color-primary: #647295;    /* Color primario (navbar, botones) */
    --color-accent: #9F496E;     /* Color de acento (precios, enlaces) */
    --color-text: #2B262D;       /* Color de texto */
}
```

### Clases Personalizadas

```css
/* Tarjetas de producto con hover */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: white;
    border-radius: 10px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

/* Precio de producto */
.product-price {
    color: var(--color-accent);
    font-weight: bold;
    font-size: 1.5rem;
}
```

### Bootstrap Overrides

```css
/* Navbar */
.navbar {
    background-color: var(--color-primary) !important;
}

/* Botones */
.btn-primary {
    background-color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn-secondary {
    background-color: var(--color-accent);
    border-color: var(--color-accent);
}
```

---

## 🗄️ Base de Datos

### Esquema Completo

#### Tabla: `usuarios`
```sql
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    tipo_usuario ENUM('cliente', 'admin') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabla: `categorias`
```sql
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabla: `productos`
```sql
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    id_categoria INT,
    imagen VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
);
```

#### Tabla: `carrito_compras`
```sql
CREATE TABLE carrito_compras (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_producto INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);
```

#### Tabla: `pedidos`
```sql
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'procesando', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    metodo_pago VARCHAR(50),
    direccion_entrega TEXT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);
```

#### Tabla: `detalle_pedidos`
```sql
CREATE TABLE detalle_pedidos (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_producto INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE SET NULL
);
```

### Inicializar Base de Datos

**Opción 1**: Ejecutar script
```bash
# Visitar en el navegador
http://localhost:8080/setup_db.php
```

**Opción 2**: PhpMyAdmin
```bash
# Acceder a PhpMyAdmin
http://localhost:8081
# Usuario: amg
# Contraseña: amg
```

---

## 🛠️ Cómo Implementar Cambios

### 1. Crear una Nueva Página

```php
<?php
// Paso 1: Incluir archivos necesarios
require_once 'config/db.php';
require_once 'includes/functions.php';

// Paso 2: Lógica de la página
start_session_safe();

// Ejemplo: Obtener datos
$sql = "SELECT * FROM productos WHERE estado = 'activo'";
$result = $conn->query($sql);

// Paso 3: Incluir header
include 'includes/header.php';
?>

<!-- Paso 4: HTML de la página -->
<div class="container mt-5">
    <h1>Mi Nueva Página</h1>
    
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
        </div>
    <?php endwhile; ?>
</div>

<?php
// Paso 5: Incluir footer
include 'includes/footer.php';
?>
```

### 2. Agregar una Nueva Función

**Archivo**: `includes/functions.php`

```php
/**
 * Descripción de la función
 * @param tipo $parametro Descripción del parámetro
 * @return tipo Descripción del retorno
 */
function mi_nueva_funcion($parametro) {
    global $conn; // Si necesitas la conexión
    
    // Lógica de la función
    $sql = "SELECT * FROM tabla WHERE campo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $parametro);
    $stmt->execute();
    
    return $stmt->get_result();
}
```

### 3. Modificar Estilos

**Archivo**: `assets/css/styles.css`

```css
/* Agregar al final del archivo */
.mi-nueva-clase {
    background-color: var(--color-primary);
    padding: 20px;
    border-radius: 10px;
}

/* Modificar clase existente */
.product-card {
    /* Agregar o modificar propiedades */
    border: 2px solid var(--color-accent);
}
```

### 4. Agregar Validación de Formulario

```php
<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar inputs
    $campo = $conn->real_escape_string($_POST['campo']);
    
    // Validar
    if (empty($campo)) {
        $error = "El campo es requerido.";
    } else {
        // Procesar
        $sql = "INSERT INTO tabla (campo) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $campo);
        
        if ($stmt->execute()) {
            $success = "Operación exitosa.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!-- Mostrar mensajes -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
```

### 5. Agregar Middleware de Autenticación

```php
<?php
// Al inicio de la página
require_once 'config/db.php';
require_once 'includes/functions.php';

start_session_safe();

// Verificar login
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// Verificar rol específico
if ($_SESSION['user_role'] !== 'admin') {
    $_SESSION['flash_message'] = "No tienes permisos para acceder.";
    redirect('index.php');
}
?>
```

### 6. Trabajar con Imágenes

```php
// Subir imagen
if (isset($_FILES['imagen'])) {
    $target_dir = "assets/img/";
    $file_extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
        // Guardar $new_filename en la base de datos
        $sql = "UPDATE productos SET imagen = ? WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_filename, $id_producto);
        $stmt->execute();
    }
}

// Mostrar imagen
<img src="assets/img/<?php echo htmlspecialchars($row['imagen']); ?>" alt="Producto">
```

### 7. Debugging

```php
// Mostrar errores PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug de variables
echo '<pre>';
print_r($variable);
echo '</pre>';

// Debug de consultas SQL
echo $conn->error;

// Ver sesión
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

---

## 📝 Checklist de Desarrollo

### Antes de Crear una Página Nueva

- [ ] ¿Necesita autenticación?
- [ ] ¿Qué datos necesita de la base de datos?
- [ ] ¿Qué funciones reutilizables puedo usar?
- [ ] ¿Necesito crear nuevas funciones?

### Estructura de la Página

- [ ] Incluir `config/db.php`
- [ ] Incluir `includes/functions.php`
- [ ] Incluir `includes/header.php`
- [ ] Contenido HTML en `<div class="container">`
- [ ] Incluir `includes/footer.php`
- [ ] NO incluir `<!DOCTYPE>`, `<html>`, `<head>`, `<body>` manualmente

### Seguridad

- [ ] Usar `htmlspecialchars()` para mostrar datos de usuario
- [ ] Usar prepared statements para consultas SQL
- [ ] Validar y sanitizar inputs
- [ ] Verificar autenticación donde sea necesario
- [ ] Usar `password_hash()` para contraseñas

### Estilos

- [ ] Usar clases de Bootstrap cuando sea posible
- [ ] Usar variables CSS para colores
- [ ] Agregar clases personalizadas en `styles.css`
- [ ] Evitar estilos inline

---

## 🚀 Comandos Útiles

```bash
# Docker
docker-compose up -d              # Iniciar servicios
docker-compose down               # Detener servicios
docker-compose restart web        # Reiniciar servidor web
docker-compose logs -f web        # Ver logs en tiempo real

# MySQL (dentro del contenedor)
docker-compose exec db mysql -u amg -p
# Contraseña: amg

# Ver tablas
SHOW TABLES;
DESCRIBE usuarios;
SELECT * FROM productos;
```

---

## 📚 Recursos

- **Bootstrap 5.3**: https://getbootstrap.com/docs/5.3/
- **PHP mysqli**: https://www.php.net/manual/en/book.mysqli.php
- **Docker Compose**: https://docs.docker.com/compose/

---

**Última actualización**: 2025-11-26
