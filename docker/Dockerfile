FROM php:8.0-apache

ARG UNAME=abacus
ARG UID=1000
ARG GID=1000
RUN groupadd -g $GID -o $UNAME
RUN useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME
ENV APACHE_RUN_USER=$UNAME
ENV APACHE_RUN_GROUP=$UNAME

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev mariadb-client less \
	&& docker-php-ext-install mysqli pdo_mysql zip

RUN curl https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar --output /usr/bin/wp \
	&& chmod +X /usr/bin/wp && chmod 777 /usr/bin/wp