<?php

use Silex\Provider\TwigServiceProvider;
use Workflow\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Workflow\Providers\RoutesProvider;

$app = new Application();

$app['debug'] = true;

$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views',
));
$app->register(new HttpFragmentServiceProvider());

$app->mount('/', new RoutesProvider());

return $app;