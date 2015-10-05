FROM codemix/yii2-base:2.0.6-php-fpm

# use China proxy
RUN composer config -g repositories.packagist composer http://packagist.phpcomposer.com
# Copy the working dir to the image's web root
COPY . /var/www/html
RUN composer install --no-progress

# The following directories are .dockerignored to not pollute the docker images
# with local logs and published assets from development. So we need to create
# empty dirs and set right permissions inside the container.
RUN mkdir runtime web/assets \
    && chown www-data:www-data runtime web/assets

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
