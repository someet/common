FROM daocloud.io/someetinc/backend-base:devops-e31c397

# Copy the working dir to the image's web root
COPY . /var/www/html

RUN composer install --no-progress \
  # install bower gulp
  && npm install \
  && bower install --allow-root --config.interactive=false \
  && gulp dist

# The following directories are .dockerignored to not pollute the docker images
# with local logs and published assets from development. So we need to create
# empty dirs and set right permissions inside the container.
RUN mkdir -p runtime web/assets \
    && chown www-data:www-data runtime web/assets

# Expose everything under /var/www (vendor + html)
# This is only required for the nginx setup
VOLUME ["/var/www"]
