<?php

namespace GithubSync\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class GithubConfig extends Component
{
    public $token;
    public $username;
    public $password;

    public function mount()
    {
        $this->token = Config::get('githubsync.github.token');
        $this->username = Config::get('githubsync.github.username');
        $this->password = Config::get('githubsync.github.password');
    }

    public function save()
    {
        $this->validate([
            'token' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Save to environment file
        Artisan::call('config:cache');

        session()->flash('message', 'GitHub configuration saved successfully.');
    }

    public function render()
    {
        return view('githubsync::config');
    }
}