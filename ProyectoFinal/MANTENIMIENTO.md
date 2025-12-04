# 🔧 Guía de Mantenimiento y Administración

## 📋 Tabla de Contenidos

1. [Tareas de Mantenimiento Diario](#tareas-diarias)
2. [Tareas Semanales](#tareas-semanales)
3. [Tareas Mensuales](#tareas-mensuales)
4. [Gestión de Base de Datos](#gestión-de-base-de-datos)
5. [Monitoreo del Sistema](#monitoreo-del-sistema)
6. [Seguridad](#seguridad)
7. [Backup y Restauración](#backup-y-restauración)
8. [Solución de Problemas Comunes](#solución-de-problemas)
9. [Actualizaciones](#actualizaciones)
10. [Mejores Prácticas](#mejores-prácticas)

---

## 1. ✅ Tareas de Mantenimiento Diario

### Revisar Pedidos Pendientes

```
1. Acceder a /admin/pedidos.php
2. Filtrar por estado "Pendiente"
3. Procesar cada pedido:
   - Verificar información del cliente
   - Validar disponibilidad de productos
   - Cambiar estado a "Procesando"
```

### Actualizar Estados de Pedidos

```
1. Pedidos en "Procesando" → "Enviado" (cuando se despacha)
2. Pedidos en "Enviado" → "Entregado" (cuando llega)
3. Notificar al cliente por email (función futura)
```

### Monitorear Stock

```
1. Ir a /admin/
2. Revisar sección "Productos Bajo Stock"
3. Reabastecer productos críticos
4. Actualizar cantidades en /admin/productos.php
```

### Revisar Logs

```bash
# Ver logs de Docker
docker-compose logs web --tail=100

# Buscar errores
docker-compose logs web | grep -i error

# Ver logs de MySQL
docker-compose logs db --tail=50
```

---

## 2. 📊 Tareas Semanales

### Análisis de Ventas

1. **Acceder al Dashboard**
   - Revisar métricas de ventas totales
   - Comparar con semana anterior
   - Identificar tendencias

2. **Productos Más Vendidos**
   ```sql
   SELECT p.nombre, SUM(dp.cantidad) as total_vendido
   FROM detalle_pedidos dp
   JOIN productos p ON dp.id_producto = p.id_producto
   JOIN pedidos ped ON dp.id_pedido = ped.id_pedido
   WHERE ped.fecha_pedido >= DATE_SUB(NOW(), INTERVAL 7 DAY)
   GROUP BY p.id_producto
   ORDER BY total_vendido DESC
   LIMIT 10;
   ```

3. **Revisar Reseñas Nuevas**
   ```sql
   SELECT r.*, u.nombre as usuario, p.nombre as producto
   FROM resenas r
   JOIN usuarios u ON r.id_usuario = u.id_usuario
   JOIN productos p ON r.id_producto = p.id_producto
   WHERE r.fecha_resena >= DATE_SUB(NOW(), INTERVAL 7 DAY)
   ORDER BY r.fecha_resena DESC;
   ```

### Limpieza de Carritos Abandonados

```sql
-- Eliminar carritos de más de 30 días
DELETE FROM carrito_compras 
WHERE fecha_agregado < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Actualizar Imágenes

1. Revisar productos sin imagen
2. Agregar imágenes de calidad
3. Optimizar imágenes existentes (reducir tamaño si es necesario)

```bash
# Encontrar imágenes grandes
find html/assets/img -type f -size +1M
```

---

## 3. 📅 Tareas Mensuales

### Backup Completo

```bash
# Crear directorio de backups
mkdir -p backups/$(date +%Y-%m)

# Backup de base de datos
docker-compose exec db mysqldump -uroot -proot_password ecomerce > backups/$(date +%Y-%m)/db_backup_$(date +%Y%m%d).sql

# Backup de imágenes
tar -czf backups/$(date +%Y-%m)/images_backup_$(date +%Y%m%d).tar.gz html/assets/img/

# Backup de código
tar -czf backups/$(date +%Y-%m)/code_backup_$(date +%Y%m%d).tar.gz html/ --exclude='html/assets/img'
```

### Análisis de Usuarios

```sql
-- Usuarios más activos
SELECT u.id_usuario, u.nombre, u.email, 
       COUNT(p.id_pedido) as total_pedidos,
       SUM(p.total) as total_gastado
FROM usuarios u
LEFT JOIN pedidos p ON u.id_usuario = p.id_usuario
GROUP BY u.id_usuario
ORDER BY total_pedidos DESC
LIMIT 20;

-- Nuevos registros del mes
SELECT COUNT(*) as nuevos_usuarios
FROM usuarios
WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Mantenimiento de Base de Datos

```sql
-- Optimizar tablas
OPTIMIZE TABLE productos;
OPTIMIZE TABLE pedidos;
OPTIMIZE TABLE usuarios;
OPTIMIZE TABLE detalle_pedidos;

-- Analizar tablas
ANALYZE TABLE productos;
ANALYZE TABLE pedidos;

-- Revisar integridad
CHECK TABLE productos;
CHECK TABLE pedidos;
```

### Revisión de Seguridad

1. Cambiar contraseña de admin mensualmente
2. Revisar usuarios con rol admin
3. Auditar accesos al panel de administración
4. Verificar permisos de archivos

```bash
# Revisar permisos
ls -la html/assets/img/
ls -la html/config/

# Asegurar que config no sea público
chmod 640 html/config/db.php
```

---

## 4. 🗄️ Gestión de Base de Datos

### Acceso a MySQL

**Vía phpMyAdmin:**
```
URL: http://localhost:8081
Usuario: root
Contraseña: root_password
```

**Vía Terminal:**
```bash
# Acceder al contenedor
docker-compose exec db bash

# Conectar a MySQL
mysql -uroot -proot_password ecomerce
```

### Consultas Útiles

**Ver tamaño de la base de datos:**
```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'ecomerce'
GROUP BY table_schema;
```

**Ver tamaño por tabla:**
```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'ecomerce'
ORDER BY (data_length + index_length) DESC;
```

**Estadísticas rápidas:**
```sql
-- Total de registros
SELECT 
    'Productos' as tabla, COUNT(*) as total FROM productos
UNION ALL
SELECT 'Usuarios', COUNT(*) FROM usuarios
UNION ALL
SELECT 'Pedidos', COUNT(*) FROM pedidos
UNION ALL
SELECT 'Categorías', COUNT(*) FROM categorias;
```

### Limpieza de Datos

**Sesiones expiradas:**
```sql
DELETE FROM sesiones 
WHERE fecha_expiracion < NOW();
```

**Pedidos cancelados antiguos:**
```sql
-- Opcional: archivar pedidos cancelados de más de 6 meses
SELECT * INTO OUTFILE '/tmp/pedidos_cancelados_archivados.csv'
FROM pedidos 
WHERE estado = 'cancelado' 
AND fecha_pedido < DATE_SUB(NOW(), INTERVAL 6 MONTH);
```

---

## 5. 📈 Monitoreo del Sistema

### Métricas de Docker

```bash
# Ver uso de recursos
docker stats

# Ver espacio en disco
docker system df

# Limpiar recursos no usados
docker system prune -a
```

### Logs Importantes

**Errores de PHP:**
```bash
# Ver últimos 100 errores
docker-compose logs web | grep -i error | tail -100

# Monitorear en tiempo real
docker-compose logs -f web
```

**Errores de MySQL:**
```bash
# Ver logs de base de datos
docker-compose logs db | grep -i error

# Ver consultas lentas
docker-compose exec db cat /var/log/mysql/slow-query.log
```

### Monitoreo de Rendimiento

**Verificar tiempo de respuesta:**
```bash
# Medir tiempo de carga de la página principal
curl -o /dev/null -s -w "Time: %{time_total}s\n" http://localhost:8080/
```

**Ver conexiones MySQL activas:**
```sql
SHOW PROCESSLIST;
```

**Ver uso de base de datos:**
```sql
SHOW STATUS LIKE 'Threads_connected';
SHOW STATUS LIKE 'Threads_running';
SHOW STATUS LIKE 'Questions';
SHOW STATUS LIKE 'Uptime';
```

---

## 6. 🔐 Seguridad

### Checklist de Seguridad

✅ **Configuración:**
- [ ] PHP error_reporting = Off en producción
- [ ] display_errors = Off en producción
- [ ] Archivos de configuración no públicos
- [ ] Contraseñas de DB seguras

✅ **Base de Datos:**
- [ ] Usar prepared statements (ya implementado)
- [ ] No exponer IDs en URLs sensibles
- [ ] Validar todos los inputs
- [ ] Sanitizar outputs

✅ **Archivos:**
- [ ] Validar tipo de archivo en uploads
- [ ] Límite de tamaño en uploads
- [ ] Nombres aleatorios para archivos subidos
- [ ] Carpeta de uploads fuera de root si es posible

✅ **Sesiones:**
- [ ] Sesiones con cookies httpOnly
- [ ] Regenerar session ID en login
- [ ] Timeout de sesión configurado
- [ ] Logout limpia todas las variables

### Auditoría de Seguridad

**Revisar usuarios admin:**
```sql
SELECT id_usuario, nombre, email, fecha_registro 
FROM usuarios 
WHERE tipo_usuario = 'admin';
```

**Últimos logins:**
```sql
SELECT u.nombre, s.fecha_creacion 
FROM sesiones s
JOIN usuarios u ON s.id_usuario = u.id_usuario
ORDER BY s.fecha_creacion DESC
LIMIT 20;
```

**Actividad sospechosa:**
```sql
-- Múltiples pedidos en poco tiempo
SELECT u.nombre, COUNT(*) as pedidos
FROM pedidos p
JOIN usuarios u ON p.id_usuario = u.id_usuario
WHERE p.fecha_pedido > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY u.id_usuario
HAVING pedidos > 5;
```

---

## 7. 💾 Backup y Restauración

### Backup Automático

Crear script de backup automático:

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/ruta/a/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup de base de datos
docker-compose exec -T db mysqldump -uroot -proot_password ecomerce > \
    "$BACKUP_DIR/db_$DATE.sql"

# Comprimir
gzip "$BACKUP_DIR/db_$DATE.sql"

# Backup de imágenes
tar -czf "$BACKUP_DIR/images_$DATE.tar.gz" html/assets/img/

# Eliminar backups antiguos (más de 30 días)
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +30 -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +30 -delete

echo "Backup completado: $DATE"
```

**Agendar con cron (Linux/Mac):**
```bash
# Editar crontab
crontab -e

# Agregar línea (ejecutar diario a las 2 AM)
0 2 * * * /ruta/a/backup.sh >> /ruta/a/backup.log 2>&1
```

### Restauración

**Restaurar base de datos:**
```bash
# Descomprimir backup
gunzip db_20251204_020000.sql.gz

# Restaurar
docker-compose exec -T db mysql -uroot -proot_password ecomerce < db_20251204_020000.sql
```

**Restaurar imágenes:**
```bash
# Extraer backup
tar -xzf images_20251204_020000.tar.gz -C html/assets/
```

### Backup Incremental

Para bases de datos grandes, configurar replicación o binlog:

```sql
-- Habilitar binlog en MySQL (my.cnf)
[mysqld]
server-id = 1
log_bin = /var/log/mysql/mysql-bin.log
expire_logs_days = 10
max_binlog_size = 100M
```

---

## 8. 🔧 Solución de Problemas Comunes

### Problema: Base de datos no responde

```bash
# Ver estado del contenedor
docker-compose ps db

# Reiniciar servicio
docker-compose restart db

# Ver logs para errores
docker-compose logs db | tail -50

# Si es necesario, recrear contenedor
docker-compose down db
docker-compose up -d db
```

### Problema: Imágenes no se muestran

```bash
# Verificar permisos
ls -la html/assets/img/

# Dar permisos (desarrollo)
chmod -R 755 html/assets/img/

# Verificar espacio en disco
df -h

# Verificar que las imágenes existan
ls -lh html/assets/img/
```

### Problema: Sesiones no persisten

```php
// Verificar en config que session_start() se llame correctamente
// En includes/functions.php verificar start_session_safe()

// Verificar configuración de PHP session
docker-compose exec web php -i | grep session
```

### Problema: Carrito se vacía al navegar

1. Verificar que las cookies estén habilitadas
2. Revisar configuración de dominio (localhost vs 127.0.0.1)
3. Verificar que `start_session_safe()` se llame en cada página

### Problema: Panel admin no carga

```bash
# Verificar que el usuario sea admin
SELECT * FROM usuarios WHERE email = 'tu_email@ejemplo.com';

# Si no es admin, actualizar:
UPDATE usuarios SET tipo_usuario = 'admin' WHERE email = 'tu_email@ejemplo.com';
```

---

## 9. 🔄 Actualizaciones

### Actualizar Código

```bash
# Hacer backup antes de actualizar
./backup.sh

# Pull últimos cambios
git pull origin main

# Reiniciar contenedores
docker-compose restart web

# Verificar que todo funcione
curl -I http://localhost:8080/
```

### Actualizar Dependencias

**PHP:**
```bash
# Verificar versión actual
docker-compose exec web php -v

# Para actualizar, modificar Dockerfile si es necesario
```

**MySQL:**
```bash
# Backup completo antes de actualizar
./backup.sh

# Modificar versión en docker-compose.yml
# Recrear contenedor
docker-compose down db
docker-compose up -d db
```

### Migraciones de Base de Datos

Crear archivos de migración numerados:

```sql
-- migration_001_add_field.sql
ALTER TABLE productos ADD COLUMN peso DECIMAL(10,2) DEFAULT 0;

-- migration_002_add_table.sql
CREATE TABLE IF NOT EXISTS envios (
    id_envio INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    tracking_number VARCHAR(100),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido)
);
```

Ejecutar:
```bash
docker-compose exec -T db mysql -uroot -proot_password ecomerce < migrations/migration_001_add_field.sql
```

---

## 10. ✨ Mejores Prácticas

### Para Desarrollo

1. **Nunca** modificar directamente en producción
2. **Siempre** hacer backup antes de cambios grandes
3. **Probar** en entorno local primero
4. **Documentar** todos los cambios
5. **Usar** control de versiones (Git)

### Para Producción

1. **Monitorear** logs diariamente
2. **Revisar** métricas semanalmente
3. **Actualizar** software regularmente
4. **Hacer** backups automáticos
5. **Mantener** documentación actualizada

### Código Limpio

```php
// ✅ BUENO: Código legible y documentado
/**
 * Obtiene el total de ventas del mes actual
 * @return float Total de ventas
 */
function obtener_ventas_mes() {
    global $conn;
    $sql = "SELECT SUM(total) as ventas 
            FROM pedidos 
            WHERE MONTH(fecha_pedido) = MONTH(NOW())
            AND YEAR(fecha_pedido) = YEAR(NOW())
            AND estado != 'cancelado'";
    // ... resto del código
}

// ❌ MALO: Sin comentarios, nombres poco claros
function get_vm() {
    global $c;
    $s = "SELECT SUM(t) FROM p WHERE M(f) = M(NOW())";
    // ...
}
```

### Seguridad

```php
// ✅ BUENO: Prepared statements
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// ❌ MALO: Concatenación directa (vulnerable a SQL injection)
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
```

---

## 📞 Contacto de Emergencia

En caso de problemas críticos:

1. **Verificar** estado de servicios: `docker-compose ps`
2. **Revisar** logs: `docker-compose logs`
3. **Restaurar** último backup si es necesario
4. **Contactar** al equipo de desarrollo

---

## 📚 Recursos Adicionales

- [Documentación de Docker](https://docs.docker.com/)
- [Documentación de PHP](https://www.php.net/manual/es/)
- [Documentación de MySQL](https://dev.mysql.com/doc/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)

---

**Mantén este documento actualizado con cada cambio importante en el sistema.**

---

*Última actualización: Diciembre 2025*
