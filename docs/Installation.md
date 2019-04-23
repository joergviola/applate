# Installation

## Backend
1. Basic installation:
    ````
    $ git clone https://github.com/joergviola/applate.git your-name
    $ cd your-name/backend
    $ composer install
    $ php artisan key:generate
    $ php artisan passport:keys
    $ cp .env.example .env
    ````
1. Create database and link to it in ```.env```
1. Instal system in database and create admin user:
    ````
    $ php artisan migrate
    $ php artisan passport:client --personal
    $ php artisan init
    ````
