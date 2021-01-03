#FROM php:7.0.23-apache as base
FROM php:5.5.38-apache as base


LABEL maintainer="Günter Hipler"
LABEL description="php5 version to run old typo3 website"
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
#Variante mit container
#COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

#which libraries are really needed for vufind these are far too much
#see the already preinstalled extensions in notes/pre_installed_modules.txt
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions  bcmath \
                            mysql \
                            bz2 \
                            calendar \
                            ctype \
                            curl \
                            date \
                            dba \
                            dom \
                            exif \
                            fileinfo \
                            filter \
                            ftp \
                            gd \
                            gettext \
                            hash \
                            imap \
                            intl \
                            json \
                            libxml \
                            mbstring \
                            mcrypt \
                            mysqli \
                            mysqlnd \
                            openssl \
                            pcntl \
                            pcre \
                            pdo_mysql \
                            pdo_sqlite \
                            pgsql \
                            tokenizer \
                            xml \
                            xsl \
                            xmlreader \
                            xmlwriter \
                            zip \
                            zlib


RUN a2enmod rewrite



FROM base as withvim

RUN apt-get update && apt-get install -y vim

FROM withvim as suzanne

ARG PHP_APP_DIR

COPY charset.conf /etc/apache2/conf-available/


COPY archiv /var/www/html/
RUN chown -R www-data:www-data /var/www/html




