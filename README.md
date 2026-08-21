# Eval27 - Gestion de tickets Symfony

Application interne permettant aux clients de créer un ticket et au personnel de suivre son traitement.

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MariaDB/MySQL
- Symfony CLI (recommandé)

## Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

La connexion à la base doit être définie localement dans `.env.local` avec la variable `DATABASE_URL`.

## Comptes de démonstration

- Administrateur : `admin@symfony` / `Admin123!`
- Personnel : `staff@eval27.test` / `Staff123!`

## Fonctionnalités

- création publique d'un ticket ;
- authentification et déconnexion ;
- consultation et modification du statut par le personnel ;
- gestion des catégories, états, responsables et tickets par l'administrateur ;
- validation serveur, migrations et fixtures Doctrine ;
- tests avec PHPUnit.

## Tests

```bash
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction
php bin/phpunit
```
