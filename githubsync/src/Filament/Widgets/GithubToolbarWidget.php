<?php

namespace YamiKnigth\GithubSync\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use YamiKnigth\GithubSync\Models\GithubSetting;
use YamiKnigth\GithubSync\Services\GitCommandService;

class GithubToolbarWidget extends Widget implements HasActions
{
    use InteractsWithActions;

    // Referencia al namespace de la vista (definido en el Provider)
    protected string $view = 'YamiKnigth-GithubSync::toolbar';
    
    // Hacer el widget visible siempre
    protected static bool $isLazy = false;
    
    // Sin restricciones
    public static function canView(): bool
    {
        return true;
    }

    public $server;

    public function mount()
    {
        // Intentar obtener el servidor de diferentes maneras
        $this->server = request()->route()?->parameter('server');
        
        // Si no funciona con 'server', intentar obtenerlo por el UUID de la URL
        if (!$this->server) {
            $path = request()->path();
            if (preg_match('/server\/([a-f0-9-]+)/', $path, $matches)) {
                $uuid = $matches[1];
                $this->server = \App\Models\Server::where('uuid_short', $uuid)->first();
            }
        }
    }

    public function hasSettings(): bool
    {
        if (!$this->server || !isset($this->server->id)) {
            return false;
        }
        
        return GithubSetting::where('server_id', $this->server->id)->exists();
    }

    public function configureAction(): Action
    {
        return Action::make('configure')
            ->label('')
            ->tooltip('Configurar Git')
            ->icon('heroicon-o-cog-6-tooth')
            ->color('gray')
            ->fillForm(fn () => GithubSetting::where('server_id', $this->server->id)->first()?->toArray() ?? [])
            ->form([
                TextInput::make('repo_url')->required()->url()->label('URL Repositorio')->placeholder('https://github.com/usuario/repo.git'),
                TextInput::make('branch')->required()->default('main'),
                TextInput::make('git_username')->required()->label('Usuario'),
                TextInput::make('git_email')->required()->email()->label('Email'),
                TextInput::make('encrypted_token')->password()->required()->label('Token (PAT)'),
            ])
            ->action(function (array $data) {
                GithubSetting::updateOrCreate(['server_id' => $this->server->id], $data);
                Notification::make()->title('Configuración guardada')->success()->send();
            });
    }

    public function cloneAction(): Action
    {
        return Action::make('clone')
            ->label('Clone')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (GitCommandService $s) => $this->runGit($s, 'clone'));
    }

    public function pullAction(): Action
    {
        return Action::make('pull')
            ->label('Pull')
            ->color('primary')
            ->action(fn (GitCommandService $s) => $this->runGit($s, 'pull'));
    }

    public function pushAction(): Action
    {
        return Action::make('push')
            ->label('Push')
            ->color('success')
            ->form([TextInput::make('message')->required()->default('Update via Panel')])
            ->action(fn (array $data, GitCommandService $s) => $this->runGit($s, 'push', $data['message']));
    }

    protected function runGit(GitCommandService $service, string $action, ?string $msg = null)
    {
        try {
            $settings = GithubSetting::where('server_id', $this->server->id)->firstOrFail();
            $service->execute($settings, $action, $msg);
            Notification::make()->title('Comando enviado a consola')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
        }
    }
}