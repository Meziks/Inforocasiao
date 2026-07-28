# Inforocasião — Site institucional + montra de produtos

Site da loja **Inforocasião** (computadores, telemóveis, componentes electrónicos e reparações), com:

- **Site público** apelativo: página inicial, montra de produtos com pesquisa e filtros por categoria, página de produto, serviços/reparações e contactos.
- **Área de gestão** (`/admin`) com login, onde o gerente adiciona, edita e apaga artigos (com até 4 fotografias, preço, stock, categoria e condição: novo / usado / recondicionado).
- **Base de dados** MySQL gerida por **migrations** (versionadas no Git).
- **Deploy automático** cPanel ↔ GitHub via `.cpanel.yml`.

Stack: **PHP + MySQL** (sem passos de build) — o ideal para alojamento cPanel.

---

## 📁 Estrutura do projeto

```
├── index.php              # Controlador frontal (todas as rotas)
├── .htaccess              # Rewrite + segurança (público)
├── .cpanel.yml            # Tarefas de deploy do cPanel
├── app/                   # Núcleo da aplicação (protegido)
│   ├── bootstrap.php       #   arranque, sessão, config
│   ├── Database.php        #   ligação PDO
│   ├── Auth.php            #   login do gestor
│   ├── helpers.php         #   funções (escape, CSRF, render…)
│   └── views/              #   páginas e layouts
├── assets/                # CSS, JS, imagens
├── config/
│   ├── config.example.php  #   modelo (versionado)
│   └── config.php          #   credenciais reais (NÃO versionado)
├── database/
│   ├── migrate.php         #   executor de migrations
│   └── migrations/*.sql    #   alterações à base de dados
├── bin/create-admin.php   # Criar/atualizar utilizador de gestão
└── uploads/               # Imagens dos produtos (não versionadas)
```

---

## 🚀 Como ligar o cPanel ao GitHub (uma só vez)

> Precisa de: acesso ao **cPanel**, **SSH** ativo e uma **base de dados MySQL**.

### Passo 1 — Criar a base de dados no cPanel
1. cPanel → **MySQL® Databases**.
2. Crie uma base de dados (ex.: `inforocasiao`) → fica com um nome tipo `utilizador_inforocasiao`.
3. Crie um **utilizador MySQL** e uma password forte.
4. **Adicione o utilizador à base de dados** com **ALL PRIVILEGES**.
5. Anote: nome da BD, utilizador e password.

### Passo 2 — Clonar o repositório no cPanel
1. cPanel → **Git™ Version Control** → **Create**.
2. **Clone URL:** o URL do repositório GitHub
   (HTTPS com token, ou SSH com a chave do servidor autorizada no GitHub).
3. **Repository Path:** ex. `/home/UTILIZADOR/repositories/inforocasiao`
4. **Create.** O cPanel clona o repositório (ainda **não** é o site em si).

### Passo 3 — Definir a raiz do site
O código tem de ser servido a partir de `public_html`. Há duas opções:

- **Opção A (recomendada, usa o `.cpanel.yml`):** deixar o clone em
  `~/repositories/inforocasiao` e o deploy copia para `public_html`
  automaticamente (ver Passo 5).
- **Opção B:** apontar o *Document Root* do domínio diretamente para a
  pasta do clone (cPanel → **Domains** → alterar Document Root).

### Passo 4 — Criar o `config.php` no servidor (uma vez)
Por **SSH**:
```bash
cd ~/repositories/inforocasiao      # ou a pasta do clone
cp config/config.example.php config/config.php
nano config/config.php              # preencher host/nome/utilizador/password da BD
```
> Se usar a Opção A, este ficheiro tem de existir também em
> `~/public_html/config/config.php` (crie-o lá da mesma forma). Como é
> ignorado pelo Git, **nunca** é substituído pelos deploys.

### Passo 5 — Primeiro deploy
No cPanel → **Git Version Control** → no repositório → separador **Pull or Deploy**:
1. **Update from Remote** (traz o código do GitHub).
2. **Deploy HEAD Commit** (corre o `.cpanel.yml`: copia para `public_html` e aplica migrations).

