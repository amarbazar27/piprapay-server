#!/bin/bash
# Run database initialization
php /var/www/html/init-db.php

# Replace 80 with the value of $PORT in ports.conf and any vhost files
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/*.conf

# Execute the main Apache process
exec apache2-foreground
