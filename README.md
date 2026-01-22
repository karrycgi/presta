# PrestaShop Dev-Environment

## Basic commands

```docker compose -f docker-compose.dev.yml -f docker-compose.yml up -d``` or ```npm run up``` to start environment.

```docker compose up -d``` or ```npm run up:test``` to start environment in testing mode. No automated integration of module an no storage of data in disk.

 ```docker compose exec prestashop rm -R /var/www/html/install``` or ```npm run rm:install``` to remove install folder (needed since version 9.0.1 of Prestashop after installation is finshed to access admin section)

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

## .env

If ```.env``` is present the values of ```PS_COUNTRY``` and ```PS_LANGUAGE``` can be overwirtten. This causes prestashop to pull a different country and language during installation.

An example if ```.env``` is provided as ```default.env```. ```.env``` is placed in ```.gitignore```.

| Field       | Default value |
| :---------- | ------------: |
| PS_COUNTRY  | fr            |
| PS_LANGUAGE | en            |

## Install with zip file

1. Login into Backend 
   1. Go to login page (your provider or admin has provided)
   ![Login](./documentation/assets/01-Login.png)
   2. Fill in your credentials (your provider or admin has provided) then press ```Log In``` Button
   ![Login with credentials](./documentation/assets/02-Login-with-credentials.png)
2. On left side menu go to ```Improve``` &rarr; ```Modules``` &rarr; ```Module Manager```
   1. Look for ```Improve``` on the left side
   ![Dashboard](./documentation/assets/03-Dashboard.png)
   2. Click ```Modules```
   ![Dashboard Modules opened](./documentation/assets/04-Dashboard-Modules-opened.png)
   3. Click ```Module Manager```
   ![Module Manager](./documentation/assets/05-Module-Manager.png)
3. On top-right click ```Upload a module``` and select the zip-file.
   1. On top-right click ```Upload a module```
   ![Upload a module](./documentation/assets/06-Upload-a-module.png)
   2. Drop or select the zip-file
   ![Install module](./documentation/assets/07-Installin-module.png)
   ![Module installed](./documentation/assets/08-Module-installed.png)
4. After installing process finished click ```Configure```.
![Configure IPG Checkout](./documentation/assets/09-Configure.png)
![Configure IPG Checkout (bottom)](./documentation/assets/10-Configure-bottom.png)
5. Fill in your configuration you want to use. The press the ```Save``` Button on bottom-right
6. Confirm you configured IPG Checkout under ```Improve``` &rarr; ```Payment``` &rarr; ```Preferences``` the way you want to use it. (Easiest configuration &rarr; Checking ```IPG Transaction``` for ```France``` will make it avaiable for orders in ```France```)
   1. Click ```Payment``` on left side
   ![Dashboard Payment opened(./documentation/assets/11-Dashboard-Payment-open.png)
   2. Click ```Preferences``` and check configuration
   ![Payment preferences currency restrictions](./documentation/assets/12-Payment-preferences-currency-restrictions.png)
   ![Payment preferences group restrictions](./documentation/assets/13-Payment-preferences-group-restrictions.png)
   ![Payment preferences country restrictions](./documentation/assets/14-Payment-preferences-country-restrictions.png)
   ![Payment preferences carrier restrictions](./documentation/assets/15-Payment-preferences-carrier-restrictions.png)
7. __Done__

## Overview

![Component overview](./documentation/assets/overview.drawio.svg)

## Flows

![Component overview](./documentation/assets/architecture-flow.drawio.svg)
![Sequence overview](./documentation/assets/sequence-flow.drawio.svg)

### 1 Customer selects a IPGCheckout payment option

Customer selecting an IPGCheckout payment methode for payment.

### 2 Frontend Controller: controllers/front/pay.php

Customer is forwarded to ```/modules/ipgcheckout/pay?option={option}``` where ```option``` is limited to ```applepay```, ```googlepay```, ```cards```, ```bizum``` and ```generic```. Any other ```option``` will be interperted as ```generic```.

[IPG Checkout service documentation](https://docs.fiserv.dev/public/reference/postcheckouts) is used to create payment link.

Customer is forwarded to payment link automatically.

TBD: Request example

### 3 IPG Checkout page provided by IPG Checkout in response

__Out of IPGCheckout scope__

Customer is doing his stuff on payment page.

### 4 IPG Checkout specific behavior

__Out of IPGCheckout scope__

IPG is doing some magic.

### 5 Frontend Controller: controllers/front/success.php

On Success IPG is redirecting to this controller. The controller checks with (Checkout Solution)[https://docs.fiserv.dev/public/reference/get-checkouts-id] if payment was completed. If webhook was called before by IPG it's checking anyway.

### 6 Prestashop order completed page

Customer is redirected to ```order completed``` page.

__END__

### 7 Frontend Controller: controllers/front/payError.php

On error IPG is redirecting to this controller. Problems are prompted to Prestashop logs and the customer gets a message than an error occured

__END__