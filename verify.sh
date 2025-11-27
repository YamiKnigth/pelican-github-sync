#!/bin/bash

# Script de verificación para GithubSync Plugin
# Este script ayuda a verificar que todo esté correctamente instalado

echo "======================================"
echo "GithubSync Plugin - Verificación"
echo "======================================"
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para verificar
check() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1"
        return 1
    fi
}

# Verificar ubicación del plugin
echo "1. Verificando ubicación del plugin..."
if [ -f "/var/www/pelican/app/Plugins/githubsync/plugin.json" ]; then
    check "plugin.json existe"
else
    echo -e "${RED}✗${NC} plugin.json no encontrado en /var/www/pelican/app/Plugins/githubsync/"
    echo "   Por favor, copia el plugin en la ubicación correcta"
    exit 1
fi

# Verificar permisos
echo ""
echo "2. Verificando permisos..."
OWNER=$(stat -c '%U' /var/www/pelican/app/Plugins/githubsync)
if [ "$OWNER" = "www-data" ]; then
    check "Permisos correctos (owner: www-data)"
else
    echo -e "${YELLOW}⚠${NC} Owner es '$OWNER' en lugar de 'www-data'"
    echo "   Ejecuta: sudo chown -R www-data:www-data /var/www/pelican/app/Plugins/githubsync"
fi

# Verificar composer autoload
echo ""
echo "3. Verificando composer autoload..."
cd /var/www/pelican
composer dump-autoload -q 2>/dev/null
check "Composer autoload regenerado"

# Verificar migración
echo ""
echo "4. Verificando tabla en base de datos..."
TABLE_EXISTS=$(php artisan tinker --execute="echo Schema::hasTable('yamiknigth_github_settings') ? 'yes' : 'no';" 2>/dev/null)
if [[ "$TABLE_EXISTS" == *"yes"* ]]; then
    check "Tabla 'yamiknigth_github_settings' existe"
else
    echo -e "${RED}✗${NC} Tabla no existe"
    echo "   Ejecuta: php artisan migrate"
fi

# Verificar Livewire component
echo ""
echo "5. Verificando componente Livewire..."
LIVEWIRE_CHECK=$(php artisan livewire:list 2>/dev/null | grep -c "yamiknigth-github-sync-toolbar")
if [ "$LIVEWIRE_CHECK" -gt 0 ]; then
    check "Componente Livewire registrado"
else
    echo -e "${YELLOW}⚠${NC} Componente no listado (puede ser normal)"
fi

# Verificar archivos del plugin
echo ""
echo "6. Verificando estructura de archivos..."
FILES=(
    "src/GithubSyncPlugin.php"
    "src/Providers/GithubSyncProvider.php"
    "src/Filament/Widgets/GithubToolbarWidget.php"
    "src/Models/GithubSetting.php"
    "src/Services/GitCommandService.php"
    "resources/views/toolbar.blade.php"
)

ALL_FILES_OK=true
for file in "${FILES[@]}"; do
    if [ -f "/var/www/pelican/app/Plugins/githubsync/$file" ]; then
        echo -e "${GREEN}  ✓${NC} $file"
    else
        echo -e "${RED}  ✗${NC} $file ${RED}FALTA${NC}"
        ALL_FILES_OK=false
    fi
done

if $ALL_FILES_OK; then
    check "Todos los archivos presentes"
fi

# Limpiar cachés
echo ""
echo "7. Limpiando cachés..."
php artisan optimize:clear -q 2>/dev/null
check "Cachés limpiadas"

# Resumen final
echo ""
echo "======================================"
echo "Resumen"
echo "======================================"
echo ""
echo "Siguiente paso:"
echo "1. Ve a tu panel: /admin/plugins"
echo "2. Busca 'GithubSync' y actívalo si está deshabilitado"
echo "3. Ve a cualquier servidor → Files"
echo "4. Deberías ver el widget 'GitHub Sync' en la parte superior"
echo ""
echo "Si no aparece, revisa:"
echo "  - Logs: tail -f /var/www/pelican/storage/logs/laravel.log"
echo "  - Ejecuta: php artisan route:list | grep server"
echo ""

# Verificar si estamos en el directorio correcto
if [ ! -f "/var/www/pelican/artisan" ]; then
    echo -e "${YELLOW}⚠${NC} Este script debe ejecutarse en un servidor con Pelican instalado"
    echo "   Si estás en desarrollo, adapta las rutas según tu entorno"
fi

echo "======================================"
