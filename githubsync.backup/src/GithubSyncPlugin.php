<?php

namespace YamiKnigth\GithubSync;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use YamiKnigth\GithubSync\Filament\Widgets\GithubToolbarWidget;

class GithubSyncPlugin implements Plugin
{
    public function getId(): string
    {
        return 'githubsync';
    }

    public function register(Panel $panel): void
    {
        // Registrar el widget en el panel
        $panel->widgets([
            GithubToolbarWidget::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Registrar render hook cuando el panel está listo
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn (): string => Blade::render('@livewire(\'yamiknigth-github-sync-toolbar\')'),
            scopes: ['server']
        );
    }
}
