# Installation

Create your repo on github, then:

````
$ git clone <your-repo>
$ cd <your-repo>
$ git remote add upstream https://github.com/joergviola/applate.git
$ git fetch upstream
$ git merge upstream/master
````

## Backend

1. Install the backend:
    ````
    $ cd backend
    $ composer install
    $ php artisan key:generate
    $ php artisan passport:keys
    $ cp .env.example .env
    ````
1. Create database and link to it in ```.env```
1. Install system in database and create admin user:
    ````
    $ php artisan migrate
    $ php artisan passport:client --personal
    $ php artisan init
    ````

## Upgrade from applate

In your repo:

````
git fetch upstream
git merge upstream/master
````
 