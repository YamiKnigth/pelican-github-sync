<?php

namespace YamiKnigth\GithubSync\Services;

use YamiKnigth\GithubSync\Models\GithubSetting;
use App\Repositories\Daemon\DaemonRepository; 

class GitCommandService
{
    public function __construct(protected DaemonRepository $daemonRepository)
    {
    }

    public function execute(GithubSetting $settings, string $action, ?string $commitMessage = null): void
    {
        // 1. Construir URL con autenticación embebida
        // https://usuario:token@github.com/...
        // Nota: Laravel encripta automáticamente el token, accedemos al valor real
        $token = $settings->encrypted_token; // Laravel lo desencripta automáticamente
        $authUrl = str_replace(
            'https://',
            "https://{$settings->git_username}:{$token}@",
            $settings->repo_url
        );

        // 2. Configurar identidad temporal
        $identity = "git config user.name \"{$settings->git_username}\" && git config user.email \"{$settings->git_email}\"";

        // 3. Definir el comando según la acción
        $cmd = match ($action) {
            'clone' => "{$identity} && if [ ! -d .git ]; then git clone {$authUrl} .; else echo 'Repositorio ya existe'; fi",
            'pull'  => "{$identity} && git pull {$authUrl} {$settings->branch}",
            'push'  => "{$identity} && git add . && (git diff-index --quiet HEAD || git commit -m \"{$commitMessage}\") && git push {$authUrl} {$settings->branch}",
            default => 'echo "Accion no reconocida"',
        };

        // 4. Enviar comando al Daemon (Wings)
        // Usamos el repositorio del daemon para enviar el comando a la instancia del servidor
        $server = $settings->server;
        $this->daemonRepository->setServer($server)->send($cmd);
    }
}