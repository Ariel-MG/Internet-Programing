# 📚 Índice de Documentación del Proyecto

Bienvenido a la documentación completa del proyecto E-commerce. Esta guía te ayudará a navegar por toda la documentación disponible.

---

## 📖 Documentos Disponibles

### 1. [ESTRUCTURA.md](ESTRUCTURA.md)
**Resumen rápido del proyecto**

Contiene:
- ✅ Comandos para ejecutar el proyecto con Docker
- ✅ Estructura de directorios
- ✅ Configuración de la base de datos
- ✅ Patrón básico para crear páginas
- ✅ Esquema de base de datos

**Cuándo usar**: Para referencia rápida de la estructura y configuración básica.

---

### 2. [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
**Documentación técnica completa**

Contiene:
- 🏗️ Arquitectura del proyecto (patrón MVC)
- ⚙️ Configuración y conexión a Docker/MySQL
- 🔐 Sistema de autenticación completo
- 🛒 Sistema de carrito (todas las funciones)
- 📄 Estructura estándar de páginas
- 🔧 Todas las funciones reutilizables documentadas
- 🎨 Sistema de estilos y variables CSS
- 🗄️ Esquema completo de base de datos
- 🛠️ Guía paso a paso para implementar cambios

**Cuándo usar**: 
- Cuando necesites entender cómo funciona el sistema
- Antes de implementar nuevas funcionalidades
- Para consultar funciones disponibles
- Para entender el flujo de autenticación o carrito

---

### 3. [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md)
**Código de ejemplo listo para usar**

Contiene ejemplos completos de:
1. ✅ Crear página de detalle de producto
2. ✅ Implementar sistema de búsqueda
3. ✅ Agregar filtros por categoría
4. ✅ Crear panel de administración
5. ✅ Implementar proceso de checkout
6. ✅ Agregar sistema de reseñas
7. ✅ Implementar paginación
8. ✅ Agregar wishlist/favoritos

**Cuándo usar**: 
- Cuando necesites implementar una funcionalidad específica
- Como referencia de código funcional
- Para copiar y adaptar ejemplos a tu proyecto

---

## 🚀 Guía de Inicio Rápido

### Para Nuevos Desarrolladores

1. **Primero**: Lee [ESTRUCTURA.md](ESTRUCTURA.md) para entender la organización básica
2. **Segundo**: Revisa [GUIA_COMPLETA.md](GUIA_COMPLETA.md) sección "Arquitectura del Proyecto"
3. **Tercero**: Familiarízate con las funciones en la sección "Funciones Reutilizables"
4. **Cuarto**: Consulta [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) cuando necesites implementar algo

### Para Implementar una Nueva Funcionalidad

1. **Planifica**: Revisa "Cómo Implementar Cambios" en [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
2. **Busca ejemplos**: Consulta [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md)
3. **Sigue el patrón**: Usa la estructura estándar de páginas
4. **Prueba**: Verifica que funcione correctamente

### Para Solucionar Problemas

1. **Conexión DB**: Revisa "Configuración y Conexión" en [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
2. **Autenticación**: Revisa "Sistema de Autenticación" en [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
3. **Carrito**: Revisa "Sistema de Carrito" en [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
4. **Debugging**: Usa la sección "Debugging" en [GUIA_COMPLETA.md](GUIA_COMPLETA.md)

---

## 🔍 Búsqueda Rápida por Tema

### Autenticación y Sesiones
- Funciones: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#sistema-de-autenticación)
- Proteger páginas: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#proteger-páginas)
- Middleware: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 5

### Carrito de Compras
- Funciones del carrito: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#sistema-de-carrito)
- Flujo completo: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#flujo-del-carrito)
- Checkout: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 5

### Productos
- Mostrar productos: [ESTRUCTURA.md](ESTRUCTURA.md) + [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
- Detalle de producto: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 1
- Búsqueda: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 2
- Filtros: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 3
- Paginación: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 7

### Administración
- Panel admin: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 4
- Gestión de productos: [EJEMPLOS_PRACTICOS.md](EJEMPLOS_PRACTICOS.md) - Ejemplo 4

### Base de Datos
- Esquema completo: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#base-de-datos)
- Inicializar DB: [ESTRUCTURA.md](ESTRUCTURA.md) + [GUIA_COMPLETA.md](GUIA_COMPLETA.md)
- Consultas preparadas: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#funciones-reutilizables)

### Estilos y Diseño
- Variables CSS: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#estilos-y-diseño)
- Clases personalizadas: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#estilos-y-diseño)
- Bootstrap: [GUIA_COMPLETA.md](GUIA_COMPLETA.md#estilos-y-diseño)

---

## 📋 Checklist de Referencia Rápida

### Antes de Crear una Página

```
□ Incluir config/db.php
□ Incluir includes/functions.php
□ Incluir includes/header.php (NO crear HTML manualmente)
□ Contenido en <div class="container">
□ Incluir includes/footer.php
□ Usar htmlspecialchars() para datos de usuario
□ Usar prepared statements para SQL
```

### Estructura de Archivos del Proyecto

```
html/
├── config/db.php           → Conexión DB
├── includes/
│   ├── header.php          → HTML + Navbar
│   ├── footer.php          → Footer + Scripts
│   └── functions.php       → Funciones reutilizables
├── assets/
│   ├── css/styles.css      → Estilos personalizados
│   ├── img/                → Imágenes
│   └── js/main.js          → JavaScript
├── admin/                  → Panel admin
├── *.php                   → Páginas públicas
```

### Funciones Más Usadas

```php
// Sesiones
start_session_safe()
is_logged_in()
get_user_name()
redirect($url)

// Carrito
obtener_carrito($id_usuario)
agregar_al_carrito($id_usuario, $id_producto, $cantidad)
eliminar_del_carrito($id_carrito)
vaciar_carrito($id_usuario)
total_carrito($id_usuario)
contar_items_carrito($id_usuario)
```

### Comandos Docker

```bash
docker-compose up -d        # Iniciar
docker-compose down         # Detener
docker-compose logs -f web  # Ver logs
```

### Accesos

- **Sitio**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
- **Setup DB**: http://localhost:8080/setup_db.php

---

## 💡 Consejos de Desarrollo

1. **Siempre consulta la documentación antes de implementar**
2. **Usa las funciones existentes en `functions.php`**
3. **Sigue el patrón estándar de páginas**
4. **Usa prepared statements para seguridad**
5. **Consulta los ejemplos prácticos para código funcional**
6. **Mantén los estilos en `styles.css`, no inline**
7. **Usa variables CSS para colores consistentes**

---

## 🔄 Flujo de Trabajo Recomendado

```
1. Leer documentación relevante
   ↓
2. Buscar ejemplo similar en EJEMPLOS_PRACTICOS.md
   ↓
3. Copiar estructura base
   ↓
4. Adaptar a tu necesidad
   ↓
5. Probar funcionalidad
   ↓
6. Verificar seguridad (htmlspecialchars, prepared statements)
   ↓
7. Revisar estilos
```

---

## 📞 Estructura de Soporte

Si tienes dudas:

1. **Primero**: Busca en esta documentación
2. **Segundo**: Revisa los ejemplos prácticos
3. **Tercero**: Consulta el código existente similar
4. **Cuarto**: Usa la sección de debugging en GUIA_COMPLETA.md

---

**Última actualización**: 2025-11-26

**Mantenedores**: Equipo de desarrollo

**Versión del proyecto**: 1.0
