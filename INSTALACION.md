# Guía Detallada de Instalación

## Paso a paso para que el plugin aparezca en la interfaz

### 1. Verificar la ubicación correcta

El plugin debe estar en: `/var/www/pelican/app/Plugins/githubsync/`

```bash
# Verificar que existe
ls -la /var/www/pelican/app/Plugins/githubsync/plugin.json
```

### 2. Verificar permisos

```bash
# Dar permisos correctos
sudo chown -R www-data:www-data /var/www/pelican/app/Plugins/githubsync
sudo chmod -R 755 /var/www/pelican/app/Plugins/githubsync
```

### 3. Ejecutar migraciones

```bash
cd /var/www/pelican
php artisan migrate --force
```

Deberías ver:
```
Migrating: 2024_01_01_000000_create_github_settings_table
Migrated:  2024_01_01_000000_create_github_settings_table
```

### 4. Verificar que la tabla existe

```bash
php artisan tinker
```

Dentro de tinker:
```php
Schema::hasTable('yamiknigth_github_settings');
// Debería retornar: true
exit
```

### 5. Limpiar todas las cachés

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

### 6. Verificar que el ServiceProvider se carga

Agrega esto temporalmente en `app/Providers/AppServiceProvider.php`:

```php
public function boot()
{
    \Log::info('Providers cargados:', config('app.providers'));
}
```

Luego verifica los logs:
```bash
tail -f storage/logs/laravel.log
```

### 7. Forzar el registro del plugin

Si el plugin no se carga automáticamente, regístralo manualmente:

Edita `config/app.php` y agrega al array `providers`:

```php
'providers' => [
    // ... otros providers
    YamiKnigth\GithubSync\Providers\GithubSyncProvider::class,
],
```

Luego:
```bash
php artisan config:cache
```

### 8. Verificar en el panel

1. Ve a `/admin/plugins` en tu panel
2. Busca **GithubSync**
3. Si aparece pero está deshabilitado, habilítalo
4. Si no aparece, verifica los logs

### 9. Verificar que Livewire funciona

```bash
php artisan livewire:list
```

Deberías ver:
```
yamiknigth-github-sync-toolbar .... YamiKnigth\GithubSync\Filament\Widgets\GithubToolbarWidget
```

### 10. Debug: Ver dónde se carga

Agrega debug temporal en `src/Providers/GithubSyncProvider.php`:

```php
public function boot(): void
{
    \Log::info('GithubSyncProvider BOOT ejecutado');
    
    // ... resto del código
}
```

Y en `src/GithubSyncPlugin.php`:

```php
public function register(Panel $panel): void
{
    \Log::info('GithubSyncPlugin REGISTER ejecutado');
    
    // ... resto del código
}

public function boot(Panel $panel): void
{
    \Log::info('GithubSyncPlugin BOOT ejecutado');
    
    // ... resto del código
}
```

Luego revisa los logs:
```bash
tail -f storage/logs/laravel.log
```

## Solución de problemas comunes

### El plugin no aparece en Admin → Plugins

**Causa**: El archivo `plugin.json` no se está leyendo correctamente.

**Solución**:
1. Verifica que `plugin.json` esté en la raíz del directorio del plugin
2. Verifica que el JSON sea válido: `cat plugin.json | jq`
3. Limpia la caché: `php artisan optimize:clear`

### El widget no aparece en la página de servidor

**Causa**: El render hook no se está ejecutando o el scope es incorrecto.

**Solución 1 - Usar un hook diferente**:

Edita `src/GithubSyncPlugin.php` y prueba estos hooks:

```php
// Opción 1: Al inicio del contenido
FilamentView::registerRenderHook(
    'panels::content.start',
    fn (): string => Blade::render('@livewire(\'yamiknigth-github-sync-toolbar\')')
);

// Opción 2: En el header
FilamentView::registerRenderHook(
    'panels::header',
    fn (): string => Blade::render('@livewire(\'yamiknigth-github-sync-toolbar\')')
);

// Opción 3: Después del header
FilamentView::registerRenderHook(
    'panels::header.after',
    fn (): string => Blade::render('@livewire(\'yamiknigth-github-sync-toolbar\')')
);
```

**Solución 2 - Registrar directamente desde el Provider**:

En `src/Providers/GithubSyncProvider.php`:

```php
public function boot(): void
{
    // ... código existente ...
    
    // Registrar el hook directamente
    if (class_exists(\Filament\Support\Facades\FilamentView::class)) {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.start',
            function () {
                // Solo mostrar en rutas de servidor
                if (request()->route() && request()->route()->hasParameter('server')) {
                    return view('YamiKnigth-GithubSync::toolbar');
                }
                return '';
            }
        );
    }
}
```

### Error: Class 'YamiKnigth\GithubSync\Providers\GithubSyncProvider' not found

**Causa**: Composer no encuentra las clases.

**Solución**:
```bash
composer dump-autoload
php artisan optimize:clear
```

### El widget se muestra pero los botones no funcionan

**Causa**: Livewire no está procesando las acciones.

**Solución**:
1. Verifica que `@livewireScripts` esté en el layout
2. Limpia la caché de vistas: `php artisan view:clear`
3. Verifica en la consola del navegador si hay errores JavaScript

## Verificación final

Si todo está bien, deberías ver:

1. ✅ En `/admin/plugins`: El plugin **GithubSync** habilitado
2. ✅ En cualquier servidor → Files: Widget "GitHub Sync" en la parte superior
3. ✅ Al hacer clic en ⚙️: Se abre un modal de configuración
4. ✅ Después de configurar: Aparecen botones Clone, Pull, Push

## Contacto

Si sigues teniendo problemas, abre un issue en GitHub con:
- Logs de Laravel (`storage/logs/laravel.log`)
- Output de `php artisan route:list | grep server`
- Output de `php artisan about`
