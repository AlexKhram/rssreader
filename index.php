<?php
ini_set('display_errors',1); //////////////////////////////////////////////////////// change it

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/app/config.php';

$app = new Silex\Application();

$app['debug'] = true; //////////////////////////////////////////////////////// change it

// service providers
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/app/src/Alex/View',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $dbParams
));

// models
$app['modelUser'] = new Alex\Model\User($app);
$app['modelChannel'] = new Alex\Model\Chanel($app);
$app['modelFeed'] = new Alex\Model\Feed($app);

// routing
$app->get('/', "Alex\Controller\ChanelController::index");
$app->post('/', "Alex\Controller\ChanelController::add");
$app->post('/del', "Alex\Controller\ChanelController::delete");
$app->get('/update', "Alex\Controller\ChanelController::update");
$app->get('/login', "Alex\Controller\UserController::login");
$app->post('/login', "Alex\Controller\UserController::loginPost");
$app->get('/register', "Alex\Controller\UserController::register");
$app->post('/register', "Alex\Controller\UserController::registerPost");
$app->get('/logout',  "Alex\Controller\UserController::logout");

// errors handling
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 404:
            return new Response( $app['twig']->render('404.twig'), 404);
        default:
            return new Response( $app['twig']->render('500.twig'), 500);
    }

    return new Response('Something went wrong');
});

$app->run();

