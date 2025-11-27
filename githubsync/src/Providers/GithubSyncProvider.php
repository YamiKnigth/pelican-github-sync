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
        
        // 4. Registrar múltiples render hooks para asegurar que se vea
        $this->app->booted(function () {
            if (class_exists(\Filament\Support\Facades\FilamentView::class)) {
                $widgetHtml = function () {
                    // Solo mostrar en rutas de servidor
                    if (request()->route() && request()->route()->hasParameter('server')) {
                        return \Illuminate\Support\Facades\Blade::render(
                            '@livewire(\'yamiknigth-github-sync-toolbar\')'
                        );
                    }
                    return '';
                };
                
                // Probar diferentes hooks
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::body.start', $widgetHtml);
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::content.start', $widgetHtml);
                \Filament\Support\Facades\FilamentView::registerRenderHook('panels::page.start', $widgetHtml);
            }
        });
    }
}
