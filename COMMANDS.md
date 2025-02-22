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