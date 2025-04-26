<div align="center">
  <img src="public/assets/images/kaizen_logo_transparent.png" width="180" alt="Kaizen">
  <h1>Kaizen Cinema Platform</h1>
  <p><em>Modern cinema management system built with Laravel</em></p>

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-8892BF?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat-square&logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Development-blue?style=flat-square)]()

</div>

## 📋 Overview

> Kaizen is an elegant cinema management platform featuring movie catalogs, show scheduling, and booking capabilities with a modern responsive interface.

## ✨ Key Features

-   **🎬 Movie Management** - Catalog with detailed information
-   **🎭 Show Scheduling** - Optimized cinema programming
-   **🎟️ Booking System** - Quick and intuitive purchase process
-   **🌙 Dark Mode** - Eye-friendly interface option

## 🚀 Quick Start

```bash
# Clone repository
git clone https://github.com/mgrl39/kaizen.git && cd kaizen

# Setup environment
composer install
cp .env.example .env
php artisan key:generate

# Database migration
php artisan migrate --seed

# Serve application
php artisan serve
```

> **Note**  
> Make sure to configure your database settings in the `.env` file before running migrations.

## 💡 Usage Tips

> **Important**  
> This application uses API versioning with `/api/v1/` prefix for all endpoints.

<div align="center">
  <br>
  <p>Built with ❤️ by the Kaizen Team</p>
</div>
