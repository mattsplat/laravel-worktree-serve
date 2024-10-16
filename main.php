<?php

require_once  __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Process\Process;
use function Laravel\Prompts\text;
const aliasScreen = "\e[?1049h";
const aliasScreenEnd = "\e[?1049l";

$output = new \Laravel\Prompts\Output\ConsoleOutput();


$argv = $argv ?? [];
$argc = $argc ?? 0;
if ($argc < 2) {
    echo "\n\nUsage: php main.php <command>\n";
    exit(1);
}
$directory = $argv[1];
if (!is_dir($directory)) {
    echo "\n\nDirectory not found: $directory\n";
    exit(1);
}
chdir($directory);
$ls = new Process(['ls', '-la']);
$ls->start();
foreach ($ls as $type => $data) {
    if ($ls::OUT === $type) {
        echo "\nRead from stdout: ".$data;
    } else { // $ls::ERR === $type
        echo "\nRead from stderr: ".$data;
    }
}
echo "\n\n";
$process = new Process(['git', 'status']);
echo $process->getWorkingDirectory() . "\n";
$process->start();
if (!$process->isSuccessful()) {
    $output->write($process->getOutput(), 1);
    exit(1);
} else {
    $output->write(aliasScreen, 1);
    $output->write($process->getOutput(), 1);
    $output->write(aliasScreenEnd, 1);
}
foreach ($process as $type => $data) {
    if ($process::OUT === $type) {
        echo "\nRead from stdout: ".$data;
    } else { // $process::ERR === $type
        echo "\nRead from stderr: ".$data;
    }
}

//$output = new \Laravel\Prompts\Output\ConsoleOutput();
//$output->write(aliasScreen, 1,
//    \Symfony\Component\Console\Output\OutputInterface::OUTPUT_NORMAL
//);
//
//$branches = [];
//exec('git branch', $branches);
//dd($branches);
//$name = text(implode("\n", $branches));
//
//$output->write(aliasScreenEnd);

