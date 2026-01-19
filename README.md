# S.I.M.A – Sistema de Información de Matrículas Académicas

El **Sistema de Información de Matrículas Académicas (S.I.M.A)** fue desarrollado durante las prácticas pre-profesionales con el objetivo de digitalizar y optimizar el proceso de matrícula estudiantil en una institución educativa, permitiendo el registro, actualización, validación y gestión de la información académica y documental de los estudiantes.

El sistema fue desarrollado utilizando **PHP, MySQL, HTML, CSS y JavaScript**, bajo la **arquitectura MVC (Modelo – Vista – Controlador)**, lo que permite una mejor organización, mantenimiento y escalabilidad del proyecto.

---

## 📌 Requisitos del sistema

Para ejecutar correctamente el proyecto es necesario contar con:

- Servidor local (**XAMPP** o **WAMP**)
- PHP versión 7.4 o superior
- MySQL
- Navegador web (Google Chrome, Edge o Firefox)

---

## 📂 Descarga y ubicación del proyecto

1. Descargar el proyecto desde este repositorio de GitHub.
2. Extraer todos los archivos del proyecto.
3. Copiar la carpeta del sistema dentro de la ruta del servidor local:
   - **XAMPP:** `htdocs`
   - **WAMP:** `www`

Ejemplo: C:\xampp\htdocs\Matriculas_Maria_Currea

---

## 🗄️ Configuración de la base de datos

1. Abrir **phpMyAdmin** desde el servidor local.
2. Crear una base de datos :sistema_matriculas.
3. Importar el archivo `.sql` incluido en el proyecto (ubicado en la carpeta `/database`).
4. Verificar que todas las tablas se hayan creado correctamente.

---

## ⚙️ Configuración de conexión a la base de datos

Editar el archivo:/config/database.php

Configuración recomendada para entorno local:

```php
$host = "localhost";
$db_name = "matriculas_maria_currea";
$username = "root";
$password = "";

El sistema utiliza la librería mPDF para la generación de archivos PDF (hojas de matrícula).

Debido a limitaciones de tamaño, esta librería puede no estar incluida completamente en el repositorio, por lo que debe descargarse manualmente.

Accesos al sistema
Acceso Administrador

Usuario (documento): 123456789

Contraseña: password

