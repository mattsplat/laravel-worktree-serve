<?php

require_once  __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;
const aliasScreen = "\e[?1049h";
const aliasScreenEnd = "\e[?1049l";

$output = new \Laravel\Prompts\Output\ConsoleOutput();


$argv = $argv ?? [];
$argc = $argc ?? 0;
if ($argc < 2) {
    \Laravel\Prompts\error("Usage: php index.php <command>\n");
    exit(1);
}
$directory = $argv[1];
if (!is_dir($directory)) {
    \Laravel\Prompts\error("Directory not found: $directory");
    exit(1);
}

chdir($directory);
// look for git folder
if (!is_dir('.git')) {
    \Laravel\Prompts\error("Error: not a git repository\n");
    exit(1);
}
$process = new Process(['git', 'status']);
$output->writeln("" . $process->getWorkingDirectory(), );
$process->run();
if (!$process->isSuccessful()) {
    $output->writeDirectly($process->getErrorOutput());
    exit(1);
} else {
    $output->writeDirectly($process->getOutput());
}

// Would you like to add a worktree?
$worktree = \Laravel\Prompts\confirm('Would you like to add a worktree?');
 if (!$worktree) {
     \Laravel\Prompts\info("Goodbye!\n");
     exit(0);
 }

// get branches
info("Fetching branches\n");
$fetch = new Process(['git', 'fetch', '--all']);
$fetch->run();
info($fetch->getOutput());
$process = new Process(['git', 'branch', '-r']);
$process->start();
$branches = [];
foreach ($process as $type => $data) {
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: getting branches\n");
        exit(1);
    }
    $branches = array_map('trim', explode("\n", $data));
}


$branch = \Laravel\Prompts\select(
    label: 'Select a branch to use. ',
    options: $branches,
    scroll: 10
);

// what directory would you like to use?
$worktreeDirectory = \Laravel\Prompts\text('Enter the directory to use: ');

// create the worktree
$process = new Process(['git', 'worktree', 'add', $worktreeDirectory, $branch]);
$process->start();
foreach ($process as $type => $data) {
    \Laravel\Prompts\info($data);
}
if ($type === Process::ERR) {
    if (str_contains($data, 'already exists')) {
        \Laravel\Prompts\error("Error: directory already exists\n");
        $resolveOptions = ['Remove it and reinstall', 'Use existing', 'Go home and cry'];
        $resolve = \Laravel\Prompts\select('What would you like to do?',
            $resolveOptions,
            0
        );
        if ($resolve === $resolveOptions[0]) {
            (new Process(['rm', '-rf', $worktreeDirectory]))->run();
            $process = new Process(['git', 'worktree', 'prune']);
            $process->start();
            foreach ($process as $type => $data) {
                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: deleting directory\n");
                exit(1);
            }
        } elseif ($resolve === $resolveOptions[1]) {
            \Laravel\Prompts\info("Using existing directory\n");
            // git pull
            chdir($worktreeDirectory);
            $process = new Process(['git', 'pull']);
            $process->run();
            if (!$process->isSuccessful()) {
                \Laravel\Prompts\error("Error: pulling changes\n");
                \Laravel\Prompts\info($process->getErrorOutput());
                exit(1);
            }
            chdir($directory);
        } else {
            \Laravel\Prompts\info("Goodbye!\n");
            exit(0);
        }

    } else {
        \Laravel\Prompts\error("Error: creating worktree\n");
        exit(1);
    }
}

$response =  \Laravel\Prompts\form()->confirm('Would you like to copy the .env file?', name: 'env')
    ->confirm('Would you like to run composer install?', name: 'composer')
    ->confirm('Would you like to run npm install?', name: 'npm')
    ->confirm('Would you like to run npm run build?', name: 'npmBuild')
    ->confirm('Would you like to run php artisan migrate?', 0, name: 'migrate')
    ->confirm('Would you like to serve the app?', name: 'serve')
    ->addIf(fn($res) => $res['serve'], fn() => \Laravel\Prompts\text('Enter the port to use: ', '42069', 42069), name: 'port')
    ->submit();

// [$env, $composer, $npm, $npmBuild, $migrate, $serve, $port] = $response;
extract($response);

//$env = \Laravel\Prompts\confirm('Would you like to copy the .env file?');
if ($env) {
    $copied = copy('.env', $worktreeDirectory . '/.env');
    if(!$copied) {
        \Laravel\Prompts\error("Error: copying .env file\n");
        exit(1);
    }
}
// change to the worktree directory
chdir($worktreeDirectory);

// would you like to run composer install?
//$composer = \Laravel\Prompts\confirm('Would you like to run composer install?');
if ($composer) {
    $process = new Process(['composer', 'install']);
    $process->start();
    foreach ($process as $type => $data) {
        \Laravel\Prompts\info($data);
    }
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: running composer install?\n");
    }
}

// would you like to run npm install?
//$npm = \Laravel\Prompts\confirm('Would you like to run npm install?');
if ($npm) {
    $process = new Process(['npm', 'install']);
    $process->start();
    foreach ($process as $type => $data) {
        \Laravel\Prompts\info($data);
    }
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: running npm install?\n");
    }
}

// would you like to run npm run build?
//$npmBuild = \Laravel\Prompts\confirm('Would you like to run npm run build?');
if ($npmBuild) {
    $process = new Process(['npm', 'run', 'build']);
    $process->start();
    foreach ($process as $type => $data) {

        \Laravel\Prompts\info($data);
    }
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: running npm run build\n");
    }
}

// would you like to run php artisan migrate?
//$migrate = \Laravel\Prompts\confirm('Would you like to run php artisan migrate?', false);
if ($migrate) {
    $process = new Process(['php', 'artisan', 'migrate']);
    $process->start();
    foreach ($process as $type => $data) {
        \Laravel\Prompts\info($data);
    }
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: running php artisan migrate\n");
        exit(1);
    }
}

// would you like to serve
//$serve = \Laravel\Prompts\confirm('Would you like to serve the app?', false);
if ($serve) {
    $process = new Process(['php', 'artisan', 'serve', '--port=' . $port]);
    $process->start();
    foreach ($process as $type => $data) {
        \Laravel\Prompts\info($data);
    }
    if($type === Process::ERR) {
        \Laravel\Prompts\error("Error: running php artisan serve\n");
        exit(1);
    }
}

info("Goodbye!\n");


