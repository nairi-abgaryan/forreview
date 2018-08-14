#!/bin/sh

# ----------------------------------------------------------------------------------------------------------------------
# Change working directory
# ----------------------------------------------------------------------------------------------------------------------

cd $(dirname $0)

# ----------------------------------------------------------------------------------------------------------------------
# Finalize
# ----------------------------------------------------------------------------------------------------------------------

# Fix permissions before cache clear
sudo chmod -R 0777 var
sudo chown -R expago:www-data var

# Composer install
composer install --no-interaction

# Ru migrations
./bin/console doctrine:migrations:migrate --no-interaction

# Create admin user
./bin/console app:create-admin

# Fix permissions before cache clear
sudo chmod -R 0777 var
sudo chown -R expago:www-data var
