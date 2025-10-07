# Proyecto MT - Programación en Internet

**Autor:** Ariel Martínez González  
**ID:** 00379435  
**Universidad:** Anahuac México Norte  
**Facultad:** Ingeniería  

## 📁 Estructura del Proyecto

```
MT/P1/
├── index.html              # Página principal
├── estilos.css            # Hoja de estilos unificada
├── .htaccess              # Configuración del servidor web
├── README.md              # Este archivo
├── estilos-backup.css     # Respaldo del CSS original
├── assets/                # Recursos multimedia
│   ├── favicon.png
│   ├── faviconB.png
│   ├── LogoAMG.png
│   ├── LogoAMG.webp
│   └── LogoBlanco.png
└── paginas/               # Páginas de prácticas
    ├── P2/                # Conversor de Unidades
    │   ├── P2.html
    │   └── converter.js
    ├── practica2/
    │   └── practica2.html
    ├── practica3/
    │   ├── practica3.html
    │   ├── gemini.html
    │   └── assets/
    │       └── images/
    ├── practica4/
    │   ├── practica4.html
    │   ├── factorial.js
    │   ├── calificacion.js
    │   └── info.js
    ├── practica5/
    │   ├── practica5.html
    │   └── script.js
    ├── practica6/
    │   ├── practica6.html
    │   ├── audio.mp3
    │   └── video.mp4
    └── practica7/
        ├── practica7.html
        ├── docker-compose.yml
        ├── html/
        │   ├── e.php
        │   └── pi.php
        └── mysql_data/
```

## 🚀 Características

### ✅ **Estructura Optimizada**
- **Rutas consistentes** en todas las páginas
- **CSS unificado** con variables CSS y organización modular
- **Nomenclatura estandarizada** de archivos
- **Eliminación de duplicados** y archivos innecesarios

### 🎨 **Diseño Responsivo**
- **Breakpoints optimizados** para móviles y tablets
- **Efectos visuales** con gradientes y animaciones
- **Consistencia visual** en todas las prácticas
- **Accesibilidad mejorada** con contrastes y tipografía

### ⚡ **Optimización Web**
- **Archivo .htaccess** configurado para producción
- **Compresión habilitada** para mejor rendimiento
- **Cache de archivos estáticos** configurado
- **Headers de seguridad** implementados

## 📋 Contenido de las Prácticas

| Práctica | Descripción | Tecnologías |
|----------|-------------|-------------|
| **Práctica 2** | Página básica con estructura HTML | HTML, CSS |
| **Práctica 3** | Calculadora Gemini y tabla de calificaciones | HTML, CSS, JavaScript |
| **Práctica 4** | Calculadora de factorial y evaluador | HTML, CSS, JavaScript |
| **Práctica 5** | Formulario interactivo con validación | HTML, CSS, JavaScript |
| **Práctica 6** | Reproductor multimedia | HTML, CSS, Audio/Video |
| **Práctica 7** | Aplicación web con Docker y MySQL | PHP, Docker, MySQL |
| **Parte 2** | Conversor universal de unidades | HTML, CSS, JavaScript |

## 🔄 Conversor de Unidades (Parte 2)

### Funcionalidades:
- **🌡️ Temperatura:** Celsius, Fahrenheit, Kelvin, Rankine
- **📏 Distancia:** Metros, Kilómetros, Millas, Pies, Pulgadas
- **⏰ Tiempo:** Segundos, Minutos, Horas, Días, Semanas, Meses, Años
- **💰 Moneda:** USD, EUR, MXN, GBP, JPY, CAD

### Características técnicas:
- ✅ Conversión en tiempo real
- ✅ Interfaz intuitiva con selector de categorías
- ✅ Validación de entrada y manejo de errores
- ✅ Botón de intercambio de unidades
- ✅ Diseño responsivo

## 🌐 Despliegue en public_html

### Requisitos del servidor:
- **Apache** con mod_rewrite habilitado
- **PHP** 7.4+ (para Práctica 7)
- **Soporte** para .htaccess

### Pasos de instalación:
1. Subir toda la carpeta `P1/` al directorio `public_html/`
2. Verificar permisos de archivos (644 para archivos, 755 para directorios)
3. Comprobar que `.htaccess` esté presente y activo
4. Acceder via navegador a `http://tudominio.com/`

### URLs de acceso:
- **Página principal:** `/`
- **Práctica 2:** `/paginas/practica2/practica2.html`
- **Práctica 3:** `/paginas/practica3/practica3.html`
- **Práctica 4:** `/paginas/practica4/practica4.html`
- **Práctica 5:** `/paginas/practica5/practica5.html`
- **Práctica 6:** `/paginas/practica6/practica6.html`
- **Práctica 7:** `/paginas/practica7/practica7.html`
- **Conversor P2:** `/paginas/P2/P2.html`

## 🔧 Tecnologías Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript ES6+
- **Backend:** PHP (Práctica 7)
- **Base de datos:** MySQL (Práctica 7)
- **Containerización:** Docker (Práctica 7)
- **Multimedia:** Audio/Video HTML5
- **Responsive Design:** CSS Grid, Flexbox, Media Queries

## 📱 Compatibilidad

- ✅ **Chrome** 90+
- ✅ **Firefox** 88+
- ✅ **Safari** 14+
- ✅ **Edge** 90+
- ✅ **Móviles** iOS/Android

## 🎯 Características de Calidad

### 🏗️ **Estructura:**
- Organización lógica de archivos y carpetas
- Nomenclatura consistente
- Separación de responsabilidades

### 🎨 **Diseño:**
- Interfaz moderna y atractiva
- Colores y tipografía consistentes
- Animaciones y efectos visuales

### ⚡ **Rendimiento:**
- CSS optimizado y minificado conceptualmente
- Imágenes en formatos web modernos (WebP)
- Carga asíncrona de recursos

### 🔒 **Seguridad:**
- Headers de seguridad configurados
- Protección contra acceso a archivos sensibles
- Validación de entrada en formularios

---

**© 2024 Ariel Martínez González - Universidad Anahuac México Norte**