<?php
set_time_limit(0);
ini_set('max_execution_time', 0);
require_once  __DIR__ . '/vendor/autoload.php';

use Mattcoleman\PrServe\Router;
use Mattcoleman\PrServe\Screens\MainMenu;
use function Laravel\Prompts\info;

const aliasScreen = "\e[?1049h";
const aliasScreenEnd = "\e[?1049l";
$output = new \Laravel\Prompts\Output\ConsoleOutput();

$router = new Router();
/**
 * If supplying a directory as an argument, set the repoDirectory
 * @var string $directory
 */
if (isset($argv[1]) && !str_starts_with($argv[1], '-')) {
    $directory = $argv[1];
    $router->repoDirectory = $argv[1];
}
$output->writeDirectly(aliasScreen);
$router->push(new \Mattcoleman\PrServe\Screens\CheckRepoDirectory());
$router->push(new MainMenu());

info("Goodbye!\n");



