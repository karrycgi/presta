# PrestaShop Dev-Environment

## Basic commands

```docker compose up -d``` or ```npm run up``` to start environment.

```docker compose logs -f``` or ```npm run logs``` to float console output of docker environment.

```docker compose down``` or ```npm run down``` to stop environment.

```npm run clean``` resent environment (maybe sudo is required - depending on Docker configuration)

## URLs

Frontend: [http://localhost:8080](http://localhost:8080)

Backend: [http://localhost:8080/admin_xxx](http://localhost:8080/admin_xxx)

## Credentials and configuration

| Field     | Value           |
| :-------- | --------------: |
| Username  | admin@admin.com |
| Password  | admin123        |