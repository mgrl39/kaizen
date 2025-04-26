<div align="center" style="text-align: center; width: 100%">
<img src="public/assets/images/kaizen_logo_transparent.png" height="90px"/>
<h1>Kaizen Cinema<br/><sub>Modern cinema management platform with elegant design.</sub></h1>

<br/>

▶️ <a href="#quick-start">Quickstart</a> | <a href="#overview">Overview</a> | <a href="#key-features">Features</a> | <a href="#architecture">Architecture</a> | <a href="#development">Development</a>

<br/>

<a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php"/></a>
<a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel"/></a>
<a href="https://getbootstrap.com"><img src="https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat-square&logo=bootstrap"/></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=flat-square"/></a>
<a href="#status"><img src="https://img.shields.io/badge/Status-Development-blue?style=flat-square"/></a>

</div>
<hr/>
<br/>

**Kaizen Cinema is a modern cinema management platform with a focus on elegant design and user experience.**

Built for the modern cinema business, our platform offers everything from movie catalog management to seamless booking capabilities, all with a carefully crafted user interface. We use a modular architecture that clearly separates backend and frontend components for improved maintainability and scalability.

_Kaizen Cinema makes it simple for cinema operators to manage their business and for patrons to discover and book their next movie experience._

<br/>

> ➡️ Get started quickly using our [Quick Start](#quick-start) guide ⭐️

<br/>
<hr/>
<br/>

<div align="center" style="text-align: center">
<img src="public/assets/images/kaizen_logo_transparent.png" height="60px" alt="logo" align="top"/>
<br/><br/>
<small><a href="#overview">Overview</a> | <a href="#key-features">Features</a> | <a href="#architecture">Architecture</a></small>
<br/>
<sub>. . . . . . . . . . . . . . . . . . . . . . . . . . . .</sub>
<br/><br/>
</div>

## Overview

<img src="public/assets/images/screenshot_dashboard.png" width="330px" align="right" style="float: right"/>

Kaizen Cinema is a complete solution for cinema management that combines admin tools and customer-facing features in a single platform.

The platform uses a **modular architecture** with a clear separation between:

-   **Backend API** (`/srcs/back`) - Laravel-powered REST API
-   **Frontend** (`/srcs/front`) - Modern vanilla JS client connecting to the API
-   **Configuration** (`/srcs/conf`) - Environment and deployment settings

Our approach emphasizes:

-   **Clean, modern design** inspired by top SaaS products like Stripe
-   **Performance-focused** architecture for smooth user experiences
-   **Mobile-first** responsive interface for all screen sizes
-   **Elegant booking flow** that guides customers through the process

<br/>

## Key Features

-   **🎬 Movie Management** - Comprehensive movie catalog with detailed information
-   **🎭 Show Scheduling** - Intelligent showtime programming for optimal screening schedules
-   **🎟️ Booking System** - Frictionless purchase process with seat selection
-   **🌙 Dark Mode** - Eye-friendly interface option for all users
-   **📱 Responsive Design** - Works seamlessly on mobile, tablet, and desktop
-   **🔒 Secure Payments** - Integrated payment processing with fraud protection
-   **📊 Analytics Dashboard** - Actionable insights for cinema operators
-   **🔔 Notifications** - Automated alerts for customers about upcoming shows

<br/>

## Architecture

<img src="public/assets/images/architecture.png" width="500px" align="center" />

The Kaizen Cinema platform uses a modular architecture:

```
kaizen/
├── srcs/
│   ├── back/           # Laravel backend API
│   ├── front/          # Vanilla JS frontend client
│   └── conf/           # Configuration files
├── .gitignore          # Customized for our folder structure
└── README.md           # This file
```

**Technology Stack:**

-   **Backend:** Laravel 10 (PHP 8.1+)
-   **Frontend:** Vanilla JavaScript, Bootstrap 5 with custom styling
-   **Database:** MySQL/PostgreSQL
-   **API:** RESTful endpoints with comprehensive documentation

<br/>

## Quick Start

```bash
# Clone repository
git clone https://github.com/mgrl39/kaizen.git && cd kaizen

# Setup backend environment
cd srcs/back
composer install
cp .env.example .env
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=kaizen
# DB_USERNAME=root
# DB_PASSWORD=

# Run database migrations
php artisan migrate --seed

# Serve backend application
php artisan serve --port=8001

# In a new terminal, setup frontend
cd srcs/front
# Serve frontend (using any simple HTTP server)
php -S localhost:8000
```

> **Note**  
> For production deployment, use proper web servers like Nginx or Apache.

<br/>

## Development

<div align="center" style="text-align: center">
<img src="public/assets/images/stripes.png" width="100%" alt="stripes"/>
</div>

### Backend Development (Laravel)

```bash
cd srcs/back

# Run tests
php artisan test

# Generate API documentation
php artisan scribe:generate

# Access API documentation
# Visit: http://localhost:8001/docs
```

### Frontend Development

```bash
cd srcs/front

# The frontend uses vanilla JavaScript with Bootstrap
# Simply edit the files directly and refresh your browser
```

### Environment Configuration

Configuration files are stored in `srcs/conf/` and include:

-   Docker configuration
-   Environment variables templates
-   Server configuration files

<br/>

## Deployment

Kaizen Cinema can be deployed in various environments:

-   **Development:** Local setup with `php artisan serve`
-   **Staging:** Docker-based deployment
-   **Production:** Traditional VPS or containerized cloud deployment

Detailed deployment instructions for each environment are available in the `docs/deployment.md` file.

<br/><br/>

<div align="center" style="text-align: center">
<sub>. . . . . . . . . . . . . . . . . . . . . . . . . . . .</sub>
<br/><br/>
<b>Built with ❤️ by the Kaizen Team</b><br/>
<a href="https://github.com/mgrl39/kaizen">GitHub</a> | <a href="https://github.com/mgrl39/kaizen/issues">Report Bug</a> | <a href="https://github.com/mgrl39/kaizen/issues">Request Feature</a>
<br/><br/>
</div>
```
