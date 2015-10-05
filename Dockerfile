FROM codemix/yii2-base:2.0.6-php-fpm

# Copy the working dir to the image's web root
COPY . /var/www/html
ADD http://npmjs.org/install.sh /npmjs.install.sh

RUN apt-get update \
  # install nodejs ruby and some dev packages
  && apt-get install -y --no-install-recommends libmcrypt-dev libpng12-dev libxslt-dev libtidy-dev bzip2 libbz2-dev libssl-dev curl nodejs ruby ruby-dev \
  && ls /usr/bin/node || ln -s /usr/bin/nodejs /usr/bin/node \
  # install composer
  && composer install --no-progress \
  # install bower gulp
  && cat /npmjs.install.sh | sh \
  && npm install -g bower gulp-cli \
  && gem install compass \
  && npm install \
  && bower install \
  && gulp dist \
  # clean
  && rm -rf /npmjs.install.sh \
  && rm -rf /var/lib/apt/lists/* \

# The following directories are .dockerignored to not pollute the docker images
# with local logs and published assets from development. So we need to create
# empty dirs and set right permissions inside the container.
RUN mkdir runtime web/assets \
    && chown www-data:www-data runtime web/assets

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
