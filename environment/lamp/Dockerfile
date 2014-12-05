#FROM ubuntu:14.04
#FROM ushios/ubuntu14-apache-php
FROM ushios/lamp-ubuntu
MAINTAINER HannahLin 

# Keep upstart from complaining
#RUN dpkg-divert --local --rename --add /sbin/initctl
#RUN ln -sf /bin/true /sbin/initctl

# Update
RUN apt-get update
RUN apt-get -y upgrade

# Basic Requirements
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install libapache2-mod-php5 php5-mysql php-apc python-setuptools curl git unzip vim-tiny

# Wordpress Requirements
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install php5-curl php5-gd php5-intl php-pear php5-imagick php5-imap php5-mcrypt php5-memcache php5-ming php5-ps php5-pspell php5-recode php5-sqlite php5-tidy php5-xmlrpc php5-xsl php5-mysqlnd

# Install phpunit
RUN apt-get install -y  phpunit 


# php config
RUN sed -i -e "s/upload_max_filesize\s*=\s*2M/upload_max_filesize = 100M/g" /etc/php5/apache2/php.ini
RUN sed -i -e "s/post_max_size\s*=\s*8M/post_max_size = 100M/g" /etc/php5/apache2/php.ini
RUN sed -i -e "s/short_open_tag\s*=\s*Off/short_open_tag = On/g" /etc/php5/apache2/php.ini

# fix for php5-mcrypt
RUN /usr/sbin/php5enmod mcrypt

# Install todolist_wine
RUN git config --global user.name "Hannah Lin"
RUN git config --global user.email "light940929@gmail.com"
RUN git clone https://github.com/light940929/todolist_wine.git 
RUN cp -R todolist_wine /var/www/html/

# Set https CA
RUN mkdir /etc/apache2/ssl
RUN openssl req -x509 -nodes -subj "/C=TW/ST=Denial/L=Springfield/O=Dis/CN=www.wis.com" -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/apache.key -out /etc/apache2/ssl/apache.crt
RUN rm -rf /etc/apache2/sites-available/default-ssl.conf 
RUN rm -rf /etc/apache2/apache2.conf

# Initialization Startup Script
#ADD ./installLAMP.sh /installLAMP.sh
#RUN chmod 755 /installLAMP.sh
ADD ./default-ssl.conf /etc/apache2/sites-available/default-ssl.conf  
ADD ./apache2.conf  /etc/apache2/apache2.conf

RUN a2ensite default-ssl.conf
RUN a2enmod rewrite
RUN service apache2 restart

# Install todolist_wine DB
RUN service mysql restart && mysql -uroot -e "CREATE DATABASE todolist_wine" && mysql -uroot todolist_wine < /var/www/html/todolist_wine/db/todolist_wine1127.sql

EXPOSE 443




