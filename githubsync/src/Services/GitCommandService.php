<?php

namespace GithubSync\Services;

use Symfony\Component\Process\Process;

class GitCommandService
{
    public function runCommand(array $command, string $workingDirectory = null): string
    {
        $process = new Process($command, $workingDirectory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    public function cloneRepository(string $repositoryUrl, string $directory): string
    {
        return $this->runCommand(['git', 'clone', $repositoryUrl, $directory]);
    }

    public function pullRepository(string $directory): string
    {
        return $this->runCommand(['git', '-C', $directory, 'pull']);
    }

    public function pushRepository(string $directory): string
    {
        return $this->runCommand(['git', '-C', $directory, 'push']);
    }
}