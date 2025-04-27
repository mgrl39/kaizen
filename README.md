<div align="center" style="text-align: center; width: 100%">
<img src="public/assets/images/kaizen_logo_transparent.png" height="90px"/>
<h1>Kaizen Cinema<br/><sub>API backend for cinema management.</sub></h1>

▶️ <a href="#quick-start">Quickstart</a> | <a href="#overview">Overview</a> | <a href="#api-endpoints">API Endpoints</a> | <a href="#development">Development</a>

<a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php"/></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel"/></a>
<a href="https://postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-15-336791?style=flat-square&logo=postgresql"/></a>
<a href="https://podman.io"><img src="https://img.shields.io/badge/Podman-Container-892CA0?style=flat-square&logo=podman"/></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=flat-square"/></a>
<a href="#status"><img src="https://img.shields.io/badge/Status-Development-blue?style=flat-square"/></a>

</div>
<hr/>

**Kaizen Cinema is a RESTful API backend that powers cinema management functionality.**

This Laravel-based API provides the essential backend services for movie management, session scheduling, and seat booking. It's designed to be consumed by any frontend client (web, mobile, or desktop).

> ➡️ Get started quickly using our [Quick Start](#quick-start) guide ⭐️

<hr/>

## Overview

Kaizen Cinema API provides the essential endpoints for cinema management:

- Movie management
- Cinema and screening room configuration
- Session scheduling
- Booking management
- User authentication

The API uses JSON for all requests and responses, with standard HTTP status codes and error handling.

## Quick Start

```bash
# Clone repository
git clone https://github.com/mgrl39/kaizen.git && cd kaizen

# Start PostgreSQL container
cd srcs/conf/postgres && ./pg-podman.sh start

# Use the Makefile for easy setup
make install

# Start the API server
make back

# (Optional) Run database migrations
make db
```

> **Note**  
> The PostgreSQL database is automatically configured in a Podman container. Your `.env` file should already be set up to connect to it.

## API Endpoints

The API provides the following main endpoint groups:

- `/api/auth` - Authentication endpoints
- `/api/movies` - Movie management
- `/api/cinemas` - Cinema and room configuration
- `/api/screenings` - Session scheduling
- `/api/bookings` - Booking management

Full API documentation can be generated with:

```bash
cd srcs/back
php artisan route:list
```

## Development

### Requirements

- PHP 8.1+
- Composer
- Podman (for containerized PostgreSQL)

### Container Management

The PostgreSQL database runs in a Podman container for easier development:

```bash
# Start database container
cd srcs/conf/postgres && ./pg-podman.sh start

# View database logs
cd srcs/conf/postgres && ./pg-podman.sh logs

# Access PostgreSQL shell
cd srcs/conf/postgres && ./pg-podman.sh shell

# Reset database (caution: deletes all data)
cd srcs/conf/postgres && ./pg-podman.sh reset
```

### Useful Commands

Use our simplified Makefile for common development tasks:

```bash
# Install dependencies
make install

# Run the backend server (API) with external access
make back

# Reset the database
make fresh

# Clear all caches
make clear
```
