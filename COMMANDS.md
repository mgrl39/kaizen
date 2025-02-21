sudo apt-get install mysql-server

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';

CREATE USER 'root'@'%'
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';

// create user
CREATE USER 'root'@'%' IDENTIFIED BY 'root';

// grant all privileges to root user
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';

// flush privileges
FLUSH PRIVILEGES;
