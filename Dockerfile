FROM daocloud.io/someetinc/backend-base:docker-base.0.0.5

# Copy the working dir to the image's web root
COPY . /var/www/html
# Copy the opcache configfile
COPY opcache.ini /usr/local/etc/php/conf.d/

RUN composer self-update \
  && composer install --no-progress \
  # 优化自动加载
  #&& composer dump-autoload --optimize \
  # install bower
  #&& npm install -g cnpm --registry=https://registry.npm.taobao.org \
  && npm install gulp \
  && npm install \
  && bower install --allow-root --config.interactive=false \
  && gulp dist \
  && mkdir -p runtime web/assets \
  && chown www-data:www-data runtime web/assets

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
