FROM gitpod/workspace-mysql
                    
USER gitpod

# Install custom tools, runtime, etc. using apt-get
# For example, the command below would install "bastet" - a command line tetris clone:
#
# RUN sudo apt-get -q update && #     sudo apt-get install -yq bastet && #     sudo rm -rf /var/lib/apt/lists/*
#
# More information: https://www.gitpod.io/docs/config-docker/

FROM gitpod/workspace-mysql

USER root

RUN sudo usermod -a -G sudo gitpod

# Install custom tools, runtime, etc. using apt-get
# For example, the command below would install "bastet" - a command line tetris clone:
#
# RUN apt-get update \
#    && apt-get install -y bastet \
#    && apt-get clean && rm -rf /var/cache/apt/* && rm -rf /var/lib/apt/lists/* && rm -rf /tmp/*
#
# More information: https://www.gitpod.io/docs/42_config_docker/











# PHP HERE:


# Install Redis.
RUN sudo apt-get update \
 && sudo apt-get install -y \
  redis-server \
 && sudo rm -rf /var/lib/apt/lists/*

# Install php-fpm
RUN apt-get update \
 && apt-get -y install php-fpm php-cli php-bz2 php-bcmath php-gmp php-imap php-shmop php-soap php-xmlrpc php-xsl php-ldap \
 && apt-get -y install php-amqp php-apcu php-imagick php-memcached php-mongodb php-oauth php-redis\
 && apt-get clean && rm -rf /var/cache/apt/* /var/lib/apt/lists/* /tmp/*

RUN mkdir /php-fpm
RUN chown -R gitpod:gitpod /php-fpm

COPY php-fpm/7.2/fpm/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
COPY php-fpm/7.2/fpm/php.ini /etc/php/7.2/fpm/php.ini
COPY php-fpm/7.2/fpm/pool.d/www.conf /etc/php/7.2/fpm/pool.d/www.conf

RUN mkdir /run/php && touch /var/log/php7.2-fpm.log
RUN chown -R gitpod:gitpod /run/php && chown gitpod:gitpod /var/log/php7.2-fpm.log

#Nginx

# optional: use a custom Nginx config.
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# optional: change document root folder. It's relative to your git working copy.
ENV NGINX_DOCROOT_IN_REPO="www"