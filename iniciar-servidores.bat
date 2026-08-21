@echo off
echo Iniciando servidores...

cd backend
start "Backend" C:\MAMP\bin\php\php8.2.14\php.exe artisan serve --host=127.0.0.1 --port=8000

cd ..\frontend
start "Frontend" C:\MAMP\bin\php\php8.2.14\php.exe artisan serve --host=127.0.0.1 --port=8001
start "Vite" npm run dev

echo Servidores iniciados! Revisa las ventanas negras que se abrieron.
pause
