<?php
use Slim\Http\Request;
use Slim\Http\Response;

$config = [
    'settings' => [
        'displayErrorDetails' => true, 
        'renderer'            => [
            'blade_template_path' => __DIR__ . '/views',
            'blade_cache_path'    => __DIR__ . '/cache', 
        ],
    ],
];

$app = new \Slim\App($config);

$container = $app->getContainer();

$container['view'] = function ($container) {
    return new \Slim\Views\Blade(
        $container['settings']['renderer']['blade_template_path'],
        $container['settings']['renderer']['blade_cache_path']
    );
};

$app->get('/', ImadokoSearchController::class . ':index');

$app->run();