## Setup
1. composer install
2. Copy .env.example to .env and update the db setting where necessary
3. php artisan migrate
4. php artisan db:seed
5. php artisan serve

## Useful command
- make format, run this for auto formatting

## cors open to all as of now, shall going to deployment, should make it an env value to control the allowed origin