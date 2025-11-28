CREATE DATABASE BD_AWCS_III25;
 
USE BD_AWCS_III25;
 
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  fecha_nacimiento DATE,
  genero ENUM('masculino', 'femenino', 'otro'),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tareaUsuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tarea_nombre VARCHAR(150) NOT NULL,
  descripcion VARCHAR(50),
  estado ENUM('pendiente', 'en_progreso', 'completada') DEFAULT 'pendiente',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  usuario_id INT NOT NULL,
  url_imagen VARCHAR(255),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

