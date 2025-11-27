<?php

namespace YamiKnigth\GithubSync\Filament\Widgets;

use Livewire\Component;
use Filament\Notifications\Notification;
use YamiKnigth\GithubSync\Models\GithubSetting;
use YamiKnigth\GithubSync\Services\GitCommandService;

class GithubToolbarWidget extends Component
{
    public $server;
    
    // Propiedades del formulario de configuración
    public $showConfigModal = false;
    public $repo_url = '';
    public $branch = 'main';
    public $git_username = '';
    public $git_email = '';
    public $encrypted_token = '';
    
    // Modal de push
    public $showPushModal = false;
    public $commit_message = 'Update via Panel';

    public function render()
    {
        return view('YamiKnigth-GithubSync::toolbar');
    }

    public function mount()
    {
        // Intentar obtener el servidor de diferentes maneras
        $this->server = request()->route()?->parameter('server');
        
        // Si no funciona con 'server', intentar obtenerlo por el UUID de la URL
        if (!$this->server) {
            $path = request()->path();
            if (preg_match('/server\/([a-f0-9-]+)/', $path, $matches) && isset($matches[1])) {
                $uuid = $matches[1];
                try {
                    $this->server = \App\Models\Server::where('uuid_short', $uuid)->first();
                } catch (\Exception $e) {
                    $this->server = null;
                }
            }
        }
        
        // Cargar configuración existente
        if ($this->server) {
            $settings = GithubSetting::where('server_id', $this->server->id)->first();
            if ($settings) {
                $this->repo_url = $settings->repo_url;
                $this->branch = $settings->branch;
                $this->git_username = $settings->git_username;
                $this->git_email = $settings->git_email;
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
    
    public function openConfigModal()
    {
        $this->showConfigModal = true;
    }
    
    public function closeConfigModal()
    {
        $this->showConfigModal = false;
    }
    
    public function saveConfiguration()
    {
        $this->validate([
            'repo_url' => 'required|url',
            'branch' => 'required',
            'git_username' => 'required',
            'git_email' => 'required|email',
            'encrypted_token' => 'required',
        ]);
        
        GithubSetting::updateOrCreate(
            ['server_id' => $this->server->id],
            [
                'repo_url' => $this->repo_url,
                'branch' => $this->branch,
                'git_username' => $this->git_username,
                'git_email' => $this->git_email,
                'encrypted_token' => $this->encrypted_token,
            ]
        );
        
        Notification::make()->title('Configuración guardada')->success()->send();
        $this->showConfigModal = false;
    }
    
    public function gitClone()
    {
        $this->runGit('clone');
    }
    
    public function gitPull()
    {
        $this->runGit('pull');
    }
    
    public function openPushModal()
    {
        $this->showPushModal = true;
    }
    
    public function gitPush()
    {
        $this->runGit('push', $this->commit_message);
        $this->showPushModal = false;
    }

    protected function runGit(string $action, ?string $msg = null)
    {
        try {
            $settings = GithubSetting::where('server_id', $this->server->id)->firstOrFail();
            $service = app(GitCommandService::class);
            $service->execute($settings, $action, $msg);
            Notification::make()->title('Comando ejecutado')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
        }
    }
}