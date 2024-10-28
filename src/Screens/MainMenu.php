<?php

namespace Mattcoleman\PrServe\Screens;

use function Laravel\Prompts\info;

class MainMenu extends Contracts\Screen
{
    public function render(array $params = []): void
    {

        $options = [
            'Show worktree list',
            'Create a new worktree',
            'Delete a worktree',
            'Prune worktrees',
            'Select an existing worktree',
            'Exit'
        ];

        $selected = \Laravel\Prompts\select('Main Menu', $options);

        $index = array_search($selected, $options);
        match ($index) {
            0 => $this->showWorktreeList(),
            1 => $this->createWorktree(),
            2 => $this->removeWorktree(),
            3 => $this->pruneWorktrees(),
            4 => $this->selectWorktree(),
            default => exit(0)
        };
    }

    protected function showWorktreeList()
    {
        $this->router->push(new WorktreeList());
    }

    protected function createWorktree()
    {
        $this->router->push(new CreateWorktree());
    }

    protected function removeWorktree()
    {
        $this->router->push(new RemoveWorktree());
    }

    protected function pruneWorktrees()
    {
        info('Pruning worktrees...');
    }

    protected function selectWorktree()
    {
        $this->router->push(new SelectWorktree());
    }

}