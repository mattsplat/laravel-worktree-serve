<?php

namespace Mattcoleman\PrServe\Service;

use Symfony\Component\Process\Process;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class GitService
{
    public static function status(string $path): ?string
    {

//        (new Command(['git', 'status'],
//            function (string $output) {
//                info($output);
//            },
//            function (string $error) {
//                error($error);
//            }
//        ))->run();

        $process = new Process(['git', 'status']);
        $process->setWorkingDirectory($path);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    public static function fetch(string $path): void
    {
        info("Fetching branches\n");
        $process = new Process(['git', 'fetch', '--all']);
        $process->setWorkingDirectory($path);
        $process->run();
    }
    public static function getBranches(string $path): array
    {
        $process = new Process(['git', 'branch', '-r']);
        if ($path) {
            $process->setWorkingDirectory($path);
        }
        static::fetch($path);
        $process->start();
        $branches = [];
        foreach ($process as $type => $data) {
            if($type === Process::ERR) {
                error("Error: getting branches\n");
            }
            $branches = array_map('trim', explode("\n", $data));
        }
        $branches = array_map(function ($branch) {
            return str_replace('origin/', '', $branch);
        }, $branches);

        return $branches;
    }
    public static function getWorktrees(?string $path = null): array
    {
        $worktrees = [];
        $process = new Process(['git', 'worktree', 'list']);
        if ($path) {
            $process->setWorkingDirectory($path);
        }
        $process->run();
        if (!$process->isSuccessful()) {
            error($process->getErrorOutput());
            exit(1);
        }
        return $worktrees;
    }

    public static function pruneWorktrees(string $workTreeDirectory, ?string $path = null, bool $force = true): void
    {
        $process = new Process(['git', 'worktree', 'prune']);
        if ($force) {
            $process->add('--force');
        }
        if ($path) {
            $process->setWorkingDirectory($path);
        }
        $process->run();
        if (!$process->isSuccessful()) {
            error($process->getErrorOutput());
            exit(1);
        }
    }

    public static function createWorktree(string $path, string $branch, string $repoDirectory): bool
    {
        info(implode(', ', ['git', 'worktree', 'add', $path, $branch]));
        $process = new Process(['git', 'worktree', 'add', $path, $branch]);
        $process->setWorkingDirectory($repoDirectory);
        $process->run();
        if (!$process->isSuccessful()) {
            error($process->getErrorOutput());
            return false;
        }
        info($process->getOutput());
        return true;
    }

    public static function removeWorktree(string $path): bool
    {
        $process = new Process(['git', 'worktree', 'remove', $path]);
        $process->run();
        if (!$process->isSuccessful()) {
            error($process->getErrorOutput());
            return false;
        }
        info($process->getOutput());
        return true;
    }
}