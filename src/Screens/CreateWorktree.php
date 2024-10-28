<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Screens\Contracts\Screen;
use Mattcoleman\PrServe\Service\GitService;
use Symfony\Component\Process\Process;
use function ArtisanBuild\CommunityPrompts\fileselector;
use function Laravel\Prompts\error;

class CreateWorktree extends Screen
{
    protected string $worktreeDirectory;

    /**
     * @param \Mattcoleman\PrServe\Router $router
     */
    public function __construct()
    {
    }

    public function render(array $params = []): void
    {
        // get branches
        $branches = GitService::getBranches($this->router->repoDirectory);

        $branch = \Laravel\Prompts\select(
            label: 'Select a branch to use. ',
            options: $branches,
            scroll: 10
        );


        // what directory would you like to use?
        $this->worktreeDirectory = fileselector(label: 'Enter the directory to use: ', default: $this->router->repoDirectory);


        $success = GitService::createWorktree($this->worktreeDirectory, $branch, $this->router->repoDirectory);
        if (!$success) {
            $this->handleAlreadyExists();
        }
        $this->router->worktreeDirectory = $this->worktreeDirectory;
        $this->router->push(new WorktreeInstanceMenu(), ['workTreeDirectory' => $this->worktreeDirectory]);
    }

    protected function handleAlreadyExists()
    {
        error("Error: directory already exists\n");
//        if ($type === Process::ERR) {
//            if (str_contains($data, 'already exists')) {
//                \Laravel\Prompts\error("Error: directory already exists\n");
//                $resolveOptions = ['Remove it and reinstall', 'Use existing', 'Go home and cry'];
//                $resolve = \Laravel\Prompts\select('What would you like to do?',
//                    $resolveOptions,
//                    0
//                );
//                if ($resolve === $resolveOptions[0]) {
//                    (new Process(['rm', '-rf', $this->worktreeDirectory]))->run();
//                    $process = new Process(['git', 'worktree', 'prune']);
//                    $process->start();
//                    foreach ($process as $type => $data) {
//                        \Laravel\Prompts\info($data);
//                    }
//                    if ($type === Process::ERR) {
//                        \Laravel\Prompts\error("Error: deleting directory\n");
//                        exit(1);
//                    }
//                } elseif ($resolve === $resolveOptions[1]) {
//                    \Laravel\Prompts\info("Using existing directory\n");
//                    // git pull
//                    chdir($worktreeDirectory);
//                    $process = new Process(['git', 'pull']);
//                    $process->run();
//                    if (!$process->isSuccessful()) {
//                        \Laravel\Prompts\error("Error: pulling changes\n");
//                        \Laravel\Prompts\info($process->getErrorOutput());
//                        exit(1);
//                    }
//                    chdir($directory);
//                } else {
//                    \Laravel\Prompts\info("Goodbye!\n");
//                    exit(0);
//                }
//
//            } else {
//                \Laravel\Prompts\error("Error: creating worktree\n");
//                exit(1);
//            }
//        }

    }
}