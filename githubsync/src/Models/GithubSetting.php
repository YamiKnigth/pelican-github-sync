<?php

namespace YamiKnigth\GithubSync\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Server;

class GithubSetting extends Model
{
    // Apuntamos a la tabla que creamos en el paso 3
    protected $table = 'yamiknigth_github_settings';

    protected $fillable = [
        'server_id',
        'repo_url',
        'branch',
        'encrypted_token',
        'git_username',
        'git_email',
    ];

    protected $casts = [
        'encrypted_token' => 'encrypted', // Encriptación automática
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}