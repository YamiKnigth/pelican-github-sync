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
        
        // 4. Registrar render hook solo en content.start (el que aparece en la posición correcta)
        $this->app->booted(function () {
            if (class_exists(\Filament\Support\Facades\FilamentView::class)) {
                \Filament\Support\Facades\FilamentView::registerRenderHook(
                    'panels::content.start',
                    function () {
                        $path = request()->path();
                        
                        // Solo procesar si NO es una petición de Livewire
                        if (str_contains($path, 'livewire/')) {
                            return '';
                        }
                        
                        // Verificar si estamos en una ruta de servidor (contiene /server/)
                        $isServerRoute = str_contains($path, 'server/');
                        
                        if ($isServerRoute) {
                            try {
                                return \Illuminate\Support\Facades\Blade::render(
                                    '@livewire(\'yamiknigth-github-sync-toolbar\')'
                                );
                            } catch (\Exception $e) {
                                return '<div style="background: #fee2e2; padding: 10px; margin: 10px; border-radius: 6px;">Error cargando GitHub Sync: ' . $e->getMessage() . '</div>';
                            }
                        }
                        
                        return '';
                    }
                );
            }
        });
    }
}
