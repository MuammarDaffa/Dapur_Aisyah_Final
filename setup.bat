@echo off
cd /d e:\TA\Dapur_Aisyah
set PHP_EXE=e:\TA\Dapur_Aisyah\php83\php.exe

echo [1/3] Generating app key...
"%PHP_EXE%" artisan key:generate --force

echo [2/3] Installing Breeze scaffolding (Blade)...
"%PHP_EXE%" artisan breeze:install blade --no-interaction

echo [3/3] Installing npm dependencies...
npm install

echo [DONE]
