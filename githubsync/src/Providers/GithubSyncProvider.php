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
        
        // 4. Registrar render hook solo en páginas de servidor
        $this->app->booted(function () {
            if (class_exists(\Filament\Support\Facades\FilamentView::class)) {
                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    'panels::page.start',
                    function () {
                        // DEBUG: Siempre mostrar algo para confirmar que el hook funciona
                        $route = request()->route();
                        $hasServer = $route && $route->hasParameter('server');
                        $path = request()->path();
                        
                        // Mostrar debug básico siempre
                        $debug = '<div style="background: #fee2e2; border: 3px solid #dc2626; padding: 15px; margin: 10px; border-radius: 8px; font-family: monospace;">';
                        $debug .= '<strong>🚨 HOOK EJECUTADO - panels::page.start</strong><br>';
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
                    }
                );
            }
        });
    }
}
