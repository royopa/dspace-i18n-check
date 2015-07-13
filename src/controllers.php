<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Royopa\DSpace\i18n\CheckerKeys;
use Royopa\DSpace\i18n\SourcesUpdate;
use Royopa\DSpace\i18n\Reader;
use Royopa\DSpace\i18n\Type\FormChecker;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->match('/', function (Request $request) use ($app) {
    $sourcesUpdate = new SourcesUpdate($app['db']);
    $form = $app['form.factory']
        ->createBuilder(new FormChecker())
        ->getForm();
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        $checker = new CheckerKeys($app['db'], $data['master'], $data['toCheck']);

        return $app['twig']->render(
            'list_differences.html.twig',
            array(
                'checker' => $checker,
                'last_update_message' => $sourcesUpdate
                    ->getLastUpdate('messages.xml'),
                'last_update_translations' => $sourcesUpdate
                    ->getLastUpdate('translations_messages_xx.xml'),
            )
        );
    }

    // display the form
    return $app['twig']->render(
        'index.html.twig',
        array(
            'form' => $form->createView(),
            'last_update_message' => $sourcesUpdate
                ->getLastUpdate('messages.xml'),
            'last_update_translations' => $sourcesUpdate
                ->getLastUpdate('translations_messages_xx.xml'),
        )
    );
})
->bind('homepage');

$app->match('/update_sources', function (Request $request) use ($app) {
    return $app['twig']->render(
        'update_sources.html.twig',
        array()
    );
})
->bind('update_sources');

$app->match('/reader', function (Request $request) use ($app) {
    return new Reader();

    return $app['twig']->render(
        'update_sources.html.twig',
        array()
    );
})
->bind('reader');

$app->match('/update_sources_list', function (Request $request) use ($app) {
    $rows = $app['db']->fetchAll('SELECT * FROM update_source');
    return $app['twig']->render(
        'update_sources_list.html.twig',
        array('rows' => $rows)
    );
})
->bind('update_sources_list');

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']
        ->resolveTemplate($templates)
        ->render(array('code' => $code)), $code);
});
