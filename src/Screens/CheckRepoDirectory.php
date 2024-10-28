<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Screens\Contracts\Screen;
use Mattcoleman\PrServe\Service\Command;
use Mattcoleman\PrServe\Service\DirectoryValidator;
use function ArtisanBuild\CommunityPrompts\fileselector;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class CheckRepoDirectory extends Screen
{
    public function render(array $params = []): void
    {
        info('Checking for repository...');

        $isValidRepoDirectory = DirectoryValidator::isGitRepository($this->router->repoDirectory);
        if (empty($this->router->repoDirectory) || !$isValidRepoDirectory) {
            $default = Command::exec('echo "$HOME"');
            $default = $default[0] ?? null;

            $isValidRepoDirectory = false;
            while (!$isValidRepoDirectory) {
                $folder = fileselector(
                    label: 'Enter the path to the repository: ',
                    default: $default
                );
                $isValidRepoDirectory = DirectoryValidator::isGitRepository($folder);
                if (!$isValidRepoDirectory) {
                    error('Invalid repository directory');
                }
            }
            $this->router->repoDirectory = $folder;
        }

        info('Using repository: ' . $this->router->repoDirectory);
    }
}