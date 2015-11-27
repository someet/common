FROM daocloud.io/someetinc/backend-base:docker-base.0.0.1

# use China proxy
RUN composer config -g repositories.packagist composer http://packagist.phpcomposer.com
# Copy the working dir to the image's web root
COPY . /var/www/html
COPY php/redis.tgz /home/redis.tgz

RUN composer self-update \
  && composer install --no-progress \
  # 优化自动加载
  && composer dump-autoload --optimize \
  # install bower gulp
  && npm install -g cnpm --registry=https://registry.npm.taobao.org \
  && cnpm install gulp \
  && cnpm install \
  && bower install --allow-root --config.interactive=false \
  && gulp dist \
  && mkdir -p runtime web/assets \
  && chown www-data:www-data runtime web/assets \
  && pecl install /home/redis.tgz \
  && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

# 安装 NewRelic
RUN mkdir -p /etc/apt/sources.list.d \
    && echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' \
        >> /etc/apt/sources.list.d/newrelic.list \

    # 添加 NewRelic APT 下载时用来验证的 GPG 公钥
    && curl -s https://download.newrelic.com/548C16BF.gpg \
        | apt-key add - \
    # 安装 NewRelic PHP 代理
    && apt-get update \
    && apt-get install -y newrelic-php5 \
#    && newrelic-install install \
    # 用完包管理器后安排打扫卫生可以显著的减少镜像大小
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


ENV NR_INSTALL_SILENT true
ENV NR_PHP_INI "/usr/local/etc/php/conf.d/newrelic.ini"

RUN mv /usr/local/php /usr/local/php-a \
  && ln -s /usr/local/bin/php /usr/local/php \
  && newrelic-install  install \
  && unlink /usr/local/php \
  && mv /usr/local/php-a /usr/local/php

RUN sed -i 's/"REPLACE_WITH_REAL_KEY"/\${NEW_RELIC_LICENSE_KEY}/g' \
    /usr/local/etc/php/conf.d/newrelic.ini
RUN sed -i 's/"PHP Application"/\${NEW_RELIC_APP_NAME}/g' \
    /usr/local/etc/php/conf.d/newrelic.ini

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