Ou por SSH, na pasta do clone:
```bash
git pull
# o deploy corre sozinho ao carregar em "Deploy"; para correr migrations à mão:
cd ~/public_html && php database/migrate.php
```

### Passo 6 — Criar o utilizador de gestão
Por **SSH**, na pasta onde está o `config.php` já preenchido (ex. `~/public_html`):
```bash
php database/migrate.php                        # cria as tabelas (se ainda não correu)
php bin/create-admin.php gerente "PasswordForte123!"
```
Entre em **`https://oseudominio.pt/admin`** com essas credenciais. ✅

---

## 🔄 Fluxo de trabalho no dia-a-dia (atualizar "em direto")

1. Fazem-se as alterações ao código (localmente ou aqui) e **push** para o GitHub.
2. No cPanel → Git Version Control → **Update from Remote** → **Deploy**.
   - O site é atualizado e as **migrations novas** são aplicadas automaticamente.

### ⚡ Deploy 100% automático (já configurado — só falta ativar o cron)

O cPanel, por si só, só faz *pull* quando carrega no botão. Para publicar
**sozinho a cada `git push`**, o projeto inclui o script `bin/auto-pull.sh`:
de X em X minutos verifica se há commits novos no GitHub e, se houver,
atualiza o clone e publica automaticamente (copia para `public_html` +
migrations). Se não houver nada novo, não faz nada.

**Ativar (uma vez):** cPanel → **Cron Jobs** → adicionar, por exemplo de 5 em 5 minutos:

```
*/5 * * * * /bin/bash $HOME/repositories/inforocasiao/bin/auto-pull.sh >> $HOME/deploy.log 2>&1
```

Antes de ativar, abra `bin/auto-pull.sh` e confirme as duas variáveis no topo:

- `REPO`   → a pasta do clone (a que indicou no *Repository Path* do cPanel)
- `BRANCH` → o ramo a publicar (já vem preenchido com o ramo de trabalho,
  para que cada atualização fique online automaticamente)

A partir daí, o fluxo passa a ser simplesmente: **`git push` → o site atualiza
sozinho** em poucos minutos. O registo de cada deploy fica em `~/deploy.log`.

> **Alternativa (webhook):** se preferir deploy instantâneo em vez de
> intervalos de minutos, dá para ligar um *webhook* do GitHub a um pequeno
> recetor no servidor que chama `bin/auto-pull.sh`. O cron é mais simples e
> fiável para a maioria dos casos; o webhook é para quando o "quase imediato"
> não chega. Posso montar essa variante se precisar.

---

## 🗄️ Base de dados por migrations

- Cada alteração ao esquema é um ficheiro `.sql` numerado em
  `database/migrations/` (ex.: `005_add_campo_x.sql`).
- O `database/migrate.php` aplica **apenas as que ainda não correram**
  (guarda o registo na tabela `schema_migrations`), por isso é seguro correr
  as vezes que quiser.
- **Regra de ouro:** nunca se apaga/reescreve uma migration já aplicada em
  produção — cria-se sempre uma **nova**. Assim os dados reais nunca são
  destruídos por um deploy.

Criar uma nova migration:
```bash
# database/migrations/005_descricao.sql
ALTER TABLE products ADD COLUMN garantia_meses INT NOT NULL DEFAULT 0;
```
No próximo deploy (ou `php database/migrate.php`) é aplicada.

---

## 🔒 Segurança (já incluída)
- Passwords com `password_hash()`; sessões seguras; proteção **CSRF** nos formulários.
- Consultas com **prepared statements** (PDO) — sem injeção de SQL.
- Saída sempre escapada (`htmlspecialchars`).
- Pastas `app/`, `config/`, `database/`, `bin/` bloqueadas via `.htaccess`.
- `config.php` (credenciais) e `uploads/` fora do Git.
- **Ative o SSL** no cPanel e descomente o bloco "Forçar HTTPS" no `.htaccess`.

---

## 🛠️ Desenvolvimento local (opcional)
```bash
cp config/config.example.php config/config.php   # apontar para uma BD MySQL local
php database/migrate.php
php bin/create-admin.php admin "admin12345"
php -S localhost:8000 index.php                  # abrir http://localhost:8000
```
Ponha `'env' => 'development'` no `config.php` para ver os erros em detalhe.
