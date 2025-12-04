# 🛒 E-Commerce Project - Internet Programming

Proyecto final de Programación en Internet - Sistema de comercio electrónico completo desarrollado con PHP, MySQL y Bootstrap.

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Funcionalidades Implementadas](#funcionalidades-implementadas)
- [Base de Datos](#base-de-datos)
- [Usuarios de Prueba](#usuarios-de-prueba)
- [Capturas de Pantalla](#capturas-de-pantalla)
- [Créditos](#créditos)

---

## 📖 Descripción

Sistema de comercio electrónico completo que permite a los usuarios navegar por un catálogo de productos, agregar artículos al carrito de compras, realizar pedidos y gestionar su perfil. Incluye un panel de administración completo para gestionar productos, pedidos, usuarios y categorías.

### ✨ Características Principales

- 🛍️ **Catálogo de productos** con búsqueda, filtros y ordenamiento
- 🛒 **Carrito de compras** funcional con actualización de cantidades
- 💳 **Sistema de checkout** con procesamiento de pedidos
- 📦 **Historial de pedidos** para usuarios
- ⭐ **Sistema de reseñas** y calificaciones de productos
- 👤 **Autenticación de usuarios** con sesiones seguras
- 🔐 **Panel de administración** completo con control de acceso
- 📱 **Diseño responsive** compatible con todos los dispositivos
- 🎨 **Interfaz moderna** con Bootstrap 5

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.2** - Lenguaje de programación del lado del servidor
- **MySQL 8.1** - Sistema de gestión de bases de datos
- **Apache** - Servidor web

### Frontend
- **HTML5** - Estructura de las páginas
- **CSS3** - Estilos personalizados
- **Bootstrap 5.3.0** - Framework CSS para diseño responsive
- **Bootstrap Icons 1.11.0** - Biblioteca de iconos
- **JavaScript** - Interactividad del lado del cliente

### Herramientas
- **Docker & Docker Compose** - Contenedorización y despliegue
- **phpMyAdmin** - Administración de base de datos
- **Git** - Control de versiones

---

## 📦 Requisitos

- Docker Desktop instalado
- Git
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- 4GB de RAM disponible
- 2GB de espacio en disco

---

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Ariel-MG/Internet-Programing.git
cd Internet-Programing/ProyectoFinal
```

### 2. Levantar los contenedores con Docker

```bash
docker-compose up -d
```

Esto iniciará tres servicios:
- **Web Server (Apache + PHP)**: http://localhost:8080
- **MySQL Database**: Puerto 3307
- **phpMyAdmin**: http://localhost:8081

### 3. Inicializar la base de datos

Accede a: http://localhost:8080/setup_db.php

Este script creará todas las tablas necesarias y datos de ejemplo.

### 4. ¡Listo! Accede a la aplicación

- **Sitio web**: http://localhost:8080
- **Panel Admin**: http://localhost:8080/admin/
- **phpMyAdmin**: http://localhost:8081

---

## 📁 Estructura del Proyecto

```
ProyectoFinal/
├── docker-compose.yml          # Configuración de Docker
├── html/                       # Código fuente de la aplicación
│   ├── admin/                  # Panel de administración
│   │   ├── index.php          # Dashboard administrativo
│   │   ├── productos.php      # Gestión de productos
│   │   ├── pedidos.php        # Gestión de pedidos
│   │   ├── usuarios.php       # Gestión de usuarios
│   │   └── categorias.php     # Gestión de categorías
│   ├── assets/                # Recursos estáticos
│   │   ├── css/              # Hojas de estilo
│   │   ├── img/              # Imágenes de productos
│   │   └── js/               # Scripts JavaScript
│   ├── config/               # Configuración
│   │   └── db.php           # Conexión a base de datos
│   ├── includes/            # Archivos compartidos
│   │   ├── header.php       # Encabezado común
│   │   ├── footer.php       # Pie de página común
│   │   └── functions.php    # Funciones reutilizables
│   ├── index.php            # Página de inicio
│   ├── productos.php        # Catálogo de productos
│   ├── producto.php         # Detalle de producto
│   ├── carrito.php          # Carrito de compras
│   ├── checkout.php         # Finalizar compra
│   ├── mis_pedidos.php      # Historial de pedidos
│   ├── login.php            # Inicio de sesión
│   ├── registro.php         # Registro de usuarios
│   ├── logout.php           # Cerrar sesión
│   └── setup_db.php         # Script de inicialización
├── mysql_data/              # Datos persistentes de MySQL
└── documentacion/           # Documentación del proyecto
```

---

## ⚙️ Funcionalidades Implementadas

### 👥 Para Usuarios

#### Navegación y Productos
- ✅ Página de inicio con productos destacados
- ✅ Catálogo completo de productos con paginación
- ✅ Filtrado por categorías
- ✅ Búsqueda por nombre o descripción
- ✅ Ordenamiento (precio, nombre)
- ✅ Vista detallada de producto con imágenes
- ✅ Sistema de reseñas y calificaciones (1-5 estrellas)

#### Carrito y Compras
- ✅ Agregar productos al carrito
- ✅ Modificar cantidades en el carrito
- ✅ Eliminar productos del carrito
- ✅ Vaciar carrito completo
- ✅ Visualización del total del carrito
- ✅ Proceso de checkout con formulario de envío
- ✅ Actualización automática de inventario
- ✅ Generación de pedidos en base de datos

#### Gestión de Cuenta
- ✅ Registro de nuevos usuarios
- ✅ Inicio de sesión con email y contraseña
- ✅ Contraseñas hasheadas con bcrypt
- ✅ Sesiones persistentes y seguras
- ✅ Cierre de sesión
- ✅ Historial completo de pedidos
- ✅ Visualización de estado de pedidos

### 👨‍💼 Para Administradores

#### Dashboard
- ✅ Métricas clave (ventas totales, pedidos, usuarios, productos)
- ✅ Estadísticas de ventas del mes
- ✅ Alertas de pedidos pendientes
- ✅ Alertas de productos con bajo stock
- ✅ Tabla de últimos pedidos
- ✅ Visualización de productos bajo stock

#### Gestión de Productos
- ✅ Listado completo de productos
- ✅ Agregar nuevos productos
- ✅ Editar productos existentes
- ✅ Eliminar productos
- ✅ Subida de imágenes
- ✅ Control de stock
- ✅ Estados (activo/inactivo)
- ✅ Filtros por categoría, estado e imagen
- ✅ Búsqueda por nombre

#### Gestión de Pedidos
- ✅ Listado de todos los pedidos
- ✅ Actualización de estados (pendiente, procesando, enviado, entregado, cancelado)
- ✅ Visualización de detalles de pedido
- ✅ Información de cliente y productos
- ✅ Filtrado por estado
- ✅ Búsqueda por cliente

#### Gestión de Usuarios
- ✅ Listado completo de usuarios
- ✅ Cambio de roles (cliente/admin)
- ✅ Eliminación de usuarios
- ✅ Protección contra auto-eliminación
- ✅ Visualización de fecha de registro

#### Gestión de Categorías
- ✅ Listado de categorías
- ✅ Agregar nuevas categorías
- ✅ Editar categorías existentes
- ✅ Eliminar categorías (con validación)
- ✅ Contador de productos por categoría

### 🎨 Diseño y UX

- ✅ Diseño responsive (móvil, tablet, desktop)
- ✅ Navegación intuitiva
- ✅ Feedback visual de acciones
- ✅ Mensajes de confirmación y error
- ✅ Breadcrumbs de navegación
- ✅ Cards modernas con sombras
- ✅ Badges de estado con colores
- ✅ Iconos descriptivos
- ✅ Formularios validados
- ✅ Modales para acciones importantes

---

## 🗄️ Base de Datos

### Esquema de Base de Datos

La base de datos `ecomerce` contiene 8 tablas relacionadas:

#### 1. **usuarios**
```sql
- id_usuario (PK, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255)
- telefono (VARCHAR 20)
- direccion (TEXT)
- tipo_usuario (ENUM: 'cliente', 'admin')
- fecha_registro (TIMESTAMP)
```

#### 2. **categorias**
```sql
- id_categoria (PK, AUTO_INCREMENT)
- nombre (VARCHAR 100)
- descripcion (TEXT)
```

#### 3. **productos**
```sql
- id_producto (PK, AUTO_INCREMENT)
- nombre (VARCHAR 200)
- descripcion (TEXT)
- precio (DECIMAL 10,2)
- stock (INT)
- imagen (VARCHAR 255)
- id_categoria (FK -> categorias)
- estado (ENUM: 'activo', 'inactivo')
- fecha_creacion (TIMESTAMP)
```

#### 4. **carrito_compras**
```sql
- id_carrito (PK, AUTO_INCREMENT)
- id_usuario (FK -> usuarios)
- id_producto (FK -> productos)
- cantidad (INT)
- precio_unitario (DECIMAL 10,2)
- fecha_agregado (TIMESTAMP)
```

#### 5. **pedidos**
```sql
- id_pedido (PK, AUTO_INCREMENT)
- id_usuario (FK -> usuarios)
- total (DECIMAL 10,2)
- estado (ENUM: 'pendiente', 'procesando', 'enviado', 'entregado', 'cancelado')
- metodo_pago (VARCHAR 50)
- direccion_entrega (TEXT)
- fecha_pedido (TIMESTAMP)
```

#### 6. **detalle_pedidos**
```sql
- id_detalle (PK, AUTO_INCREMENT)
- id_pedido (FK -> pedidos)
- id_producto (FK -> productos)
- cantidad (INT)
- precio_unitario (DECIMAL 10,2)
- subtotal (DECIMAL 10,2)
```

#### 7. **resenas**
```sql
- id_resena (PK, AUTO_INCREMENT)
- id_producto (FK -> productos)
- id_usuario (FK -> usuarios)
- calificacion (INT 1-5)
- comentario (TEXT)
- fecha_resena (TIMESTAMP)
- UNIQUE(id_usuario, id_producto)
```

#### 8. **sesiones**
```sql
- id_sesion (PK, AUTO_INCREMENT)
- id_usuario (FK -> usuarios)
- token_sesion (VARCHAR 255)
- fecha_creacion (TIMESTAMP)
- fecha_expiracion (TIMESTAMP)
```

### Relaciones

- Usuarios → Pedidos (1:N)
- Usuarios → Carrito (1:N)
- Usuarios → Reseñas (1:N)
- Productos → Categorías (N:1)
- Productos → Carrito (1:N)
- Productos → Detalle Pedidos (1:N)
- Productos → Reseñas (1:N)
- Pedidos → Detalle Pedidos (1:N)

---

## 👤 Usuarios de Prueba

Una vez ejecutado `setup_db.php`, se crearán los siguientes usuarios:

### Administrador
- **Email**: admin@tienda.com
- **Contraseña**: admin123
- **Privilegios**: Acceso completo al panel de administración

### Cliente
- **Email**: cliente@tienda.com
- **Contraseña**: cliente123
- **Privilegios**: Puede navegar, comprar y ver su historial

> **Nota**: Puedes crear más usuarios desde la página de registro.

---

## 📸 Capturas de Pantalla

### Página de Inicio
![Página de inicio con productos destacados y hero section]

### Catálogo de Productos
![Catálogo con filtros, búsqueda y ordenamiento]

### Detalle de Producto
![Vista detallada con reseñas y calificaciones]

### Carrito de Compras
![Carrito con opciones de modificar y eliminar productos]

### Panel de Administración
![Dashboard con métricas y estadísticas]

### Gestión de Productos
![CRUD completo de productos con imágenes]

---

## 🔒 Seguridad

El proyecto implementa las siguientes medidas de seguridad:

- ✅ **Prepared Statements** para prevenir inyección SQL
- ✅ **Contraseñas hasheadas** con `password_hash()` y `password_verify()`
- ✅ **Validación de entrada** en todos los formularios
- ✅ **Escape de salida** con `htmlspecialchars()`
- ✅ **Control de acceso** basado en roles
- ✅ **Sesiones seguras** con validación
- ✅ **Protección CSRF** en formularios críticos
- ✅ **Validación de tipos de archivo** en uploads
- ✅ **Transacciones SQL** para operaciones críticas

---

## 🚧 Características Futuras (Roadmap)

- [ ] Sistema de cupones y descuentos
- [ ] Pasarela de pago real (Stripe/PayPal)
- [ ] Notificaciones por email
- [ ] Recuperación de contraseña
- [ ] Lista de favoritos/wishlist
- [ ] Comparador de productos
- [ ] Chat de soporte en vivo
- [ ] Sistema de puntos y recompensas
- [ ] Múltiples imágenes por producto
- [ ] Exportación de reportes (PDF/Excel)
- [ ] API REST para integración móvil
- [ ] Sistema de envío con tracking

---

## 📝 Documentación Adicional

Para más información detallada, consulta:

- [ESTRUCTURA.md](documentacion/ESTRUCTURA.md) - Arquitectura del proyecto
- [GUIA_COMPLETA.md](documentacion/GUIA_COMPLETA.md) - Guía de desarrollo
- [EJEMPLOS_PRACTICOS.md](documentacion/EJEMPLOS_PRACTICOS.md) - Ejemplos de código

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

---

## 👨‍💻 Autor

**Ariel MG**
- GitHub: [@Ariel-MG](https://github.com/Ariel-MG)
- Proyecto: [Internet-Programing](https://github.com/Ariel-MG/Internet-Programing)

---

## 📞 Soporte

Si tienes alguna pregunta o problema:

1. Revisa la documentación en la carpeta `/documentacion`
2. Busca en los issues del repositorio
3. Crea un nuevo issue con detalles del problema

---

## 🙏 Agradecimientos

- Bootstrap por el framework CSS
- Bootstrap Icons por los iconos
- Docker por facilitar el despliegue
- La comunidad de PHP por las mejores prácticas

---

**⭐ Si este proyecto te fue útil, por favor dale una estrella en GitHub!**

---

*Última actualización: Diciembre 2025*
