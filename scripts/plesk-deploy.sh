#!/bin/sh
# Plesk "Eylemleri dağıt" kutusu (dağıtım hedefi /httpdocs iken):
#   sh scripts/plesk-deploy.sh
# ASLA "httpdocs/scripts/..." kullanmayın; çalışma dizini zaten httpdocs olur.

set -e

# Plesk PATH'te dirname/cd olmayabiliyor; $0 ile yol kurmayız.
if [ -f ./artisan ]; then
  :
elif [ -f httpdocs/artisan ]; then
  cd httpdocs
elif [ -f /httpdocs/artisan ]; then
  cd /httpdocs
elif [ -n "$HOME" ] && [ -f "$HOME/httpdocs/artisan" ]; then
  cd "$HOME/httpdocs"
elif [ -f /var/www/vhosts/rehagazetesi.com/httpdocs/artisan ]; then
  cd /var/www/vhosts/rehagazetesi.com/httpdocs
else
  echo "artisan not found cwd=$(pwd)" >&2
  exit 1
fi

PHP="${PLESK_PHP_BIN:-/opt/plesk/php/8.4/bin/php}"
COMPOSER_PHAR="/usr/local/psa/var/modules/composer/composer.phar"
if [ ! -f "$COMPOSER_PHAR" ]; then
  COMPOSER_PHAR="/opt/psa/var/modules/composer/composer.phar"
fi

if [ -f "$COMPOSER_PHAR" ]; then
  COMPOSER_ALLOW_SUPERUSER=1 "$PHP" -d memory_limit=512M "$COMPOSER_PHAR" install --no-dev --optimize-autoloader --no-interaction
fi

"$PHP" artisan migrate --force --no-interaction
"$PHP" artisan optimize:clear --no-interaction
"$PHP" artisan config:cache --no-interaction
"$PHP" artisan route:cache --no-interaction
"$PHP" artisan view:cache --no-interaction
