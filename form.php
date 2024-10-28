<?php

require_once __DIR__ . '/vendor/autoload.php';

$response = \Laravel\Prompts\form()->confirm('Would you like to copy the .env file?', name: 'env')
    ->confirm('Would you like to run composer install?', name: 'composer')
    ->confirm('Would you like to run npm install?', name: 'npm')
    ->confirm('Would you like to run npm run build?', name: 'npmBuild')
    ->confirm('Would you like to run php artisan migrate?', 0, name: 'migrate')
    ->confirm('Would you like to serve the app?', name: 'serve')
    ->addIf(fn($res) => $res['serve'], fn() => \Laravel\Prompts\text('Enter the port to use: ', '42069', 42069), name: 'port')
    ->submit();
//$port = \Laravel\Prompts\text('Enter the port to use: ', '42069', 42069);
dd($response, $port);