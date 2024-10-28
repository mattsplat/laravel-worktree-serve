<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Router;
use Mattcoleman\PrServe\Screens\Contracts\Screen;
use Mattcoleman\PrServe\Service\Command;
use function Laravel\Prompts\error;
use function Laravel\Prompts\table;

class WorktreeList extends Screen
{

    /**
     * @param Router $router
     */
    public function __construct()
    {
    }

    public function render(array $params = []): void
    {
        $then = function ($process) {
            $worktrees = explode("\n", $process->getOutput());
            $worktrees = array_filter($worktrees);
            $worktrees = array_map(
                function ($worktree) {
                    return explode(' ', $worktree);
                },
                $worktrees
            );

            $worktrees = array_map(
                function ($worktree) {
                    return [
                        'path' => $worktree[0],
                        'branch' => $worktree[1],
                        'commit' => $worktree[2]
                    ];
                },
                $worktrees
            );
            $this->displayWorktrees($worktrees);
            $this->router->back();
        };

        $command = new Command(
            argv: ['git', 'worktree', 'list'],
            then: $then,catch:
            function ($process) {
                error("No worktrees found:  \n". $process->getErrorOutput());
                $this->router->back();
            }
        );
        $command->setWorkingDirectory($this->router->repoDirectory);
        $command->run();
    }

    protected function displayWorktrees(array $worktrees)
    {
        table(['path', 'branch', 'commit'], $worktrees);
    }
}