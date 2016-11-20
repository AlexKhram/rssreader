<?php
ini_set('display_errors',1); //////////////////////////////////////////////////////// change it

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true; //////////////////////////////////////////////////////// change it

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/app/src/Alex/View',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'rss',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ]
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
$app->run();

