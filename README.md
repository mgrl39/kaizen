<div align="center" style="text-align: center; width: 100%">
<img src="public/assets/images/kaizen_logo_transparent.png" height="90px"/>
<h1>Kaizen Cinema<br/><sub>API backend para gestión de cines.</sub></h1>

▶️ <a href="#inicio-rapido">Inicio Rápido</a> | <a href="#vision-general">Visión General</a> | <a href="#endpoints-api">Endpoints API</a> | <a href="#desarrollo">Desarrollo</a>

<a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php"/></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel"/></a>
<a href="https://svelte.dev"><img src="https://img.shields.io/badge/Svelte-4.x-FF3E00?style=flat-square&logo=svelte"/></a>
<a href="https://postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-15-336791?style=flat-square&logo=postgresql"/></a>
<a href="https://podman.io"><img src="https://img.shields.io/badge/Podman-Container-892CA0?style=flat-square&logo=podman"/></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=flat-square"/></a>
<a href="#status"><img src="https://img.shields.io/badge/Estado-Desarrollo-blue?style=flat-square"/></a>

</div>
<hr/>

**Kaizen Cinema es una API RESTful backend que impulsa la funcionalidad de gestión de cines.**

Esta API basada en Laravel con frontend en Svelte proporciona los servicios esenciales para la gestión de películas, programación de sesiones y reserva de asientos. Está diseñada para ser consumida por cualquier cliente frontend (web, móvil o escritorio).

> ➡️ Comienza rápidamente usando nuestra [Guía de Inicio Rápido](#inicio-rapido) ⭐️

<hr/>

## Visión General

La API de Kaizen Cinema proporciona los endpoints esenciales para la gestión de cines:

- Gestión de películas
- Configuración de cines y salas de proyección
- Programación de sesiones
- Gestión de reservas
- Autenticación de usuarios

La API utiliza JSON para todas las peticiones y respuestas, con códigos de estado HTTP estándar y manejo de errores.

## Inicio Rápido

```bash
git clone https://github.com/mgrl39/kaizen.git && cd kaizen # Clonar repositorio
cd srcs/conf/postgres && ./pg-podman.sh start # Iniciar contenedor PostgreSQL
make install # Usar el Makefile para configuración fácil
make back # Iniciar el servidor API
make db # (Opcional) Ejecutar migraciones de base de datos
```

> **Nota**  
> La base de datos PostgreSQL está automáticamente configurada en un contenedor Podman. Tu archivo `.env` ya debería estar configurado para conectarse a ella.

## Endpoints API

La API proporciona los siguientes grupos principales de endpoints:

- `/api/auth` - Endpoints de autenticación
- `/api/movies` - Gestión de películas
- `/api/cinemas` - Configuración de cines y salas
- `/api/screenings` - Programación de sesiones
- `/api/bookings` - Gestión de reservas

La documentación completa de la API puede generarse con:

```bash
cd srcs/back && php artisan route:list # Generar documentación API
```

## Desarrollo

### Requisitos

- PHP 8.1+
- Composer
- Podman (para PostgreSQL en contenedor)

### Gestión de Contenedores

La base de datos PostgreSQL se ejecuta en un contenedor Podman para facilitar el desarrollo:

```bash
cd srcs/conf/postgres && ./pg-podman.sh start # Iniciar contenedor de base de datos
cd srcs/conf/postgres && ./pg-podman.sh logs # Ver logs de la base de datos
cd srcs/conf/postgres && ./pg-podman.sh shell # Acceder al shell de PostgreSQL
cd srcs/conf/postgres && ./pg-podman.sh reset # Resetear base de datos (precaución: elimina todos los datos)
```

### Comandos Útiles

Usa nuestro Makefile simplificado para tareas comunes de desarrollo:

```bash
make install # Instalar dependencias
make back # Ejecutar el servidor backend (API) con acceso externo
make fresh # Resetear la base de datos
make clear # Limpiar todas las cachés
```

## Colaboradores

<div align="center">

<a href="https://github.com/mgrl39/kaizen/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=mgrl39/kaizen" />
</a>

¡Kaizen Cinema es mejorado y mantenido por personas como tú! 🚀

</div>
