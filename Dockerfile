FROM daocloud.io/someetinc/backend-base:latest

# Composer packages are installed first. This will only add packages
# that are not already in the yii2-base image.
# Copy the working dir to the image's web root
COPY . /var/www/html
RUN composer global require fxp/composer-asset-plugin:~1.1.1 --no-plugins  && \
    composer self-update --no-progress && \
    composer install --no-progress

RUN mkdir -p runtime web/assets \
    && chown www-data:www-data runtime web/assets

# install bower
RUN npm install
RUN bower install --allow-root --config.interactive=false
RUN gulp dist


# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
