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
