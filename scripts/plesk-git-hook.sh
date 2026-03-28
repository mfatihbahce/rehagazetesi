#!/bin/sh
# Plesk -> Git -> "Eylemleri dağıt" kutusuna SADECE bunu yazin:
#   sh scripts/plesk-git-hook.sh
#
# Dogrudan plesk-deploy.sh calismaz: Git ek eylemi chroot'ta /opt ve php yok.
# Bu dosya bash --login ile tam oturum PATH'inde plesk-deploy.sh calistirir.

set -e
HOOK_DIR=${0%/*}
case "$HOOK_DIR" in
  "$0") HOOK_DIR=. ;;
esac
cd "$HOOK_DIR/.." || exit 1
ROOT_ABS=$(pwd)
exec /bin/bash --login -c "cd \"$ROOT_ABS\" && sh scripts/plesk-deploy.sh"
