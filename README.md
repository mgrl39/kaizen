<div align="center">
  <img src="srcs/front/static/images/kaizen_logo_transparent.png" width="150px" align="right"/>
  <h1>Kaizen Cinema</h1>
  <h3>Sistema de gestión integral para cines</h3>
  <p>Una plataforma moderna para la administración completa de cines, gestión de películas y experiencia de usuario mejorada</p>

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Svelte](https://img.shields.io/badge/Svelte-4.x-FF3E00?style=flat-square&logo=svelte)](https://svelte.dev)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791?style=flat-square&logo=postgresql)](https://postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![Estado](https://img.shields.io/badge/Estado-Desarrollo-blue?style=flat-square)](https://github.com/mgrl39/kaizen)

</div>

## 📋 Índice

- [🚀 Inicio Rápido](#-inicio-rápido) • [✨ Características](#-características) • [🏗️ Arquitectura](#-arquitectura)
- [🧰 Tecnologías](#-tecnologías) • [📊 Módulos](#-módulos) • [🔧 Desarrollo](#-desarrollo)

## 🚀 Inicio Rápido

```bash
# Clonar el repositorio
git clone https://github.com/mgrl39/kaizen.git
cd kaizen/srcs/conf && chmod u+x *.sh
sudo bash i.sh
bash node.sh
```

## ✨ Características

**🎬 Gestión de Películas**

- Catálogo completo con información detallada
- Clasificación por géneros y metadata avanzada
- Integración con APIs externas de información cinematográfica

**🏢 Administración de Cines**

- Gestión de múltiples complejos y configuración de salas
- Tipos de pantallas (IMAX, 3D, etc.)
- Mapas de asientos personalizables y estadísticas

**🎟️ Sistema de Reservas**

- Selección visual de asientos con procesamiento de pagos
- Generación de tickets digitales y notificaciones
- Historial de compras y sistema de fidelización

## 🏗️ Arquitectura

- **Backend API RESTful**: Desarrollado con Laravel 10, proporciona endpoints seguros y documentados
- **Frontend SPA**: Interfaz de usuario reactiva construida con Svelte 4
- **Base de datos**: PostgreSQL optimizado para consultas complejas y alta concurrencia
- **Autenticación**: Sistema JWT con roles y permisos granulares
- **CI/CD**: Integración y despliegue continuo con GitHub Actions

## 🧰 Tecnologías

|  Backend   |  Frontend   |     DevOps     | Herramientas |
| :--------: | :---------: | :------------: | :----------: |
|  PHP 8.1+  |  Svelte 4   | GitHub Actions |     Git      |
| Laravel 10 |  SvelteKit  |     CI/CD      |   VS Code    |
| PostgreSQL | Bootstrap 5 |    Podman      |   Swagger    |
|  JWT Auth  | TypeScript  |   Focalboard   |   PHPUnit    |

## 📊 Módulos

- **Panel de Administración**: Gestión completa del sistema para operadores
- **Catálogo de Películas**: Visualización y filtrado avanzado
- **Programación de Sesiones**: Calendario visual para proyecciones
- **Sistema de Reservas**: Proceso intuitivo de selección y compra
- **Gestión de Usuarios**: Perfiles, preferencias e historial
- **Reportes y Estadísticas**: Análisis de ventas y ocupación
- **API Pública**: Documentada para integraciones externas

## 🔧 Desarrollo

### Comandos Útiles

```bash
# Backend


php artisan serve                # Iniciar servidor de desarrollo
php artisan test                 # Ejecutar tests
php artisan migrate:fresh --seed # Reiniciar base de datos

# Frontend
npm run dev      # Servidor de desarrollo
npm run build    # Compilar para producción

# Base de datos
./srcs/conf/postgres/pg-podman.sh start  # Iniciar contenedor PostgreSQL
./srcs/conf/postgres/pg-podman.sh dump   # Generar dump de la base de datos
```

### Estructura del Proyecto

```
kaizen/
├── srcs/
│   ├── back/    # Backend Laravel (app, database, routes)
│   └── front/   # Frontend Svelte (src, static, tests)
└── .github/     # Workflows de GitHub Actions
```

<div align="center">
<a href="https://github.com/sponsors/mgrl39">
  <img src="https://img.shields.io/badge/Patrocinar-mgrl39-ea4aaa?style=for-the-badge&logo=github-sponsors" alt="Patrocinar en GitHub" />
</a>

<p>Desarrollado con ❤️ por mgrl39</p>
<p><small>Gadget - Sistema de webscraping y análisis de datos de cines</small></p>
</div>
