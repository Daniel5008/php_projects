<?php 

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;
use Src\Database\SqlConnection;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

AppFactory::setContainer($container);
$app = AppFactory::create();

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function (Request $request, Response $response) use ($twig) {
    $teste = new SqlConnection();
    
    var_dump($teste->select("select * from tb_users"));

    

    return $response;
});

$app->get('/login', function (Request $request, Response $response) use ($twig) {
    return $twig->render($response, 'login.twig');
});

$app->post('/login', function (Request $request, Response $response) use ($twig) {
    $data = $request->getParsedBody();
    
    return $response;
});

$app->get('/register', function (Request $request, Response $response) use ($twig) {
    return $twig->render($response, 'register.twig');
});

$app->run();

