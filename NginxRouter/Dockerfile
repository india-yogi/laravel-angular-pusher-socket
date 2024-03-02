# Add user for laravel application
RUN groupadd -fg 1000 www
RUN id -u 1000 >/dev/null 2>&1 || useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www
