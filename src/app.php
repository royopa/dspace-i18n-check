<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\FormServiceProvider;
use Doctrine\DBAL\Schema\Table;

$app = new Application();
$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
));
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
      'locale' => 'pt_BR',
      'translation.class_path' =>  __DIR__ . '/../vendor/symfony/src',
      'translator.messages' => array()
)) ;
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath().'/'.ltrim($asset, '/');
    }));

    return $twig;
});
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
    ),
));
$schema = $app['db']->getSchemaManager();
if (!$schema->tablesExist('update_source')) {
    $table = new Table('update_source');
    $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
    $table->setPrimaryKey(array('id'));
    $table->addColumn('name_source', 'string', array('length' => 32));
    $table->addColumn('date', 'datetime');
    $schema->createTable($table);
}

return $app;
