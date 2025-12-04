# 🔐 Manual de Usuario - Sistema E-Commerce

## 📖 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso al Sistema](#acceso-al-sistema)
3. [Registro e Inicio de Sesión](#registro-e-inicio-de-sesión)
4. [Navegación del Sitio](#navegación-del-sitio)
5. [Comprar Productos](#comprar-productos)
6. [Gestión de Pedidos](#gestión-de-pedidos)
7. [Sistema de Reseñas](#sistema-de-reseñas)
8. [Panel de Administración](#panel-de-administración)
9. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## 1. 📱 Introducción

Bienvenido al sistema de E-Commerce. Este manual te guiará paso a paso en el uso de todas las funcionalidades disponibles.

### ¿Qué puedes hacer?

**Como Cliente:**
- 🛍️ Navegar por el catálogo de productos
- 🔍 Buscar y filtrar productos
- 🛒 Agregar productos al carrito
- 💳 Realizar compras
- 📦 Ver historial de pedidos
- ⭐ Dejar reseñas de productos

**Como Administrador:**
- 📊 Ver estadísticas de ventas
- 📦 Gestionar productos
- 🏷️ Gestionar categorías
- 📋 Gestionar pedidos
- 👥 Gestionar usuarios

---

## 2. 🌐 Acceso al Sistema

### Abrir el Sitio Web

1. Abre tu navegador web (Chrome, Firefox, Safari, Edge)
2. En la barra de direcciones escribe: `http://localhost:8080`
3. Presiona Enter

Verás la página de inicio con:
- Barra de navegación superior
- Sección de bienvenida
- Productos destacados
- Características del sitio

---

## 3. 👤 Registro e Inicio de Sesión

### Crear una Cuenta Nueva

1. **Acceder al Registro**
   - Haz clic en "Registro" en la barra de navegación
   - O visita directamente: `http://localhost:8080/registro.php`

2. **Llenar el Formulario**
   - **Nombre completo**: Tu nombre y apellido
   - **Email**: Tu correo electrónico (debe ser único)
   - **Teléfono**: Número de contacto (opcional)
   - **Dirección**: Dirección de entrega (opcional, puedes actualizarla después)
   - **Contraseña**: Mínimo 6 caracteres
   - **Confirmar contraseña**: Debe coincidir con la anterior

3. **Completar Registro**
   - Haz clic en el botón "Registrarse"
   - Verás un mensaje de éxito
   - Serás redirigido a la página de login

### Iniciar Sesión

1. **Acceder al Login**
   - Haz clic en "Login" en la barra de navegación
   - O visita: `http://localhost:8080/login.php`

2. **Ingresar Credenciales**
   - **Email**: El correo con el que te registraste
   - **Contraseña**: Tu contraseña

3. **Entrar**
   - Haz clic en "Entrar"
   - Serás redirigido a la página de inicio

### Cerrar Sesión

- Haz clic en tu nombre en la barra de navegación
- Selecciona "Cerrar Sesión" del menú desplegable

---

## 4. 🧭 Navegación del Sitio

### Barra de Navegación

La barra superior te permite acceder a:

- **Mi Tienda**: Volver a la página de inicio
- **Inicio**: Página principal
- **Productos**: Catálogo completo
- **Mis Pedidos**: Tu historial (solo si has iniciado sesión)
- **🛒 Carrito**: Ver tu carrito de compras
- **Tu nombre**: Menú de usuario (Perfil, Panel Admin, Cerrar Sesión)

### Página de Inicio

La página de inicio muestra:

1. **Hero Section**: Mensaje de bienvenida con botones rápidos
2. **Productos Destacados**: Los últimos 6 productos agregados
3. **Características**: Envío gratuito, soporte 24/7, garantía de devolución

### Catálogo de Productos

Accede haciendo clic en "Productos" en el menú.

**Panel de Filtros (Izquierda):**
- **Buscar**: Escribe el nombre del producto
- **Categoría**: Filtra por tipo de producto
- **Ordenar por**: 
  - Nombre (A-Z o Z-A)
  - Precio (menor a mayor o mayor a menor)

**Vista de Productos (Derecha):**
- Cada tarjeta muestra:
  - Imagen del producto
  - Nombre
  - Descripción breve
  - Precio
  - Botón "Ver Detalles"

---

## 5. 🛒 Comprar Productos

### Ver Detalle de Producto

1. **Acceder al Producto**
   - Haz clic en cualquier producto del catálogo
   - O en "Ver Detalles"

2. **Información Disponible**
   - Imagen principal del producto
   - Nombre y descripción completa
   - Precio
   - Stock disponible
   - Categoría
   - Calificación promedio
   - Reseñas de otros usuarios

### Agregar al Carrito

1. En la página del producto, busca el formulario "Agregar al Carrito"
2. Selecciona la **cantidad** deseada
3. Haz clic en "Agregar al Carrito"
4. Verás un mensaje de confirmación
5. El contador del carrito en la barra de navegación se actualizará

### Ver el Carrito

1. Haz clic en el icono 🛒 en la barra de navegación
2. Verás todos los productos agregados con:
   - Imagen miniatura
   - Nombre del producto
   - Precio unitario
   - Cantidad (editable)
   - Subtotal
   - Opciones para actualizar o eliminar

### Modificar el Carrito

**Cambiar Cantidad:**
1. Modifica el número en el campo de cantidad
2. Haz clic en el botón de actualizar (↻)

**Eliminar Producto:**
1. Haz clic en el botón "Eliminar"
2. El producto se quitará del carrito

**Vaciar Carrito:**
1. Haz clic en "Vaciar Carrito"
2. Confirma la acción
3. Todos los productos serán eliminados

### Finalizar Compra

1. **Desde el Carrito**
   - Revisa tus productos
   - Verifica el total
   - Haz clic en "Proceder al Pago"

2. **Página de Checkout**
   - Tus datos (nombre, email) aparecerán automáticamente
   - **Teléfono de contacto**: Obligatorio
   - **Dirección de envío**: Obligatoria (calle, número, ciudad, código postal)
   - **Notas adicionales**: Opcional (instrucciones de entrega)
   - **Método de pago**: Selecciona entre las opciones disponibles

3. **Resumen del Pedido**
   - Revisa los productos
   - Verifica el total
   - Haz clic en "Realizar Pedido"

4. **Confirmación**
   - Verás un mensaje de éxito
   - Se mostrará tu número de pedido
   - El carrito se vaciará automáticamente
   - El inventario se actualizará

---

## 6. 📦 Gestión de Pedidos

### Ver Mis Pedidos

1. Haz clic en "Mis Pedidos" en el menú de navegación
2. Verás una lista de todos tus pedidos

### Información de Cada Pedido

Cada tarjeta de pedido muestra:

**Encabezado:**
- Número de pedido
- Fecha y hora
- Estado actual con color:
  - 🟡 Amarillo: Pendiente
  - 🔵 Azul: Procesando
  - 🟣 Morado: Enviado
  - 🟢 Verde: Entregado
  - 🔴 Rojo: Cancelado

**Productos:**
- Imagen miniatura
- Nombre del producto
- Cantidad y precio unitario
- Subtotal

**Resumen:**
- Total pagado
- Método de pago
- Dirección de entrega

### Estados de Pedido

| Estado | Descripción |
|--------|-------------|
| **Pendiente** | El pedido ha sido recibido y está esperando procesamiento |
| **Procesando** | El pedido está siendo preparado |
| **Enviado** | El pedido ha sido despachado |
| **Entregado** | El pedido ha llegado a su destino |
| **Cancelado** | El pedido fue cancelado |

---

## 7. ⭐ Sistema de Reseñas

### Dejar una Reseña

1. **Requisitos**
   - Debes haber comprado el producto
   - El pedido debe estar entregado
   - Solo puedes dejar una reseña por producto

2. **Escribir Reseña**
   - Ve a la página del producto
   - Desplázate hasta "Dejar una Reseña"
   - Selecciona tu calificación (1 a 5 estrellas)
   - Escribe tu comentario
   - Haz clic en "Publicar Reseña"

3. **Ver Reseñas**
   - En la misma página del producto
   - Verás todas las reseñas de otros usuarios
   - Nombre del usuario y fecha
   - Calificación y comentario

### Sistema de Calificación

- ⭐ 1 estrella: Muy malo
- ⭐⭐ 2 estrellas: Malo
- ⭐⭐⭐ 3 estrellas: Regular
- ⭐⭐⭐⭐ 4 estrellas: Bueno
- ⭐⭐⭐⭐⭐ 5 estrellas: Excelente

---

## 8. 👨‍💼 Panel de Administración

> **Nota**: Solo usuarios con rol de "admin" pueden acceder a estas funciones.

### Acceder al Panel Admin

1. Inicia sesión con una cuenta de administrador
2. Haz clic en tu nombre en la barra de navegación
3. Selecciona "Panel Admin"
4. O visita: `http://localhost:8080/admin/`

### Dashboard

El panel principal muestra:

**Métricas Principales:**
- 💰 Ventas Totales
- 📋 Total de Pedidos
- 👥 Usuarios Registrados
- 📦 Productos en Catálogo

**Alertas:**
- Pedidos pendientes
- Productos con bajo stock
- Ventas del mes actual

**Tablas:**
- Últimos pedidos recibidos
- Productos con stock bajo

---

### 📦 Gestión de Productos

**Acceder:** Admin → Productos

#### Listar Productos

- Verás todos los productos en una tabla
- Información mostrada:
  - ID del producto
  - Imagen miniatura
  - Nombre
  - Categoría
  - Precio
  - Stock (con indicador de color)
  - Estado (activo/inactivo)
  - Acciones

**Indicadores de Stock:**
- 🔴 Rojo: Sin stock (0)
- 🟡 Amarillo: Bajo stock (< 10)
- 🟢 Verde: Stock normal (≥ 10)

#### Agregar Producto

1. Haz clic en "Agregar Producto"
2. Llena el formulario:
   - **Nombre**: Nombre del producto
   - **Descripción**: Detalles del producto
   - **Precio**: Precio en formato decimal (ej: 99.99)
   - **Stock**: Cantidad disponible
   - **Categoría**: Selecciona de la lista
   - **Imagen**: Sube una foto (JPG, PNG, GIF, WEBP)
   - **Estado**: Activo o Inactivo
3. Haz clic en "Guardar"

#### Editar Producto

1. Haz clic en el botón "Editar" (lápiz) del producto
2. Modifica los campos necesarios
3. Opcionalmente sube una nueva imagen
4. Haz clic en "Actualizar"

#### Eliminar Producto

1. Haz clic en el botón "Eliminar" (basura) del producto
2. Confirma la eliminación
3. Si el producto tiene una imagen, esta también se eliminará

#### Filtrar Productos

Usa los filtros en la parte superior:
- **Categoría**: Todas o una específica
- **Estado**: Todos, Activos o Inactivos
- **Imagen**: Todas, Sin foto, Con foto
- **Buscar**: Por nombre de producto
- Haz clic en "Filtrar"

---

### 📋 Gestión de Pedidos

**Acceder:** Admin → Pedidos

#### Ver Pedidos

Tabla con información de cada pedido:
- Número de pedido
- Cliente (nombre y email)
- Total
- Método de pago
- Estado (con selector)
- Fecha
- Acciones

#### Actualizar Estado

1. Selecciona el nuevo estado del menú desplegable
2. El cambio se guarda automáticamente

Estados disponibles:
- Pendiente
- Procesando
- Enviado
- Entregado
- Cancelado

#### Ver Detalle de Pedido

1. Haz clic en "Ver Detalles"
2. Se abrirá un modal con:
   - Información del cliente
   - Dirección de entrega
   - Lista de productos
   - Cantidades y precios
   - Total del pedido

#### Filtrar Pedidos

- **Por estado**: Selecciona un estado específico
- **Por cliente**: Busca por nombre o email
- Haz clic en "Filtrar"

---

### 👥 Gestión de Usuarios

**Acceder:** Admin → Usuarios

#### Listar Usuarios

Tabla con todos los usuarios registrados:
- ID
- Nombre
- Email
- Teléfono
- Rol (con selector)
- Fecha de registro
- Acciones

#### Cambiar Rol

1. Selecciona "admin" o "cliente" en el selector
2. El cambio se guarda automáticamente

**Roles:**
- **Cliente**: Usuario normal, puede comprar
- **Admin**: Acceso completo al panel de administración

#### Eliminar Usuario

1. Haz clic en "Eliminar"
2. Confirma la acción
3. **Nota**: No puedes eliminar tu propia cuenta

---

### 🏷️ Gestión de Categorías

**Acceder:** Admin → Categorías

#### Ver Categorías

Lista de todas las categorías con:
- ID
- Nombre
- Descripción
- Total de productos
- Acciones

#### Agregar Categoría

1. Haz clic en "Nueva Categoría"
2. Llena el formulario:
   - **Nombre**: Nombre de la categoría
   - **Descripción**: Descripción breve
3. Haz clic en "Guardar"

#### Editar Categoría

1. Haz clic en "Editar"
2. Modifica los campos
3. Haz clic en "Actualizar"

#### Eliminar Categoría

1. Haz clic en "Eliminar"
2. **Restricción**: No puedes eliminar una categoría que tenga productos asignados
3. Primero debes reasignar o eliminar los productos

---

## 9. ❓ Preguntas Frecuentes

### Cuenta y Acceso

**P: ¿Olvidé mi contraseña, qué hago?**
R: Actualmente no hay función de recuperación automática. Contacta al administrador.

**P: ¿Puedo cambiar mi email?**
R: No directamente. Debes contactar al administrador.

**P: ¿Cómo actualizo mi dirección?**
R: En el proceso de checkout puedes ingresar una nueva dirección.

### Compras

**P: ¿Puedo cancelar un pedido?**
R: Solo un administrador puede cambiar el estado del pedido. Contacta soporte.

**P: ¿Cuánto tiempo tarda en llegar mi pedido?**
R: Depende del estado actual. Revisa tu pedido en "Mis Pedidos".

**P: ¿Puedo modificar un pedido ya realizado?**
R: No, una vez confirmado no se puede modificar. Contacta al administrador.

**P: ¿Hay límite en la cantidad de productos?**
R: Solo el stock disponible.

### Productos

**P: ¿Puedo comprar productos sin stock?**
R: No, solo puedes agregar productos con stock disponible.

**P: ¿Cómo sé si un producto está disponible?**
R: En la página del producto verás el stock actual.

**P: ¿Puedo devolver un producto?**
R: Contacta al administrador para políticas de devolución.

### Reseñas

**P: ¿Por qué no puedo dejar una reseña?**
R: Solo puedes reseñar productos que hayas comprado y que estén entregados.

**P: ¿Puedo editar mi reseña?**
R: No, las reseñas son permanentes. Escribe con cuidado.

**P: ¿Puedo dejar varias reseñas del mismo producto?**
R: No, solo una reseña por producto por usuario.

### Técnico

**P: ¿Qué navegadores son compatibles?**
R: Chrome, Firefox, Safari, Edge (versiones recientes).

**P: ¿Funciona en móviles?**
R: Sí, el diseño es completamente responsive.

**P: ¿Es seguro ingresar mis datos?**
R: Sí, usamos medidas de seguridad estándar de la industria.

---

## 📞 Soporte y Contacto

Si tienes problemas o preguntas:

1. Revisa este manual
2. Consulta las Preguntas Frecuentes
3. Contacta al administrador del sistema

---

## 🎯 Consejos Útiles

### Para Clientes

✅ **DO:**
- Revisa las reseñas antes de comprar
- Verifica el stock disponible
- Lee la descripción completa del producto
- Guarda el número de tu pedido
- Revisa periódicamente el estado de tus pedidos

❌ **DON'T:**
- No compartas tu contraseña
- No dejes reseñas falsas
- No intentes explotar bugs del sistema

### Para Administradores

✅ **DO:**
- Actualiza los estados de los pedidos regularmente
- Mantén el inventario actualizado
- Responde a los clientes rápidamente
- Revisa las métricas diariamente
- Haz backups de la base de datos

❌ **DON'T:**
- No elimines usuarios sin razón
- No cambies precios sin previo aviso
- No ignores los pedidos pendientes

---

**¡Disfruta tu experiencia de compra!** 🎉

---

*Última actualización: Diciembre 2025*
*Versión: 1.0*
