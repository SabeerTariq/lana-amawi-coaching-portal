@echo off
echo Setting up Laravel Web Application...
echo.

echo Installing PHP dependencies...
composer install

echo.
echo Installing Node.js dependencies...
npm install

echo.
echo Building assets...
npm run build

echo.
echo Setting up environment...
if not exist .env (
    copy .env.example .env
    echo Environment file created.
)

echo.
echo Generating application key...
php artisan key:generate

echo.
echo Running database migrations...
php artisan migrate

echo.
echo Seeding database...
php artisan db:seed

echo.
echo Setup complete!
echo.
echo To start the development server, run:
echo php artisan serve
echo.
echo To start with hot reloading, also run in another terminal:
echo npm run dev
echo.
pause 