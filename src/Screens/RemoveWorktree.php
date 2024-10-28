<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Screens\Contracts\Screen;

class RemoveWorktree extends Screen
{
    public string $workTreePath;



    public function render(array $params = []): void
    {
        if (!empty($params['workTreePath'])) {
            $this->workTreePath = $params['workTreePath'];
        } else {
            // show list of worktrees

        }
        // prune worktrees
    }
}