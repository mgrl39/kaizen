# Kaizen

<p align="center">
  <img src="public/assets/images/logo.png" width="200" alt="Kaizen">
</p>

> Plataforma web moderna para la gestión integral de cines, películas y reservas. Sistema optimizado, rápido y con experiencia de usuario mejorada.

<p align="center">
  <img src="https://img.shields.io/badge/version-1.1.0-blue" alt="Versión">
  <img src="https://img.shields.io/badge/licencia-MIT-green" alt="Licencia">
  <img src="https://img.shields.io/badge/estado-en%20desarrollo-orange" alt="Estado">
  <img src="https://img.shields.io/badge/PHP-8.1+-8892BF" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20" alt="Laravel">
</p>

## ✨ Características

- 🏢 **Gestión de cines y salas** - Control integral de múltiples cines
- 🎞️ **Catálogo de películas** - Información detallada, trailers y valoraciones
- 🎭 **Programación inteligente** - Proyecciones optimizadas según demanda
- 🎟️ **Reservas simplificadas** - Proceso de compra rápido e intuitivo
- 👥 **Usuarios y permisos** - Sistema de roles avanzado
- 📊 **Panel de administración** - Estadísticas en tiempo real
- 📱 **Diseño responsive** - Experiencia perfecta en cualquier dispositivo
- 🌙 **Modo oscuro** - Interfaz adaptable a preferencias del usuario

## 🔧 Stack Tecnológico

<p align="center">
  <img src="public/assets/images/laravel.png" width="75" height="75" alt="Laravel">&nbsp;&nbsp;&nbsp;
  <img src="public/assets/images/mysql.png" width="75" height="75" alt="MySQL">&nbsp;&nbsp;&nbsp;
  <img src="public/assets/images/tailwind.png" width="75" height="75" alt="Tailwind CSS">&nbsp;&nbsp;&nbsp;
  <img src="public/assets/images/alpinejs.png" width="75" height="75" alt="Alpine.js">
</p>

<p align="center">
  <b>Backend:</b> PHP 8.1+ | Laravel 10.x | <b>Base de datos:</b> MySQL | <b>Frontend:</b> Tailwind CSS | Alpine.js
</p>

## 🚀 Instalación

```bash
# Clonar el repositorio
git clone https://github.com/mgrl39/kaizen.git
cd kaizen

# Instalar dependencias
composer install
npm install

# Configurar el entorno
cp .env.example .env
php artisan key:generate

# Configurar la base de datos en .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kaizen
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run dev

# Iniciar servidor
php artisan serve
```

## 📝 Documentación

La documentación completa está disponible en `/docs` o visitando `/api-docs` en la aplicación en ejecución.

## 🌐 Demo

Visita la [demo en línea](https://kaizen-demo.ejemplo.com) para probar todas las funcionalidades.

## 👥 Contribución

Las contribuciones son bienvenidas. Por favor, lee [CONTRIBUTING.md](CONTRIBUTING.md) para más información.

## 📄 Licencia

Este proyecto está licenciado bajo [MIT License](LICENSE).

---

<p align="center">
  Desarrollado con ❤️ por <a href="https://github.com/mgrl39">mgrl39</a>
</p>