# PrestaShop Dev-Environment

## Basic commands

```docker compose up -d``` or ```npm run up``` to start environment.

 ```docker compose exec prestashop rm -R /var/www/html/install``` or ```npm run rm:install``` to remove install folder (needed from version 9.0.1 of Prestashop)

```docker compose logs -f``` or ```npm run logs``` to float console output of docker environment.

```docker compose down``` or ```npm run down``` to stop environment.

```npm run clean``` delete environment (may need ```sudo``` - depending on Docker configuration)

```npm run test``` will run Cypress (E2E) tests. Make shure prestashop is available on [http://localhost:8080](http://localhost:8080)

## URLs

Frontend: [http://localhost:8080](http://localhost:8080)

Backend: [http://localhost:8080/admin_xxx](http://localhost:8080/admin_xxx)

## Credentials and configuration

| Field     | Value           |
| :-------- | --------------: |
| Username  | admin@admin.com |
| Password  | admin123        |

## Install with zip file

1. Login into Backend 
   1. Go to login page (your provider or admin has provided)
   ![Login](./documentation/assets/01-Login.png)
   2. Fill in your credentials (your provider or admin has provided) then press ```Log In``` Button
   ![Login with credentials](./documentation/assets/02-Login-with-credentials.png)
2. On left side menu go to ```Improve``` &rarr; ```Modules``` &rarr; ```Module Manager```
   1. Look for ```Improve``` on the left side
   ![Login with credentials](./documentation/assets/03-Dashboard.png)
   2. Click ```Modules```
   ![Login with credentials](./documentation/assets/04-Dashboard-Modules-opened.png)
   3. Click ```Module Manager```
   ![Login with credentials](./documentation/assets/05-Module-Manager.png)
3. On top-right click ```Upload a module``` and select the zip-file.
   1. On top-right click ```Upload a module```
   ![Login with credentials](./documentation/assets/06-Upload-a-module.png)
   2. Drop or select the zip-file
   ![Login with credentials](./documentation/assets/07-Installin-module.png)
   ![Login with credentials](./documentation/assets/08-Module-installed.png)
4. After installing process finished click ```Configure```.
![Login with credentials](./documentation/assets/09-Configure.png)
![Login with credentials](./documentation/assets/10-Configure-bottom.png)
5. Fill in your configuration you want to use. The press the ```Save``` Button on bottom-right
6. Confirm you configured IPG Checkout under ```Improve``` &rarr; ```Payment``` &rarr; ```Preferences``` the way you want to use it. (Easiest configuration &rarr; Checking ```IPG Transaction``` for ```France``` will make it avaiable for orders in ```France```)
   1. Click ```Payment``` on left side
   ![Login with credentials](./documentation/assets/11-Dashboard-Payment-open.png)
   2. Click ```Preferences``` and check configuration
   ![Login with credentials](./documentation/assets/12-Payment-preferences-currency-restrictions.png)
   ![Login with credentials](./documentation/assets/13-Payment-preferences-group-restrictions.png)
   ![Login with credentials](./documentation/assets/14-Payment-preferences-country-restrictions.png)
   ![Login with credentials](./documentation/assets/15-Payment-preferences-carrier-restrictions.png)
7. __Done__