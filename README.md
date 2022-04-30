# TaxiDemo

Server side taxi mobile app project _build on [Staticy framework](https://github.com/hereismhtm/Staticy-Framework)_.

# Requirements

* Nginx [https://www.nginx.com]

```shell
$ sudo apt install nginx
```
* MySQL

```shell
$ sudo apt install mysql-server
```

* PHP v7.4

Make sure you have PHP and project's modules ready :

```shell
$ sudo apt install php7.4 php7.4-fpm
$ sudo apt install php7.4-mysql php7.4-curl php7.4-mbstring php7.4-xml
```

* Staticy framework [https://github.com/hereismhtm/Staticy-Framework]

This project operate under *Staticy* framework.

Download it and then *copy/link* the following :

1. Staticy-Framework/index.php *file* ===> TaxiDemo/index.php
2. Staticy-Framework/src/system *folder* ===> TaxiDemo/src/system

* phpMyAdmin (*optional*) [https://www.phpmyadmin.net]

# Configuration

* Web server configuration

Edit *`nginx_taxidemo.conf`* file and replace `{ProjectFolderPath}` value.

From the root of TaxiDemo project folder :

```shell
sudo cp nginx_taxidemo.conf /etc/nginx/sites-available/taxidemo.conf
sudo ln -s /etc/nginx/sites-available/taxidemo.conf /etc/nginx/sites-enabled
sudo systemctl reload nginx.service
```

* Database configuration

Make a copy of *`config.php.example`* file as *`config.php`*

```shell
cp config.php.example config.php
```

Open *`config.php`* file:

Replace value `{DatabaseUsernameValue}` of *`$_db_config['username']`* to your database user.

Replace value `{DatabasePasswordValue}` of *`$_db_config['password']`* to your database user password.

Save *`config.php`* file.

Create empty database called *`taxidemo`* 

```shell
sudo mysql -u root -p
CREATE DATABASE taxidemo;
exit
```

Create database tables:

```shell
sudo mysql -u root -p --one-database taxidemo < mysql_taxidemo.sql
```

# How to login
Go to http://taxidemo.localhost

- Username : admin
- Password : admin
