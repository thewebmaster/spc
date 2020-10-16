#################################################
#  Southern Phone Development Build
#################################################
FROM docker.turno.co.nz:2053/turno-ubuntu:latest AS dev

RUN chown www-data:www-data /var/www \
    && rm -rf /etc/apache2/sites-enabled/000-default.conf

COPY docker/apache/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["/usr/sbin/apachectl","-DFOREGROUND"]

#################################################
#  Southern Phone Website Production Build
#################################################
FROM dev as prod

COPY ./ /var/www

RUN rm -Rf /var/www/docker \
    && rm /var/www/.gitlab-ci.yml \
    && rm -Rf /var/www/.git \
    && rm /var/www/.gitignore \
    && rm /var/www/Dockerfile \
    && rm /var/www/composer.* \
    && mkdir -p /var/www/application/logs \
    && chmod 777 -R /var/www/application/logs
    
