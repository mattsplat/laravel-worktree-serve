<?php


use Mattcoleman\PrServe\Service\Command;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

require_once __DIR__ . '/vendor/autoload.php';

//$process = \Symfony\Component\Process\Process::fromShellCommandline('echo "${:HOME}"');
//echo $process->run()->getOutput();

exec('ech "$HOME"',$output);
var_dump($output);
(new Command(
    ['echo', '"$HOME"'],
    function ($process) use (&$default) {
        info($process->getOutput());
        $default = trim($process->getOutput());
        info('Setting default repo directory to ' . $default);
    },
    function ($process) {
        error('Error getting default repo directory');
        exit(1);
    }
))->run();