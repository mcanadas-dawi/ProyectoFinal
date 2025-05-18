# TeamManagerPro

![TeamManagerPro Logo](TeamManagerPro/public/Imagenes/Logo.png)

## 🚀 Aplicación en vivo

La aplicación está desplegada y disponible en:  
[https://www.teammanagerpro.es](https://www.teammanagerpro.es)

---

## 🧪 Credenciales de demostración

- **Usuario:** `demo@example.com`
- **Contraseña:** `password123`

---

## ℹ️ Descripción

**TeamManagerPro** es una aplicación web para entrenadores de fútbol que permite gestionar equipos, jugadores, partidos y estadísticas. Cuenta con un alineador táctico interactivo y un completo sistema de estadísticas para analizar el rendimiento de los jugadores.

---

## 🔧 Requisitos previos

- PHP >= 8.1
- Composer
- Node.js y npm
- Git
- SQLite o MySQL

---

## 🛠️ Instalación local

1. **Clonar el repositorio**
    ```bash
    git clone https://github.com/tuusuario/ProyectoFinalTeamManagerPro.git
    cd ProyectoFinalTeamManagerPro
    ```

2. **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3. **Instalar dependencias de JavaScript**
    ```bash
    npm install
    ```

4. **Configurar el entorno**
    Copia el archivo `.env.example` a `.env` y edítalo para configurar tu conexión a la base de datos.

5. **Configurar la base de datos**
    Edita el archivo `.env` para establecer los parámetros de conexión.

6. **Ejecutar migraciones y datos de prueba**
    ```bash
    php artisan migrate --seed
    ```
    > **Nota:** Si la base de datos SQLite no existe, Laravel te dará la opción de crearla automáticamente durante la migración.

7. **Crear enlace simbólico para almacenamiento**
    Este paso es crucial para que funcione correctamente el guardado de imágenes de alineaciones.
    ```bash
    php artisan storage:link
    ```

8. **Compilar assets**
    ```bash
    npm run build
    ```

9. **Iniciar el servidor de desarrollo**
    ```bash
    php artisan serve
    ```

10. **Acceder a la aplicación**
     Abre tu navegador y visita: [http://localhost:8000](http://localhost:8000)

---

## 📊 Características principales

- Gestión de equipos en diferentes formatos (F5, F7, F8, F11)
- Sistema de jugadores con estadísticas específicas por posición
- Alineador táctico interactivo con funcionalidad drag-and-drop
- Gestión de partidos amistosos y competiciones de liga
- Sistema detallado de estadísticas y rendimiento

---

## 📝 Nota para evaluadores

Este proyecto ha sido desarrollado como Trabajo Final para el ciclo formativo de Desarrollo de Aplicaciones Web (DAW). La aplicación está completamente funcional y desplegada en Azure App Service para facilitar su evaluación.

---

## 👨‍💻 Desarrollado por

**Marc Cañadas Suau**

- [GitHub](https://github.com/mcanadas-dawi)
- [LinkedIn](https://linkedin.com/in/marc-cañadas-suau-3911b7261/)

---

## 📄 Licencia

Este proyecto está licenciado bajo la [Licencia MIT](https://app.copyrighted.com/work/nF9nBQb5OCQyBWX4/?url=https%3A%2F%2Fwww.teammanagerpro.es%2F).