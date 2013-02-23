#!/bin/sh

apt-get update
apt-get upgrade
# apt-get install tcsh git emacs24-nox iostat htop
apt-get install apache2 mysql-server memcache
apt-get install php5 php5-cli php5-curl php5-mcrypt php5-memcache php5-mysql

ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/
ln -s /etc/apache2/mods-available/proxy.load /etc/apache2/mods-enabled/
ln -s /etc/apache2/mods-available/proxy.conf /etc/apache2/mods-enabled/
ln -s /etc/apache2/mods-available/proxy_http.load /etc/apache2/mods-enabled/

# chgrp -R www-data /FIX/ME/youarehere/www/templates_c
# chmod -R g+ws /FIX/ME/youarehere/www/templates_c

/etc/init.d/apache2 restart
