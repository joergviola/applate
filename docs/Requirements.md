
# Requirements

Requirement are give by [Laravel](https://laravel.com/docs/6.x#server-requirements).

Below you find a log of installing on a fresh 18.04 Ubuntu server:

````
sudo apt-get update
sudo apt-get install apache2 php libapache2-mod-php php-mysql php-curl php-common php-mbstring php-xml php-zip curl git
sudo apt install mysql-server
sudo mysql_secure_installation
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo apt install python-certbot-apache
cd /var/www
git clone <Your project repo>
<install an apache vhost to <project>/backend/public>
<ensure mod_rewrite is enabled and AllowOverride All is set>
sudo certbot --apache
cd <project>
````

From here on, start your [Installation](Installation.md).