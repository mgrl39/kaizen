# Comandos utiles

```bash
sudo apt-get install mysql-server
```

Cambiar el bind-address a 0.0.0.0 en el archivo de configuración de mysql.

```bash
sudo vim /etc/mysql/mysql.conf.d/mysqld.cnf
```

```bash
sudo systemctl restart mysql
```

Cambiar la contraseña del usuario root.

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
CREATE USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';
FLUSH PRIVILEGES;
```

```php
php artisan make:model Cinema -m
php artisan make:model Room -m
php artisan make:model AdminUser -m
php artisan make:model Manage -m
php artisan make:model User -m
php artisan make:model Booking -m
php artisan make:model Movie -m
php artisan make:model Functions -m
php artisan make:model Screening -m
php artisan make:model Seat -m
php artisan make:model BookingSeat -m
php artisan make:model Genre -m
php artisan make:model MovieGenre -m
php artisan make:model Actor -m
php artisan make:model MovieActor -m
```

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
```

## Registro de cambios - Configuración del servidor y API

### Configuración del servidor

- Instalación y configuración de MySQL Server
- Configuración del bind-address para permitir conexiones remotas
- Configuración de usuarios y permisos de MySQL

### Modelos creados

Se han generado los siguientes modelos con sus migraciones:

- Cinema (Cines)
- Room (Salas)
- AdminUser (Usuarios administradores)
- Manage (Gestión)
- User (Usuarios)
- Booking (Reservas)
- Movie (Películas)
- Functions (Funciones)
- Screening (Proyecciones)
- Seat (Asientos)
- BookingSeat (Asientos reservados)
- Genre (Géneros)
- MovieGenre (Géneros de películas)
- Actor (Actores)
- MovieActor (Actores en películas)

### API y controladores

- Creado ServerStatusController para monitorear estado del servidor
- Implementadas rutas API en api.php
- Configurado CORS para permitir peticiones entre dominios
- Añadido manejo de errores 404 y 500

### Variables de entorno

- Configuración de base de datos
- Desactivado caché en desarrollo
- Configuración de dominios para Sanctum

### Estructura de directorios

- Creados directorios necesarios para controladores API
- Configurado Telescope para debugging
- Añadidos archivos de migración

🔥 Si instalas **las dos** (Telescope y Clockwork),
    **no pasa nada malo** — **son compatibles** y pueden coexistir perfectamente en el mismo proyecto.
De hecho, muchos devs usan ambos
    al mismo tiempo porque se complementan muy bien:

### 🚀 Si instalas las dos

✅ **Telescope** te dará una visión detallada de lo que pasa en el backend (consultas SQL, jobs, eventos, errores).  
✅ **Clockwork** te mostrará el rendimiento directamente en el navegador (tiempo de respuesta, queries, caché).  

### 🧪 **Ejemplo práctico**

1. Haces una solicitud a una ruta `/posts`.  
2. **Telescope** te muestra:  
   - Qué middleware se activaron.  
   - Qué jobs o eventos se dispararon.  
   - Las consultas SQL exactas ejecutadas.  
   - Si hubo algún error en la solicitud.  

3. **Clockwork** te muestra en el navegador:  
   - Tiempo de respuesta total.  
   - Qué consultas SQL tardaron más.  
   - Si hay algún cuello de botella en el rendimiento.  

👉 Si **Clockwork** te dice que la solicitud tarda mucho,
vas a **Telescope** para descubrir
**qué consulta o evento está causando el problema**. 🔎

---

### 🤔 **¿Desventajas de tener ambas?**  

- Ambas herramientas **registran eventos y consultas**,
    lo que puede **afectar ligeramente el rendimiento** en modo local (nada grave).  
- Si alguna vez notas que el entorno local va más lento,
    puedes desactivar Telescope o Clockwork temporalmente desde la configuración
    (`config/telescope.php` o `config/clockwork.php`).  

---

### 💡 **Mi consejo:**  

👉 Instala **las dos** y prueba.
Si te abruma toda la información, empieza con **Clockwork** para ver el rendimiento y luego usa **Telescope** para depurar más a fondo. 😎
