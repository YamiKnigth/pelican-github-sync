#!/bin/bash

echo "Limpiando todas las cachés..."

# Cambiar al directorio de Pelican (ajusta esta ruta según tu instalación)
cd /var/www/pelican 2>/dev/null || cd /workspaces/pelican-github-sync 2>/dev/null || { echo "No se encontró el directorio de Pelican"; exit 1; }

# Limpiar cachés de Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan event:clear

# Limpiar caché de opciones compiladas
php artisan optimize:clear

# Regenerar autoload de composer
composer dump-autoload

echo "✅ Cachés limpiadas"
echo ""
echo "Si estás usando OPcache, también debes reiniciar PHP-FPM:"
echo "  sudo systemctl restart php8.2-fpm"
echo "  (o la versión de PHP que uses)"
