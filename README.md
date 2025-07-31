# # 📁 Sistema Modular de Gestión de Archivos

Este proyecto es una solución moderna y modular para gestionar archivos en una aplicación web usando **PHP**, **MySQL**, **Bootstrap 5**, **jQuery/AJAX** y validaciones del lado cliente. Permite a los usuarios subir, editar, listar y eliminar archivos con una interfaz responsiva y amigable.

<img width="1366" height="647" alt="image" src="https://github.com/user-attachments/assets/9f4a6ee9-1d18-4bf8-8827-034e3eb3733f" />

## 🚀 Características principales

✅ **Carga de archivos** con nombre cifrado  
✅ **Validación de formato y tamaño**  
✅ **Interfaz responsiva** con Bootstrap 5  
✅ **Interacción asincrónica** vía AJAX  
✅ **CRUD completo** desde un modal  
✅ **Mensajes JSON y control de errores**

---

## 🧱 Estructura del sistema

/index.html ← Interfaz principal del sistema

/controlador.php ← Backend para CRUD y gestión de archivos

/validacion.js ← Validaciones de archivos del lado cliente

/archivos/ ← Carpeta donde se almacenan los archivos subidos

---

## 🧰 Tecnologías utilizadas

- **PHP 5.6+**
- **MySQL**
- **Bootstrap 5.3**
- **jQuery 3.6**
- **AJAX con `$.post()`**
- **JavaScript**

---

## 📂 Estructura de la base de datos

Tabla: `archivo`

```sql
CREATE DATABASE IF NOT EXISTS db_test;
USE db_test;

CREATE TABLE archivo (
  `id_arc` int(11) NOT NULL,
  `fec_arc` datetime DEFAULT CURRENT_TIMESTAMP,
  `arc_arc` varchar(500) NOT NULL,
  `nom_arc` varchar(500) NOT NULL,
  `des_arc` text,
  `est_arc` varchar(50) DEFAULT NULL,
  `for_arc` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
