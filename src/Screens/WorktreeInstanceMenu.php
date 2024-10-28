<?php

namespace Mattcoleman\PrServe\Screens;

use Mattcoleman\PrServe\Screens\Contracts\Screen;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\info;

class WorktreeInstanceMenu extends Screen
{
    /**
     * @var mixed|string
     */
    public string $workTreeDirectory;

    public function render(array $params = []): void
    {
        $this->workTreeDirectory = $params['workTreeDirectory'] ?? $this->router->worktreeDirectory;

        $options = [
            'Build',
            'Delete',
            'Back to Main Menu',
            'Exit'
        ];

        $selected = \Laravel\Prompts\select('Main Menu', $options);

        $index = array_search($selected, $options);
        match ($index) {
            0 => $this->build(),
            1 => $this->delete(),
            2 => $this->mainMenu(),
            default => exit(0)
        };

    }

    protected function build()
    {
        $response = \Laravel\Prompts\form()->confirm('Would you like to copy the .env file?', name: 'env')
            ->confirm('Would you like to run composer install?', name: 'composer')
            ->confirm('Would you like to run npm install?', name: 'npm')
            ->confirm('Would you like to run npm run build?', name: 'npmBuild')
            ->confirm('Would you like to run php artisan migrate?', 0, name: 'migrate')
            ->confirm('Would you like to serve the app?', name: 'serve')
            ->addIf(fn($res) => $res['serve'], fn() => \Laravel\Prompts\text('Enter the port to use: ', '42069', 42069), name: 'port')
            ->submit();

        extract($response);
        if ($env) {
            info("Copying .env file\n" . $this->router->repoDirectory . '/.env'. " to ". $this->workTreeDirectory . '/.env');
            $copied = copy($this->router->repoDirectory . '/.env', $this->workTreeDirectory . '/.env');
            if (!$copied) {
                \Laravel\Prompts\error("Error: copying .env file\n");
                exit(1);
            }
        }
        chdir($this->workTreeDirectory);

        if ($composer) {
            $process = new Process(['composer', 'install']);
            $process->start();
            foreach ($process as $type => $data) {
                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: running composer install?\n");
            }
        }

        if ($npm) {
            $process = new Process(['npm', 'install']);
            $process->start();
            foreach ($process as $type => $data) {
                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: running npm install?\n");
            }
        }

        if ($npmBuild) {
            $process = new Process(['npm', 'run', 'build']);
            $process->start();
            foreach ($process as $type => $data) {

                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: running npm run build\n");
            }
        }

        if ($migrate) {
            $process = new Process(['php', 'artisan', 'migrate']);
            $process->start();
            foreach ($process as $type => $data) {
                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: running php artisan migrate\n");
                exit(1);
            }
        }

        if ($serve) {
            $process = new Process(['php', 'artisan', 'serve', '--port=' . $port]);
            $process->setTimeout(0);
            $process->start();
            foreach ($process as $type => $data) {
                \Laravel\Prompts\info($data);
            }
            if ($type === Process::ERR) {
                \Laravel\Prompts\error("Error: running php artisan serve\n");
                exit(1);
            }
        }

        info("Goodbye!\n");
    }

    protected function delete()
    {
    }

    protected function mainMenu()
    {
        $this->router->push(new MainMenu());
    }
}