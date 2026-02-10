# Power Meter Symfony

API de gestion de compteurs, construite avec Symfony 8 et Doctrine ORM.

## Stack

- Symfony 8.0
- PHP 8.4
- Doctrine ORM
- PostgreSQL 18

## Endpoints

| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/meter` | Liste tous les compteurs (JSON) |
| POST | `/api/meters` | Crée un compteur (JSON body: `serialNumber`, `location`) |

## Installation

```bash
composer install
# configurer DATABASE_URL dans .env.local
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
symfony serve
```
