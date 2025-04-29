<div align="center" style="text-align: center; width: 100%">
<img src="srcs/front/static/images/kaizen_logo_transparent.png" height="90px"/>
<h1>Kaizen Cinema<br/><sub>Sistema de gestión para cines</sub></h1>

▶️ <a href="#inicio-rapido">Inicio Rápido</a> | <a href="#vision-general">Visión General</a> | <a href="#funcionalidades">Funcionalidades</a> | <a href="#desarrollo">Desarrollo</a>

<a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php"/></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel"/></a>
<a href="https://svelte.dev"><img src="https://img.shields.io/badge/Svelte-4.x-FF3E00?style=flat-square&logo=svelte"/></a>
<a href="https://postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-15-336791?style=flat-square&logo=postgresql"/></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=flat-square"/></a>
<a href="#status"><img src="https://img.shields.io/badge/Estado-Desarrollo-blue?style=flat-square"/></a>

</div>
<hr/>

**Kaizen Cinema es un sistema para la gestión de cines, con aplicación web integrada.**

Esta aplicación basada en Laravel con frontend en Svelte proporciona herramientas para la gestión de películas, programación de sesiones y reserva de asientos.

<hr/>

## Visión General

Kaizen Cinema ofrece una solución para la gestión de cines:

- Gestión de películas
- Configuración de cines y salas de proyección
- Programación de sesiones
- Gestión de reservas
- Autenticación de usuarios

El sistema incluye una interfaz web para administradores y usuarios finales.

## Inicio Rápido

```bash
git clone https://github.com/mgrl39/kaizen.git && cd kaizen # Clonar repositorio
cd srcs/back && composer install # Instalar dependencias de backend
cd srcs/front && npm install # Instalar dependencias de frontend
cd srcs/back && php artisan serve # Iniciar el servidor API
cd srcs/front && npm run dev # Iniciar el servidor de desarrollo frontend
```

## Funcionalidades

El sistema proporciona las siguientes funcionalidades principales:

- **Gestión de películas**: Catálogo, información, clasificaciones
- **Cines y salas**: Configuración, capacidad, tipo de pantalla
- **Sesiones**: Programación, precios, disponibilidad
- **Reservas**: Venta de entradas, selección de asientos
- **Usuarios**: Registro, perfiles, historial

## Desarrollo

### Requisitos

- PHP 8.1+
- Composer
- Node.js y npm

### Comandos Útiles

```bash
# Backend (desde srcs/back)
php artisan serve # Ejecutar el servidor API
php artisan migrate # Ejecutar migraciones
php artisan db:seed # Ejecutar seeders

# Frontend (desde srcs/front)
npm run dev # Ejecutar servidor de desarrollo
npm run build # Construir para producción
```

## Colaboradores

<div align="center">

<a href="https://github.com/mgrl39/kaizen/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=mgrl39/kaizen" />
</a>

</div>
