# 📦 Guía de Instalación Detallada

Esta guía te ayudará a instalar y configurar el proyecto de E-Commerce paso a paso.

## 📋 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Instalación de Docker](#instalación-de-docker)
3. [Configuración del Proyecto](#configuración-del-proyecto)
4. [Inicialización de la Base de Datos](#inicialización-de-la-base-de-datos)
5. [Verificación de la Instalación](#verificación-de-la-instalación)
6. [Solución de Problemas](#solución-de-problemas)
7. [Configuración Avanzada](#configuración-avanzada)

---

## 1. 📋 Requisitos Previos

### Sistema Operativo
- Windows 10/11 (64-bit)
- macOS 10.15 o superior
- Linux (Ubuntu 20.04+, Debian 10+, CentOS 8+)

### Software Requerido
- Docker Desktop 4.0 o superior
- Git 2.30 o superior
- Navegador web moderno

### Recursos del Sistema
- **RAM**: Mínimo 4GB, recomendado 8GB
- **Espacio en disco**: Mínimo 2GB libres
- **CPU**: Procesador de 64 bits

---

## 2. 🐳 Instalación de Docker

### Windows

1. Descarga Docker Desktop desde: https://www.docker.com/products/docker-desktop

2. Ejecuta el instalador y sigue las instrucciones

3. Reinicia tu computadora cuando se te solicite

4. Abre Docker Desktop y espera a que se inicie

5. Verifica la instalación en PowerShell:
```powershell
docker --version
docker-compose --version
```

### macOS

1. Descarga Docker Desktop para Mac desde: https://www.docker.com/products/docker-desktop

2. Abre el archivo .dmg y arrastra Docker a Aplicaciones

3. Abre Docker desde Aplicaciones

4. Verifica la instalación en Terminal:
```bash
docker --version
docker-compose --version
```

### Linux (Ubuntu/Debian)

```bash
# Actualizar sistema
sudo apt update
sudo apt upgrade -y

# Instalar dependencias
sudo apt install apt-transport-https ca-certificates curl software-properties-common -y

# Agregar repositorio de Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"

# Instalar Docker
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io -y

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Agregar usuario al grupo docker
sudo usermod -aG docker $USER

# Aplicar cambios (reloguear o reiniciar)
newgrp docker

# Verificar instalación
docker --version
docker-compose --version
```

---

## 3. ⚙️ Configuración del Proyecto

### Paso 1: Clonar el Repositorio

```bash
# Crear directorio de trabajo
mkdir -p ~/proyectos
cd ~/proyectos

# Clonar repositorio
git clone https://github.com/Ariel-MG/Internet-Programing.git

# Navegar al proyecto
cd Internet-Programing/ProyectoFinal
```

### Paso 2: Verificar Archivos

Asegúrate de tener los siguientes archivos:

```
ProyectoFinal/
├── docker-compose.yml  ← Configuración de Docker
├── html/              ← Código de la aplicación
└── mysql_data/        ← Se creará automáticamente
```

### Paso 3: Revisar Configuración de Docker

Abre `docker-compose.yml` y verifica los puertos:

```yaml
services:
  web:
    ports:
      - "8080:80"      # Aplicación web
  
  db:
    ports:
      - "3307:3306"    # Base de datos MySQL
  
  phpmyadmin:
    ports:
      - "8081:80"      # phpMyAdmin
```

**Nota**: Si algún puerto está ocupado, puedes cambiar el número del lado izquierdo (ej: "8090:80").

### Paso 4: Levantar los Contenedores

```bash
# Iniciar servicios en segundo plano
docker-compose up -d

# Ver logs en tiempo real (opcional)
docker-compose logs -f

# Verificar que los contenedores estén corriendo
docker-compose ps
```

Deberías ver algo como:

```
NAME                    SERVICE    STATUS    PORTS
proyectofinal-web-1     web        running   0.0.0.0:8080->80/tcp
proyectofinal-db-1      db         running   0.0.0.0:3307->3306/tcp
proyectofinal-phpmyadmin-1 phpmyadmin running 0.0.0.0:8081->80/tcp
```

---

## 4. 🗄️ Inicialización de la Base de Datos

### Paso 1: Acceder al Script de Configuración

Abre tu navegador y ve a:
```
http://localhost:8080/setup_db.php
```

### Paso 2: Ejecutar la Inicialización

El script automáticamente:
- ✅ Crea la base de datos `ecomerce`
- ✅ Crea las 8 tablas necesarias
- ✅ Inserta usuarios de prueba
- ✅ Inserta categorías de ejemplo
- ✅ Opcionalmente inserta productos de muestra

### Paso 3: Verificar en phpMyAdmin

1. Accede a: http://localhost:8081

2. Credenciales de acceso:
   - **Servidor**: db
   - **Usuario**: root
   - **Contraseña**: root_password

3. Selecciona la base de datos `ecomerce`

4. Verifica que existan estas tablas:
   - usuarios
   - categorias
   - productos
   - carrito_compras
   - pedidos
   - detalle_pedidos
   - resenas
   - sesiones

---

## 5. ✅ Verificación de la Instalación

### Verificación Básica

1. **Página de Inicio**
   - URL: http://localhost:8080
   - Debe cargar la página principal con productos destacados

2. **Página de Productos**
   - URL: http://localhost:8080/productos.php
   - Debe mostrar el catálogo completo

3. **Login**
   - URL: http://localhost:8080/login.php
   - Prueba con: admin@tienda.com / admin123

4. **Panel Admin**
   - URL: http://localhost:8080/admin/
   - Debe mostrar el dashboard (requiere login como admin)

### Verificación de Funcionalidades

**Test 1: Registro de Usuario**
```
1. Ir a /registro.php
2. Llenar el formulario
3. Hacer clic en "Registrarse"
4. Verificar que redirige al login
```

**Test 2: Agregar al Carrito**
```
1. Iniciar sesión
2. Ir a un producto
3. Hacer clic en "Agregar al carrito"
4. Verificar mensaje de éxito
5. Ir a /carrito.php
6. Verificar que el producto esté listado
```

**Test 3: Realizar Pedido**
```
1. Con productos en el carrito
2. Ir a /checkout.php
3. Llenar datos de envío
4. Hacer clic en "Realizar Pedido"
5. Verificar mensaje de éxito
6. Ir a /mis_pedidos.php
7. Verificar que el pedido aparezca
```

**Test 4: Panel Admin**
```
1. Login como admin
2. Ir a /admin/
3. Verificar métricas en dashboard
4. Ir a /admin/productos.php
5. Agregar un producto nuevo
6. Verificar que aparezca en la lista
```

---

## 6. 🔧 Solución de Problemas

### Problema: Puerto ya en uso

**Error**: `Bind for 0.0.0.0:8080 failed: port is already allocated`

**Solución**:
```bash
# Detener contenedores
docker-compose down

# Editar docker-compose.yml y cambiar el puerto
# Por ejemplo, cambiar "8080:80" a "8090:80"

# Reiniciar
docker-compose up -d
```

### Problema: No se puede conectar a MySQL

**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solución**:
```bash
# Verificar que el contenedor de DB esté corriendo
docker-compose ps

# Ver logs de la base de datos
docker-compose logs db

# Reiniciar servicio
docker-compose restart db

# Esperar 30 segundos y probar nuevamente
```

### Problema: Permisos en Linux

**Error**: `Permission denied` al subir imágenes

**Solución**:
```bash
# Dar permisos a la carpeta de imágenes
sudo chmod -R 777 html/assets/img/

# O cambiar el propietario
sudo chown -R www-data:www-data html/assets/img/
```

### Problema: Página en blanco

**Solución**:
```bash
# Ver errores de PHP
docker-compose logs web

# Verificar archivos de configuración
ls -la html/config/

# Reiniciar contenedor web
docker-compose restart web
```

### Problema: Setup DB no funciona

**Solución**:
```bash
# Entrar al contenedor de MySQL
docker-compose exec db mysql -uroot -proot_password

# Crear base de datos manualmente
CREATE DATABASE IF NOT EXISTS ecomerce;
USE ecomerce;

# Salir
exit

# Volver a ejecutar setup_db.php
```

### Problema: Docker Desktop no inicia

**Windows**:
1. Activar virtualización en BIOS
2. Activar Hyper-V en Windows Features
3. Reiniciar computadora

**Mac**:
1. Verificar permisos en Preferencias del Sistema
2. Reinstalar Docker Desktop
3. Reiniciar computadora

---

## 7. 🔧 Configuración Avanzada

### Cambiar Credenciales de Base de Datos

1. Editar `docker-compose.yml`:
```yaml
db:
  environment:
    MYSQL_ROOT_PASSWORD: tu_nueva_contraseña
    MYSQL_DATABASE: ecomerce
    MYSQL_USER: tu_usuario
    MYSQL_PASSWORD: tu_contraseña
```

2. Editar `html/config/db.php`:
```php
$host = 'db';
$dbname = 'ecomerce';
$username = 'tu_usuario';
$password = 'tu_contraseña';
```

3. Recrear contenedores:
```bash
docker-compose down -v
docker-compose up -d
```

### Configurar Volúmenes Persistentes

Para que los datos persistan incluso al eliminar contenedores:

```yaml
volumes:
  mysql_data:
    driver: local

services:
  db:
    volumes:
      - mysql_data:/var/lib/mysql
```

### Habilitar Logs de PHP

Editar `docker-compose.yml`:
```yaml
web:
  environment:
    - PHP_ERROR_REPORTING=E_ALL
    - PHP_DISPLAY_ERRORS=On
```

### Configurar Límites de Memoria

```yaml
db:
  deploy:
    resources:
      limits:
        memory: 2G
      reservations:
        memory: 1G
```

### Backup de Base de Datos

```bash
# Crear backup
docker-compose exec db mysqldump -uroot -proot_password ecomerce > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -uroot -proot_password ecomerce < backup.sql
```

---

## 🎯 Comandos Útiles

```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs de todos los servicios
docker-compose logs

# Ver logs de un servicio específico
docker-compose logs web
docker-compose logs db

# Detener servicios
docker-compose stop

# Iniciar servicios detenidos
docker-compose start

# Reiniciar un servicio
docker-compose restart web

# Detener y eliminar contenedores
docker-compose down

# Eliminar también los volúmenes
docker-compose down -v

# Reconstruir imágenes
docker-compose build

# Ver recursos utilizados
docker stats

# Limpiar sistema Docker
docker system prune -a
```

---

## 📞 Obtener Ayuda

Si sigues teniendo problemas:

1. Verifica los logs: `docker-compose logs`
2. Revisa el archivo de errores de PHP en el contenedor
3. Consulta la documentación de Docker
4. Busca el error en GitHub Issues
5. Crea un nuevo issue con los detalles del problema

---

## ✅ Checklist de Instalación

- [ ] Docker Desktop instalado y corriendo
- [ ] Repositorio clonado
- [ ] Contenedores levantados con `docker-compose up -d`
- [ ] Base de datos inicializada con `setup_db.php`
- [ ] Página de inicio carga correctamente
- [ ] Puedes hacer login
- [ ] Panel admin accesible
- [ ] Puedes agregar productos al carrito
- [ ] Puedes realizar un pedido de prueba

---

**¡Felicidades! Tu instalación está completa y lista para usar.**

Para empezar a usar el sistema, visita http://localhost:8080

---

*Si esta guía te fue útil, por favor compártela con otros estudiantes.*
