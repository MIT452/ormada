# Installation en local avec XAMPP (sans Docker)

Ce projet est une application **Symfony 7.4** (PHP 8.2+). Docker a été retiré ;
voici comment le lancer avec XAMPP (Apache + MySQL) + Composer + Node.js.

## 0. Pré-requis à installer sur ta machine

- **XAMPP** (Apache + MySQL/MariaDB + PHP 8.2 minimum)
- **Composer** (gestionnaire de paquets PHP) : https://getcomposer.org/download/
- **Node.js + npm** (pour compiler le CSS Tailwind) : https://nodejs.org/

## 1. Activer les extensions PHP nécessaires

Dans XAMPP, ouvre `php.ini` (via le panneau de contrôle XAMPP → Apache → Config →
PHP (php.ini), ou dans `xampp/php/php.ini`) et vérifie que ces lignes sont
décommentées (sans `;` devant) :

```
extension=pdo_mysql
extension=mysqli
extension=intl
extension=mbstring
extension=zip
extension=fileinfo
extension=opcache
```

Redémarre Apache après modification.

## 2. Placer le projet et pointer Apache dessus

Deux options :

**Option A (recommandée) — Virtual Host**
Édite `xampp/apache/conf/extra/httpd-vhosts.conf` et ajoute :

```apache
<VirtualHost *:80>
    ServerName ormada.local
    DocumentRoot "C:/xampp/htdocs/ormada/public"
    <Directory "C:/xampp/htdocs/ormada/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ajoute ensuite `127.0.0.1 ormada.local` dans le fichier `hosts` de ton système
(`C:\Windows\System32\drivers\etc\hosts` sous Windows), puis mets le projet dans
`C:/xampp/htdocs/ormada`.

**Option B (rapide) — serveur PHP intégré**
Pas besoin de configurer Apache : place le projet où tu veux, ouvre un terminal
dedans et lance :

```bash
php -S localhost:8000 -t public
```

Le site sera sur http://localhost:8000

## 3. Créer la base de données

Ouvre **phpMyAdmin** (http://localhost/phpmyadmin) et crée une base nommée
`ormada` (interclassement `utf8mb4_unicode_ci`).

Le fichier `.env` à la racine est déjà configuré pour XAMPP par défaut :

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/ormada?serverVersion=8.0.32&charset=utf8mb4"
```

Si ton MySQL XAMPP a un mot de passe root (rare par défaut), édite plutôt le
fichier `.env.local` (déjà présent, non versionné) avec ton mot de passe.

## 4. Installer les dépendances PHP et générer le schéma

Dans un terminal, à la racine du projet :

```bash
composer install
php bin/console doctrine:migrations:migrate
```

(Répondre "yes" si demandé.)

## 5. Installer les dépendances JS et compiler le CSS (Tailwind)

```bash
npm install
npm run tailwind
```

Cela génère `public/styles.css`. Pense à relancer `npm run tailwind` (ou
`npm run tailwind:watch` en développement) à chaque modification de style.

## 6. Créer un compte administrateur

Toutes les pages d'administration (`/actualite`, `/evenement`, `/media`,
`/contact`, `/user`, `/accueil`, `/parametre`) nécessitent un compte
`ROLE_ADMIN`. Il n'y a pas de compte créé par défaut, il faut en insérer un
manuellement :

```bash
php bin/console security:hash-password
```

Ce mot de passe une fois tapé, la commande affiche un hash (une longue chaîne).
Copie-le, puis dans phpMyAdmin, insère une ligne dans la table `user` :

```sql
INSERT INTO user (email, roles, password)
VALUES ('admin@ormada.mg', '["ROLE_ADMIN"]', 'COLLE_LE_HASH_ICI');
```

Tu pourras ensuite te connecter sur `/login` avec cet email et ce mot de passe.

## 7. Vider le cache si besoin

```bash
php bin/console cache:clear
```

## Résumé express (une fois tout installé)

```bash
composer install
npm install && npm run tailwind
php bin/console doctrine:migrations:migrate
php -S localhost:8000 -t public
```

## Ce qui a été retiré / ajouté par rapport au dépôt d'origine

- Supprimés : `DockerFile`, `compose.yaml`, `compose.override.yaml`,
  `docker-entrypoint.sh`, `.dockerignore`, dossier `docker/` (plus nécessaires).
- Ajoutés : `.env` (config MySQL locale par défaut), `.env.local` (pour tes
  overrides perso), `public/.htaccess` (indispensable pour qu'Apache route
  toutes les requêtes vers `index.php`).
- `node_modules/` a été retiré de l'archive (trop volumineux) — relance
  `npm install` pour le régénérer.
