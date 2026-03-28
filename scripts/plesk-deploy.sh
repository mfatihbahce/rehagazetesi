#!/bin/sh
# Asil calistirma: plesk-git-hook.sh (Plesk kutusuna o yazilir).
# Plesk "Eylemleri dağıt":
#   sh scripts/plesk-git-hook.sh
#
# Bu dosyayi dogrudan calistirmayin; Git chroot'ta PHP bulunmaz.
# Manuel tek satir (kanca kullanmiyorsaniz):
#   /bin/bash --login -c 'cd httpdocs && /opt/plesk/php/8.3/bin/php artisan migrate --force --no-interaction'

set -e

PATH="/opt/plesk/php/8.4/bin:/opt/plesk/php/8.3/bin:/opt/plesk/php/8.2/bin:/opt/plesk/php/8.1/bin:/usr/local/bin:/usr/bin:/bin${PATH:+:$PATH}"
export PATH

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

PHP=""
if [ -n "$PLESK_PHP_BIN" ] && [ -f "$PLESK_PHP_BIN" ] && "$PLESK_PHP_BIN" -v >/dev/null 2>&1; then
  PHP="$PLESK_PHP_BIN"
fi
if [ -z "$PHP" ]; then
  for cand in \
    /opt/plesk/php/8.4/bin/php \
    /opt/plesk/php/8.3/bin/php \
    /opt/plesk/php/8.2/bin/php \
    /opt/plesk/php/8.1/bin/php \
    /usr/bin/php \
    /usr/local/bin/php
  do
    if [ -f "$cand" ] && "$cand" -v >/dev/null 2>&1; then
      PHP="$cand"
      break
    fi
  done
fi
if [ -z "$PHP" ]; then
  PHP=$(command -v php 2>/dev/null) || true
  if [ -n "$PHP" ] && ! "$PHP" -v >/dev/null 2>&1; then
    PHP=""
  fi
fi

if [ -z "$PHP" ]; then
  echo "PHP not found in Git deploy environment. Use bash login line in script header, or set PLESK_PHP_BIN." >&2
  exit 1
fi

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
