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

$app->post('/register', function (Request $request, Response $response) use ($twig) {
    $postData = $request->getParsedBody();
    $errorMessage = '';

    if ($postData['password'] !== $postData['confirm_password']){
        $errorMessage = "As senhas devem ser iguais.";
    }

    if (User::checkUsernameInUse($postData['username'])) {
        $errorMessage = "Este nome de usuário já está em uso.";
    }

    if (User::checkEmailInUse($postData['username'])) {
        $errorMessage = "Este email já está em uso.";
    }

    if ($errorMessage != '') {
        return $twig->render($response, 'register.twig', [
            'error' => $errorMessage
        ]);
    }

    $user = new User();
    $user->setData(
        [
            'username' => $postData['username'],
            'email' => $postData['email'],
            'password' => $postData['password']
        ]
    );

    try {
        $user->register();
    } catch ( Exception $e) {
        return $twig->render($response, 'register.twig', [
            'error' => $e->getMessage()
        ]);
    }

    $user->login($postData['username'], $postData['password']);

    return $response;
});

$app->run();

