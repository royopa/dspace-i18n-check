<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Royopa\DSpace\i18n\CheckKeys;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->match('/', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time
    $languagesAvailable = array(
        'messages.xml' => 'en (master)',
        'messages_ar.xml' => 'ar',
        'messages_bg.xml' => 'bg',
        'messages_ca.xml' => 'ca',
        'messages_ca_ES.xml' => 'ca_ES',
        'messages_cs.xml' => 'cs',
        'messages_de.xml' => 'de',
        'messages_el.xml' => 'el',
        'messages_es.xml' => 'es',
        'messages_et.xml' => 'et',
        'messages_eu.xml' => 'eu',
        'messages_fr.xml' => 'fr',
        'messages_gl.xml' => 'gl',
        'messages_id.xml' => 'id',
        'messages_it.xml' => 'it',
        'messages_ja.xml' => 'ja',
        'messages_pl.xml' => 'pl',
        'messages_pt_BR.xml' => 'pt_BR',
        'messages_ru.xml' => 'ru',
        'messages_tr.xml' => 'tr',
        'messages_uk.xml' => 'uk',
    );

    $form = $app['form.factory']->createBuilder('form')
        ->add('master', 'choice', array(
            'label' => 'Master *',
            'label_attr' => array('class' => 'col-sm-3 control-label'),
            'attr' => array('class' => 'form-control'),
            'choices' => $languagesAvailable,
            'required' => true,
            'expanded' => false,
            'data' => 'messages.xml',
        ))
        ->add('toCheck', 'choice', array(
            'label' => 'To Check *',
            'label_attr' => array('class' => 'col-sm-3 control-label'),
            'attr' => array('class' => 'form-control'),
            'choices' => $languagesAvailable,
            'required' => true,
            'expanded' => false,
            'empty_data'  => null,
            'placeholder' => 'Choose an option',
        ))
        ->add('Check', 'submit', array(
            'attr' => array('class' => 'btn btn-success')
        ))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        $check = new CheckKeys($data['master'], $data['toCheck']);

        // do something with the data

        // redirect somewhere
        //return $app->redirect('...');
        return new Response(var_dump($check));
    }

    // display the form
    return $app['twig']->render('index.html.twig', array('form' => $form->createView()));
})
->bind('homepage');

$app->match('/update_sources', function (Request $request) use ($app) {
    return new Response('to do');
    
    return $app['twig']->render(
        'index.html.twig',
        array('form' => $form->createView()
    ));
})
->bind('update_sources');

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

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
