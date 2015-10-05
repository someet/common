FROM daocloud.io/someetinc/backend-base:devops-e31c397

# use China proxy
RUN composer config -g repositories.packagist composer http://packagist.phpcomposer.com
# Copy the working dir to the image's web root
COPY . /var/www/html

RUN composer install --no-progress \
  # install bower gulp
  && npm install gulp \
  && npm install \
  && bower install --allow-root --config.interactive=false \
  && gulp dist \
  && mkdir -p runtime web/assets \
  && chown www-data:www-data runtime web/assets

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
