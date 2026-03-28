#!/bin/sh
# Plesk Git "Ek dağıtım eylemleri" kutusuna SADECE şunu yazın (tek satır):
#   sh scripts/plesk-deploy.sh
# PHP yolu farklıysa ortam değişkeni: PLESK_PHP_BIN=/opt/plesk/php/8.3/bin/php

set -e
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [ ! -f ./artisan ]; then
  echo "artisan not found in $ROOT" >&2
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
