# Documentación del Proyecto Final

> 📚 **Documentación Completa Disponible:**
> - [GUIA_COMPLETA.md](GUIA_COMPLETA.md) - Guía detallada de arquitectura, funciones y cómo implementar cambios
> - [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplos de código para funcionalidades comunes

Esta carpeta contiene la documentación necesaria para entender la estructura, configuración y flujo de trabajo del proyecto.

## 1. Cómo Ejecutar el Proyecto

El proyecto utiliza **Docker** para gestionar el servidor web (Apache/PHP) y la base de datos (MySQL).

### Comandos Principales
- **Iniciar el servidor:**
  ```bash
  docker-compose up -d
  ```
- **Detener el servidor:**
  ```bash
  docker-compose down
  ```

### Accesos
- **Sitio Web:** [http://localhost:8080](http://localhost:8080)
- **Base de Datos (PhpMyAdmin):** [http://localhost:8081](http://localhost:8081)

---

## 2. Estructura de Directorios

La estructura principal del proyecto es la siguiente:

```
ProyectoFinal/
├── docker-compose.yml      # Configuración de Docker (Servicios: web, db, phpmyadmin)
├── mysql_data/             # Persistencia de datos de MySQL (NO TOCAR)
├── documentacion/          # Documentación del proyecto
└── html/                   # CÓDIGO FUENTE DEL SITIO WEB
    ├── config/
    │   └── db.php          # Conexión a la base de datos
    ├── includes/
    │   ├── header.php      # Encabezado común (Navegación, CSS)
    │   ├── footer.php      # Pie de página común (Scripts, Copyright)
    │   └── functions.php   # Funciones PHP reutilizables
    ├── assets/
    │   ├── css/            # Hojas de estilo (styles.css)
    │   └── img/            # Imágenes del sitio
    ├── admin/              # Panel de Administración
    │   ├── index.php       # Dashboard admin
    │   ├── productos.php   # Gestión de productos
    │   └── agregar.php     # Formulario para agregar productos
    ├── index.php           # Página de inicio
    ├── login.php           # Inicio de sesión
    ├── registro.php        # Registro de usuarios
    ├── carrito.php         # Carrito de compras
    ├── checkout.php        # Proceso de pago
    └── setup_db.php        # Script para inicializar la base de datos
```

---

## 3. Configuración de la Base de Datos

La conexión se define en `html/config/db.php`.

**Credenciales (según docker-compose.yml):**
- **Host:** `db`
- **Usuario:** `amg`
- **Contraseña:** `amg`
- **Base de Datos:** `bd`
- **Root Password:** `root`

Si necesitas reiniciar la base de datos desde cero, puedes ejecutar el script:
[http://localhost:8080/setup_db.php](http://localhost:8080/setup_db.php)

---

## 4. Guía de Desarrollo

### Crear una Nueva Página
Para mantener la consistencia del diseño, sigue esta estructura básica al crear un nuevo archivo PHP en la carpeta `html/`:

```php
<?php
// 1. Incluir configuración y funciones comunes
require 'config/db.php';
require 'includes/functions.php';

// 2. Incluir el encabezado (abre el <body> y la navegación)
include 'includes/header.php';
?>

<main class="container">
    <h1>Título de la Nueva Página</h1>
    <p>Contenido de la página...</p>
</main>

<?php
// 3. Incluir el pie de página (cierra el <body> y <html>)
include 'includes/footer.php';
?>
```

### Estilos (CSS)
Todos los estilos globales están en `html/assets/css/styles.css`.
- Usa las clases de utilidad definidas allí.
- Evita estilos en línea (`style="..."`).

// ... existing content ...

## 5. Estructura de Base de Datos

El sistema utiliza las siguientes tablas relacionales:

### `usuarios`
Almacena la información de los clientes y administradores.
- **id_usuario** (PK): Identificador único.
- **nombre**: Nombre completo.
- **email**: Correo electrónico (único).
- **password**: Contraseña encriptada.
- **tipo_usuario**: 'cliente' o 'admin'.
- **fecha_registro**: Fecha de creación de la cuenta.

### `categorias`
Categorías para organizar los productos.
- **id_categoria** (PK): Identificador único.
- **nombre**: Nombre de la categoría.
- **descripcion**: Breve descripción.

### `productos`
Inventario de productos disponibles.
- **id_producto** (PK): Identificador único.
- **nombre**: Nombre del producto.
- **precio**: Precio unitario.
- **stock**: Cantidad disponible.
- **id_categoria** (FK): Relación con la tabla `categorias`.
- **imagen**: Ruta del archivo de imagen.
- **estado**: 'activo' o 'inactivo'.

### `pedidos`
Registro de compras realizadas.
- **id_pedido** (PK): Identificador único.
- **id_usuario** (FK): Usuario que realizó el pedido.
- **total**: Monto total de la compra.
- **estado**: 'pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'.
- **fecha_pedido**: Fecha de la transacción.

### `detalle_pedidos`
Productos individuales dentro de cada pedido.
- **id_detalle** (PK): Identificador único.
- **id_pedido** (FK): Relación con `pedidos`.
- **id_producto** (FK): Relación con `productos`.
- **cantidad**: Unidades compradas.
- **precio_unitario**: Precio al momento de la compra.

### `carrito_compras`
Carrito temporal para usuarios registrados.
- **id_carrito** (PK): Identificador único.
- **id_usuario** (FK): Usuario dueño del carrito.
- **id_producto** (FK): Producto agregado.
- **cantidad**: Cantidad deseada.

### `sesiones`
Manejo de sesiones de usuario persistentes.
- **id_sesion** (PK): ID de sesión PHP.
- **id_usuario** (FK): Usuario asociado.
- **datos**: Datos de sesión serializados.
