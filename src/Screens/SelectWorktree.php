<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Screens\Contracts\Screen;
use Mattcoleman\PrServe\Service\Command;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

class SelectWorktree extends Screen
{

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

            $this->selectWorkTree(array_map(fn ($w) => $w['path'], $worktrees));
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

    protected function selectWorkTree(array $worktrees): void
    {
        $worktree = select("Select a worktree", [...$worktrees, 'Back']);
        if ($worktree === 'Back') {
            $this->router->back();
            return;
        }
        info("Selected worktree: " . $worktree);
        $this->router->push(new WorktreeInstanceMenu(), ['workTreeDirectory' => $worktree]);
    }


}