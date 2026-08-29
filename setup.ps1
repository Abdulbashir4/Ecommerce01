$ErrorActionPreference = 'Stop'
Write-Host '=== Optimum Biomedical Laravel Setup ===' -ForegroundColor Cyan
if (!(Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer পাওয়া যায়নি। Composer install করে আবার চালান।' }
if (!(Get-Command npm -ErrorAction SilentlyContinue)) { throw 'Node/NPM পাওয়া যায়নি। Node.js install করে আবার চালান।' }
if (!(Test-Path '.env')) { Copy-Item '.env.example' '.env' }
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm install
npm run build
Write-Host ''
Write-Host 'Setup complete.' -ForegroundColor Green
Write-Host 'Start: php artisan serve'
Write-Host 'URL:   http://127.0.0.1:8000'
Write-Host 'Admin phone: 01700000000'
Write-Host 'Admin password: password'
