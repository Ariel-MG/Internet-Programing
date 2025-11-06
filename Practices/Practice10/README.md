# Sistema de Gestión de Librería - Práctica 10

## Descripción
Mini-sistema de registro de libros para una librería desarrollado con PHP, MySQL y Bootstrap 5. El sistema incluye tres páginas principales: inicio, registro y consulta de libros, con funcionalidades avanzadas de manejo de imágenes y administración de base de datos.

## Características Principales
- Página de inicio con navegación intuitiva
- Registro de libros con información completa e imágenes
- Consulta y visualización de libros registrados
- Almacenamiento de imágenes en formato BLOB
- Diseño responsive con Bootstrap 5
- Base de datos MySQL en contenedores Docker
- Herramientas de administración integradas
- Manejo seguro de archivos de imagen

## Arquitectura del Sistema

### Frontend
- **HTML5** con estructura semántica
- **Bootstrap 5** para diseño responsive
- **Bootstrap Icons** para iconografía
- **JavaScript** para validaciones del lado cliente

### Backend
- **PHP 8.2** con extensiones MySQLi y GD
- **MySQL 8.1** como base de datos
- **Apache** como servidor web
- **Docker Compose** para orquestación de servicios

### Base de Datos
```sql
CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autor VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    fecha_publicacion DATE NOT NULL,
    imagen LONGBLOB,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Estructura del Proyecto
```
Practice10/
├── docker-compose.yml          # Configuración de servicios Docker
├── .gitignore                  # Archivos excluidos del control de versiones
├── README.md                   # Documentación del proyecto
├── requierments.txt           # Especificaciones del proyecto
├── html/                      # Código fuente de la aplicación web
│   ├── index.html             # Página principal de bienvenida
│   ├── registro.php           # Formulario de registro de libros
│   ├── consulta.php           # Página de consulta y visualización
│   ├── config.php             # Configuración de base de datos
│   ├── imagen.php             # Servidor de imágenes desde BD
│   ├── limpiar_bd.php         # Herramienta de administración
│   └── database.sql           # Script de creación de tabla
├── mysql_data/                # Datos persistentes de MySQL
└── README.md                  # Este archivo
```

## Configuración e Instalación

### Prerrequisitos
- Docker y Docker Compose instalados
- Git instalado (para control de versiones)

### Instalación Paso a Paso

#### 1. Clonar el repositorio
```bash
git clone <URL_DEL_REPOSITORIO>
cd Practice10
```

#### 2. Levantar los servicios con Docker
```bash
docker-compose up -d
```

Esto iniciará los siguientes servicios:
- **Servidor web PHP**: Puerto 8080 (http://localhost:8080)
- **Base de datos MySQL**: Puerto 3306 interno
- **phpMyAdmin**: Puerto 8081 (http://localhost:8081)

#### 3. Configurar extensiones PHP en el contenedor
```bash
# Conectarse al contenedor de PHP
docker exec -it practice10-web-1 sh

# Instalar dependencias para las extensiones
apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev libxpm-dev zlib1g-dev

# Configurar e instalar extensiones PHP
docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm
docker-php-ext-install gd mysqli
docker-php-ext-enable mysqli

# Reiniciar Apache
apachectl restart

# Salir del contenedor
exit
```

#### 4. Crear la tabla de libros
Opción A - Usar phpMyAdmin:
1. Acceder a phpMyAdmin en http://localhost:8081
2. Credenciales: Servidor: `db`, Usuario: `amg`, Contraseña: `amg`
3. Seleccionar la base de datos `bd`
4. Ejecutar el script SQL del archivo `html/database.sql`

Opción B - Línea de comandos:
```bash
docker-compose exec db mysql -u amg -pamg bd < html/database.sql
```

#### 5. Verificar la instalación
Acceder a http://localhost:8080 para verificar que el sistema funciona correctamente.

## Funcionalidades del Sistema

### Página de Inicio (index.html)
- Interfaz de bienvenida con diseño atractivo
- Navegación principal a todas las secciones
- Acceso a herramientas de administración
- Información del sistema y guías de uso

### Registro de Libros (registro.php)
- Formulario completo para agregar nuevos libros
- Campos obligatorios: Autor, Título, Fecha de publicación
- Campo opcional: Imagen de portada
- Validaciones del lado servidor y cliente
- Soporte para múltiples formatos de imagen (JPEG, PNG, GIF, WebP)
- Manejo seguro de archivos BLOB

### Consulta de Libros (consulta.php)
- Visualización de todos los libros registrados
- Estadísticas de la biblioteca (total de libros, autores únicos, libros con portada)
- Visualización de imágenes de portada
- Información detallada de cada libro
- Diseño de tarjetas responsive
- Acceso a herramientas de administración

### Servidor de Imágenes (imagen.php)
- Servicio dedicado para mostrar imágenes desde la base de datos
- Detección automática de tipo MIME
- Cabeceras HTTP apropiadas para caché
- Manejo de errores con imagen por defecto
- Optimización de rendimiento

### Herramienta de Administración (limpiar_bd.php)
- Interfaz segura para limpiar la base de datos
- Confirmación doble para prevenir eliminaciones accidentales
- Reinicio de AUTO_INCREMENT
- Información del estado actual de la base de datos
- Validaciones múltiples de seguridad

## Tecnologías y Extensiones

### Extensiones PHP Requeridas
- **MySQLi**: Conexión nativa a MySQL con soporte para consultas preparadas
- **GD**: Procesamiento y manipulación de imágenes (JPEG, PNG, GIF, WebP)
- **Fileinfo**: Detección automática de tipos MIME

### Manejo Avanzado de Imágenes
- Almacenamiento como LONGBLOB en MySQL
- Uso de `send_long_data()` para imágenes grandes
- Validación de tipos de archivo permitidos
- Detección automática de formato de imagen
- Servicio de imágenes con caché HTTP
- Soporte para transparencia y múltiples formatos

### Seguridad Implementada
- Consultas preparadas para prevenir inyección SQL
- Validación de tipos de archivo en subida de imágenes
- Sanitización de datos de entrada
- Confirmaciones dobles para acciones destructivas
- Manejo seguro de errores

## Comandos Útiles

### Gestión de Docker
```bash
# Iniciar todos los servicios
docker-compose up -d

