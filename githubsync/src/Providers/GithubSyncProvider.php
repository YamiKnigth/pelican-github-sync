<?php

namespace YamiKnigth\GithubSync\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use YamiKnigth\GithubSync\Filament\Widgets\GithubToolbarWidget;

class GithubSyncProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Cargar vistas con el namespace 'YamiKnigth-GithubSync'
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'YamiKnigth-GithubSync');

        // 2. Cargar migraciones
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // 3. Registrar el componente Livewire
        Livewire::component('yamiknigth-github-sync-toolbar', GithubToolbarWidget::class);
        
        // 4. Registrar múltiples render hooks
        $this->app->booted(function () {
            if (class_exists(\Filament\Support\Facades\FilamentView::class)) {
                $renderWidget = function () {
                    // DEBUG: Siempre mostrar algo para confirmar que el hook funciona
                    $route = request()->route();
                    $hasServer = $route && $route->hasParameter('server');
                    $path = request()->path();
                    
                    // Solo procesar si NO es una petición de Livewire
                    if (str_contains($path, 'livewire/')) {
                        return '';
                    }
                    
                    // Mostrar debug básico
                    $debug = '<div style="background: #dcfce7; border: 3px solid #16a34a; padding: 15px; margin: 10px; border-radius: 8px; font-family: monospace;">';
                    $debug .= '<strong>✅ GitHub Sync Widget</strong><br>';
                    $debug .= 'Ruta: ' . $path . '<br>';
                    $debug .= 'Tiene servidor: ' . ($hasServer ? 'SÍ' : 'NO') . '<br>';
                    $debug .= '</div>';
                    
                    // Intentar renderizar el widget si hay servidor
                    if ($hasServer) {
                        try {
                            $widget = \Illuminate\Support\Facades\Blade::render(
                                '@livewire(\'yamiknigth-github-sync-toolbar\')'
                            );
                            return $debug . $widget;
                        } catch (\Exception $e) {
                            return $debug . '<div style="background: #fef3c7; padding: 10px; margin: 10px;">Error: ' . $e->getMessage() . '</div>';
                        }
                    }
                    
                    return $debug;
                };
                
                // Probar múltiples hooks
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::body.start', $renderWidget);
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::content.start', $renderWidget);
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::page.start', $renderWidget);
            }
        });
    }
}
