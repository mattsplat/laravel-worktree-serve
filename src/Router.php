<?php

namespace Mattcoleman\PrServe;

use Mattcoleman\PrServe\Screens\Contracts\Screen;

class Router
{
    public array $history = [];

    public string $repoDirectory = '';
    public string $worktreeDirectory = '';
    public function __construct()
    {

    }

    public function push(Screen $screen, array $params = [])
    {
        $this->history[] = ['class' => $screen::class, 'params' => $params];
        $screen->setRouter($this);
        $screen->render($params);
    }

    public function back()
    {
        array_pop($this->history);
        $last = end($this->history);
        $screen = new $last['class']();
        $screen->setRouter($this);
        $screen->render($last['params']);
    }
}