# ¿Dónde aparece el widget?

## Ubicación en la interfaz

El widget **GitHub Sync** debe aparecer en:

```
Panel de Pelican
└── Servidor (cualquier servidor)
    └── Files (Archivos)
        └── [AQUÍ APARECE EL WIDGET] ← En la parte superior
```

## Aspecto visual

El widget se ve así:

```
┌─────────────────────────────────────────────────────────────────┐
│ 🐙 GitHub Sync              ⚙️  │  Clone  Pull  Push           │
└─────────────────────────────────────────────────────────────────┘
```

O si no está configurado:

```
┌─────────────────────────────────────────────────────────────────┐
│ 🐙 GitHub Sync              ⚙️  │  Configura el token...        │
└─────────────────────────────────────────────────────────────────┘
```

## Rutas donde debe aparecer

El widget se muestra en URLs como:

- `/server/{server}/files`
- `/server/1/files`
- `/server/abc123/files`

Y **NO** aparece en:

- `/admin` (panel de administración)
- `/` (página principal)
- `/server/{server}/console` (consola)
- `/server/{server}/settings` (configuración)

## Cómo verificar si está funcionando

### 1. Verifica en la consola del navegador

Abre DevTools (F12) y ve a Console. No deberías ver errores como:

```
❌ Livewire component not found
❌ Class 'YamiKnigth\GithubSync\...' not found
```

### 2. Verifica en Network

Ve a la pestaña Network y recarga la página. Busca requests a:

```
✅ livewire/update (POST)
✅ @livewire('yamiknigth-github-sync-toolbar')
```

### 3. Verifica el HTML

Inspecciona el código HTML de la página. Deberías ver:

```html
<div wire:id="..." wire:initial-data="...">
    <div class="fi-section ...">
        <!-- Contenido del widget -->
    </div>
</div>
```

### 4. Verifica con Livewire DevTools

Si tienes la extensión "Laravel Livewire DevTools" instalada:

1. Abre DevTools
2. Ve a la pestaña "Livewire"
3. Deberías ver el componente `yamiknigth-github-sync-toolbar`

## Si no aparece

### Opción 1: Verificar que el plugin esté activo

```bash
cd /var/www/pelican
php artisan tinker
```

Dentro de tinker:
```php
// Ver si el provider está registrado
app()->getLoadedProviders()['YamiKnigth\GithubSync\Providers\GithubSyncProvider'] ?? 'NO REGISTRADO';

// Ver si la vista existe
view()->exists('YamiKnigth-GithubSync::toolbar'); // Debe retornar true

exit
```

### Opción 2: Forzar el render hook manualmente

Edita temporalmente `resources/views/layouts/app.blade.php` (o el layout que uses):

```blade
{{-- Busca esta línea o similar --}}
@yield('content')

{{-- Y agrega esto JUSTO DESPUÉS --}}
@if(request()->route() && request()->route()->hasParameter('server'))
    @livewire('yamiknigth-github-sync-toolbar')
@endif
```

### Opción 3: Verificar que Filament está usando el layout correcto

El widget usa componentes de Filament (`<x-filament::section>`). Si Filament no está cargado, el widget no se verá bien.

Verifica que estés en una página de Filament (no en una vista custom de Pelican).

### Opción 4: Debug visual

Crea un archivo de prueba en `resources/views/test-widget.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Test Widget</title>
    @livewireStyles
</head>
<body>
    <h1>Test del Widget</h1>
    
    @livewire('yamiknigth-github-sync-toolbar')
    
    @livewireScripts
</body>
</html>
```

Y agrégalo en `routes/web.php`:

```php
Route::get('/test-widget', function() {
    return view('test-widget');
})->middleware('auth');
```

Luego visita `/test-widget` en tu navegador.

## Paneles de Filament en Pelican

Pelican usa diferentes paneles de Filament:

- **Panel: admin** → Para administración general
- **Panel: server** → Para gestión de servidores (AQUÍ VA EL WIDGET)

El `plugin.json` especifica:
```json
"panels": ["server"]
```

Esto significa que el plugin SOLO se activa en el panel `server`.

## Hooks disponibles en Filament

Estos son los hooks donde puedes inyectar contenido:

```php
'panels::body.start'        // Al inicio del body
'panels::body.end'          // Al final del body
'panels::content.start'     // Al inicio del contenido principal
'panels::content.end'       // Al final del contenido principal
'panels::header'            // En el header
'panels::header.after'      // Después del header
'panels::footer'            // En el footer
```

El widget actualmente usa `panels::body.start`.

Si quieres probarlo en otra posición, edita `src/Providers/GithubSyncProvider.php` y cambia:

```php
\Filament\Support\Facades\FilamentView::registerRenderHook(
    'panels::content.start', // ← Cambia esto
    function () {
        // ...
    }
);
```

## Resultado esperado

Cuando todo funcione correctamente:

1. ✅ Entras a un servidor
2. ✅ Vas a Files
3. ✅ Ves el widget en la parte superior
4. ✅ Haces clic en ⚙️ y se abre el modal
5. ✅ Completas el formulario y guardas
6. ✅ Aparecen los botones Clone, Pull, Push
7. ✅ Al hacer clic, se ejecutan en la consola del servidor

## Ejemplo real de uso

1. Configuras:
   - Repo: `https://github.com/tuusuario/mi-servidor-mc.git`
   - Branch: `main`
   - Usuario: `tuusuario`
   - Email: `tu@email.com`
   - Token: `ghp_xxxxxxxxxxxxx`

2. Haces clic en **Clone**:
   - Se ejecuta en el servidor Wings
   - Clona todo el repositorio en el directorio del servidor
   - Ves los archivos en el file manager

3. Editas archivos en el file manager

4. Haces clic en **Push**:
   - Te pide un mensaje de commit
   - Sube los cambios a GitHub

5. Si alguien más hace cambios en GitHub:
   - Haces clic en **Pull**
   - Descarga los cambios

## Conclusión

El widget es una barra horizontal que aparece arriba de la lista de archivos en cualquier servidor. Si no lo ves, revisa:

1. Plugin habilitado en `/admin/plugins`
2. Cachés limpiadas
3. Estás en la página de Files de un servidor
4. No hay errores en los logs
