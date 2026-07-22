#!/bin/bash
# ============================================================
# Publica o código para public_html e aplica as migrations.
#
# É a "fonte de verdade" do deploy — usado por:
#   - .cpanel.yml         (botão "Deploy" do cPanel)
#   - bin/auto-pull.sh    (deploy automático por cron)
#
# Preserva sempre:
#   - config/config.php   (credenciais — nunca é copiado por cima)
#   - uploads/            (imagens já carregadas pelo gestor)
# ============================================================
set -euo pipefail

# Pasta pública do site. Ajuste se o site não fica na raiz do domínio
# (ex.: DEPLOYPATH="$HOME/public_html/loja").
DEPLOYPATH="${DEPLOYPATH:-$HOME/public_html}"

# Raiz do repositório (a pasta acima de bin/)
SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "[deploy] Origem : $SRC"
echo "[deploy] Destino: $DEPLOYPATH"

mkdir -p "$DEPLOYPATH/uploads"

rsync -a \
    --exclude='.git/' \
    --exclude='.github/' \
    --exclude='config/config.php' \
    "$SRC/" "$DEPLOYPATH/"

chmod -R 755 "$DEPLOYPATH/uploads"

echo "[deploy] A aplicar migrations..."
if cd "$DEPLOYPATH" && php database/migrate.php; then
    echo "[deploy] Concluído com sucesso."
else
    echo "[deploy] AVISO: migrations não aplicadas (verifique config/config.php e corra 'php database/migrate.php' por SSH)."
fi