# Detener todos los servicios
docker-compose down

# Ver logs de los servicios
docker-compose logs

# Reiniciar servicios específicos
docker-compose restart web
docker-compose restart db

# Acceder al contenedor PHP
docker exec -it practice10-web-1 sh

# Acceder al contenedor MySQL
docker exec -it practice10-db-1 mysql -u amg -pamg bd
```

### Gestión de Base de Datos
```bash
# Hacer backup de la base de datos
docker-compose exec db mysqldump -u amg -pamg bd > backup_libreria.sql

# Restaurar base de datos desde backup
docker-compose exec -T db mysql -u amg -pamg bd < backup_libreria.sql

# Verificar tablas existentes
docker-compose exec db mysql -u amg -pamg bd -e "SHOW TABLES;"

# Ver estructura de la tabla libros
docker-compose exec db mysql -u amg -pamg bd -e "DESCRIBE libros;"
```

## Modificaciones Técnicas Realizadas

### Migración de PDO a MySQLi
- Reemplazo completo de PDO por MySQLi para mejor compatibilidad
- Implementación de `bind_param()` con tipos específicos
- Uso de `send_long_data()` para manejo de datos BLOB grandes
- Manejo mejorado de errores con MySQLi

### Sistema de Imágenes
- Creación de `imagen.php` como servidor dedicado de imágenes
- Implementación de detección automática de tipos MIME
- Configuración de cabeceras HTTP para optimización de caché
- Validación exhaustiva de tipos de archivo permitidos
- Manejo de errores con imagen por defecto en formato SVG

### Mejoras de Seguridad
- Validación de tipos de archivo en el servidor
- Implementación de confirmaciones dobles para operaciones críticas
- Sanitización completa de datos de entrada
- Uso exclusivo de consultas preparadas

### Optimizaciones de Rendimiento
- Implementación de caché HTTP para imágenes
- Optimización de consultas de base de datos
- Manejo eficiente de memoria para archivos BLOB
- Estructura de base de datos optimizada con índices apropiados

## Solución de Problemas Comunes

### Error: Call to undefined function imagecreate()
**Solución**: Instalar la extensión GD siguiendo los pasos de configuración.

### Imágenes no se muestran
**Causa**: Extensión GD no instalada o `imagen.php` no accesible.
**Solución**: Verificar instalación de extensiones y permisos de archivos.

### Error de conexión a MySQL
**Causa**: Contenedor de base de datos no iniciado o credenciales incorrectas.
**Solución**: Verificar estado de contenedores con `docker-compose ps`.

### Archivos de imagen muy grandes
**Causa**: Límites de PHP para subida de archivos.
**Solución**: Configurar `upload_max_filesize` y `post_max_size` en PHP.

## URLs del Sistema
- **Aplicación principal**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Registro de libros**: http://localhost:8080/registro.php
- **Consulta de libros**: http://localhost:8080/consulta.php
- **Administración**: http://localhost:8080/limpiar_bd.php

## Credenciales de Acceso
- **Base de datos MySQL**:
  - Host: `db` (interno) / `localhost:3306` (externo)
  - Usuario: `amg`
  - Contraseña: `amg`
  - Base de datos: `bd`

- **phpMyAdmin**:
  - Servidor: `db`
  - Usuario: `amg`
  - Contraseña: `amg`

## Control de Versiones
El proyecto utiliza Git para control de versiones con `.gitignore` configurado para excluir:
- Datos de MySQL (`mysql_data/`)
- Archivos de log y temporales
- Archivos del sistema operativo
- Configuraciones de IDE

## Notas de Desarrollo
- El sistema utiliza MySQLi exclusivamente para todas las operaciones de base de datos
- Las imágenes se almacenan como LONGBLOB directamente en MySQL
- El diseño es completamente responsive y compatible con dispositivos móviles
- Todas las validaciones se realizan tanto en cliente como en servidor
- El sistema está optimizado para rendimiento y seguridad

## Créditos
Desarrollado como parte de la Práctica 10 del curso de Programación en Internet.
Sistema completo de gestión de librería con arquitectura moderna basada en contenedores Docker.