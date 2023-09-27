# TaxiDemo

Server side taxi mobile app project _build on [Staticy framework](https://github.com/hereismhtm/Staticy-Framework)_.

## - Docker containers (easy option)

* Run

```shell
sh bin/up.sh
```

* Run (development mode)

For this mode you need to copy *`docker/php/config.php`* file into `src/application/config` folder

and change `DEV_ENV` value from `false` to be `true`

```shell
sh bin/dev-mode.sh -d --build
```

* Stop

```shell
docker compose down
```

## - Manually system setup (hard option)

### Requirements

* Nginx [https://nginx.com]

```shell
sudo apt install nginx
```
* MySQL

```shell
sudo apt install mysql-server
```

* PHP v7.4

Make sure you have PHP and project's modules ready :

```shell
sudo apt install php7.4 php7.4-fpm
sudo apt install php7.4-mysql php7.4-curl php7.4-mbstring php7.4-xml
```

### Configuration

* Web server configuration

From the root of TaxiDemo project folder :

```shell
sudo cp docker/nginx/nginx_taxidemo.conf /etc/nginx/sites-available/taxidemo.conf
```

Edit *`taxidemo.conf`* file and replace `{ProjectFolderPath}` value, then:


```shell
sudo ln -s /etc/nginx/sites-available/taxidemo.conf /etc/nginx/sites-enabled
sudo systemctl reload nginx.service
```

* Database configuration

Make a copy of *`config.example.php`* file as *`config.php`*

```shell
cp config.example.php config.php
```

Open *`config.php`* file:

Replace value `{DatabaseUsernameValue}` of *`$_db_config['username']`* to your database user.

Replace value `{DatabasePasswordValue}` of *`$_db_config['password']`* to your database user password.

Replace value `{RandomSecretValue}` of *`$_auth_config['network_token']`* to a random secret string.

Save *`config.php`* file.

Create empty database called `taxidemo`

```shell
sudo mysql -u root -p

CREATE DATABASE taxidemo;
exit
```

Create database tables:

```shell
sudo mysql -u root -p --one-database taxidemo < docker/mysql/mysql_taxidemo.sql
```

## - How to login

Visit http://taxidemo.localhost:80

- Username : admin
- Password : admin
