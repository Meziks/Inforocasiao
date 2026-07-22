#!/bin/bash
# ============================================================
# Deploy AUTOMÁTICO — corre por cron de X em X minutos.
#
# Verifica se há commits novos no GitHub; se houver, atualiza o clone
# e publica automaticamente (chama bin/cpanel-deploy.sh). Se não houver
# nada novo, não faz nada.
#
# Configurar no cPanel → Cron Jobs, por exemplo de 5 em 5 minutos:
#   */5 * * * * /bin/bash $HOME/repositories/inforocasiao/bin/auto-pull.sh >> $HOME/deploy.log 2>&1
#
# AJUSTE as duas variáveis abaixo à sua realidade.
# ============================================================
set -euo pipefail

# Pasta do clone criado pelo cPanel (Git Version Control → Repository Path)
REPO="${REPO:-$HOME/repositories/inforocasiao}"

# Ramo (branch) a publicar. Depois de fazer merge para o ramo principal,
# normalmente será "main".
BRANCH="${BRANCH:-main}"

cd "$REPO"

git fetch origin "$BRANCH" --quiet

LOCAL="$(git rev-parse HEAD)"
REMOTE="$(git rev-parse "origin/$BRANCH")"

if [ "$LOCAL" = "$REMOTE" ]; then
    # Nada novo — sair em silêncio.
    exit 0
fi

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Novo commit detetado ($REMOTE). A publicar..."
git reset --hard "origin/$BRANCH" --quiet

/bin/bash "$REPO/bin/cpanel-deploy.sh"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Deploy automático concluído."
