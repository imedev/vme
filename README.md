# VME

### Install php dependencies
* composer install
### Install node dependencies
* npm install

### Settings
* copy .env.example to  .env
* Update APP_URL to your local url ( domain)
* Create your database (mysql) on your server and on your .env file update the lines to reflect your new database settings.

### Commands
*  php artisan key:generate
*  composer reset
*  php artisan migrate --seed
*  php artisan queue:work
*  npm run dev

