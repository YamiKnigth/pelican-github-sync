<?php

namespace GithubSync\Http\Livewire;

use Livewire\Component;
use GithubSync\Services\GitCommandService;

class GitOperations extends Component
{
    public $repositoryUrl;
    public $directory;

    protected $rules = [
        'repositoryUrl' => 'required|string',
        'directory' => 'required|string',
    ];

    public function cloneRepository(GitCommandService $gitService)
    {
        $this->validate();
        try {
            $output = $gitService->cloneRepository($this->repositoryUrl, $this->directory);
            session()->flash('message', "Repository cloned successfully: $output");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function pullRepository(GitCommandService $gitService)
    {
        try {
            $output = $gitService->pullRepository($this->directory);
            session()->flash('message', "Repository pulled successfully: $output");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function pushRepository(GitCommandService $gitService)
    {
        try {
            $output = $gitService->pushRepository($this->directory);
            session()->flash('message', "Repository pushed successfully: $output");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('githubsync::operations');
    }
}