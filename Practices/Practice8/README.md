# Practice 8 - Consulta de Base de Datos con Docker

## Descripción
Aplicación web que muestra los primeros 5 resultados de una consulta SQL utilizando Docker, MySQL, PHP y MySQLi.

## Requisitos Previos
- Docker
- Docker Compose

## Instrucciones de Ejecución

### 1. Levantar los contenedores
```bash
cd Practice8
docker-compose up -d
```

### 2. Configurar la base de datos
Acceder a PHPMyAdmin en: http://localhost:8081
- Usuario: `amg`
- Contraseña: `amg` 
- Servidor: `db`

En la pestaña "SQL" de PHPMyAdmin, copiar y ejecutar todo el contenido del archivo `init_database.sql`

### 3. Acceder a la aplicación
http://localhost:8080

### 4. Detener los contenedores
```bash
docker-compose down
```

## Estructura de la Base de Datos

### Tablas:
- **fabricante**: id (PK), nombre, país, año_fundacion
- **usuarios**: id (PK), nombre, email, teléfono, fecha_registro  
- **modelo**: id (PK), idFab (FK), nombre, año, tipo, precio
- **compra**: id (PK), idModelo (FK), idUsuario (FK), folio, fecha_compra, precio_final

### Consulta SQL Implementada:
```sql
SELECT 
    c.folio,
    u.nombre AS cliente,
    f.nombre AS fabricante,
    m.nombre AS modelo,
    m.anio,
    m.tipo,
    c.precio_final,
    DATE_FORMAT(c.fecha_compra, '%d/%m/%Y') AS fecha_compra
FROM compra c 
JOIN modelo m ON c.idModelo = m.id 
JOIN fabricante f ON m.idFab = f.id 
JOIN usuarios u ON c.idUsuario = u.id 
ORDER BY c.fecha_compra DESC 
LIMIT 5;
```

## Tecnologías Utilizadas
- Docker & Docker Compose
- MySQL 8.1.0
- PHP 8.2 + Apache
- MySQLi
- HTML5 & CSS3
- PHPMyAdmin

## Puertos
- Aplicación web: 8080
- PHPMyAdmin: 8081

## Archivos Principales
- `docker-compose.yml`: Configuración de servicios Docker
- `init_database.sql`: Script de creación de base de datos y datos de prueba
- `html/index.php`: Aplicación principal con conexión MySQLi
- `html/estilos.css`: Estilos CSS responsivos
- `html/test_mysqli.php`: Archivo de prueba de conexión (opcional)