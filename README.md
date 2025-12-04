# Sistema de Gestión de Tareas

Este proyecto es una aplicación web simple para gestionar tareas de usuarios registrados. Está desarrollado en PHP con MySQL y utiliza Bootstrap para la interfaz.

## Características

- **Registro de usuarios**: Los nuevos usuarios pueden crear una cuenta con nombre, correo, usuario, contraseña y fecha de nacimiento.
- **Inicio de sesión**: Los usuarios registrados pueden acceder al sistema.
- **Gestión de tareas**: Crear, editar, eliminar y listar tareas con diferentes estados (pendiente, en progreso, completada, etc.).
- **Dashboard**: Página principal con resumen de tareas y estadísticas.
- **Interfaz responsiva**: Diseñada con Bootstrap para funcionar en dispositivos móviles y de escritorio.

## Tecnologías utilizadas

- **Backend**: PHP
- **Base de datos**: MySQL
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Librerías**: SweetAlert2 para alertas, DataTables para tablas interactivas

## Instalación

1. **Requisitos**:
   - Servidor web con PHP (recomendado XAMPP o similar).
   - MySQL para la base de datos.

2. **Configuración**:
   - Coloca los archivos del proyecto en la carpeta `htdocs` de XAMPP (o equivalente), por ejemplo `C:/xampp/htdocs/Sem3chepeo`.
   - Crea una base de datos en MySQL llamada `sem3chepeo` (o ajusta en `php/conexionBD.php`).
   - Importa las tablas necesarias. Las tablas incluyen:
     - `usuarios`: Para almacenar información de usuarios.
     - `tareaUsuario`: Para las tareas.
     - `estados`: Para los estados de las tareas.

3. **Ejecución**:
   - Inicia Apache y MySQL en XAMPP.
   - Accede a `http://localhost/Sem3chepeo` en tu navegador.
   - Regístrate como nuevo usuario o inicia sesión si ya tienes cuenta.

## Estructura del proyecto

- `index.php`: Página de inicio de sesión.
- `registro.php`: Página de registro de usuarios.
- `home.php`: Dashboard principal después de iniciar sesión.
- `php/`: Carpeta con scripts PHP para conexión a BD, login, registro, etc.
- `Tareas/`: Archivos para gestión de tareas (listar, agregar, editar, eliminar).
- `assets/`: Archivos estáticos (CSS, JS, imágenes).

## Uso

1. Regístrate en la aplicación.
2. Inicia sesión.
3. En el dashboard, verás un resumen de tus tareas.
4. Usa la barra de navegación para acceder a la lista completa de tareas o a la gestión de usuarios (si eres administrador).

## Notas

- Asegúrate de que los permisos de archivos permitan la ejecución de PHP.
- Las contraseñas se almacenan en texto plano; en producción, usa hash (como password_hash en PHP).
- Este proyecto es educativo y no está optimizado para producción.

## Contribución

Si deseas mejorar el proyecto, puedes hacer fork y enviar pull requests.

## Licencia

Este proyecto es de código abierto y se distribuye bajo la licencia MIT.