<?php

session_start();

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;
use Src\Models\User;

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

$app->get('/login', function (Request $request, Response $response) use ($twig) {   
   $loginError = $_SESSION['loginError'];
   $_SESSION['loginError'] = null;

   return $twig->render($response, 'login.twig', ['error' => $loginError]);
});

$app->post('/login', function (Request $request, Response $response) use ($twig) {
   $postData = $request->getParsedBody();

   User::login($postData['username'], $postData['password']);

   if ($_SESSION['loginError'] !== null) {
      return $response->withHeader('Location', '/login')->withStatus(302);
   }

   return $response->withHeader('Location', '/home')->withStatus(302);
});

$app->get('/register', function (Request $request, Response $response) use ($twig) {
   $registerError = $_SESSION['registerError'];
   $formData = $_SESSION['formData'];

   $_SESSION['registerError'] = null;
   $_SESSION['formData'] = null;

   return $twig->render($response, 'register.twig', [
      'error' => $registerError,
      'formData' => $formData
   ]);
});

$app->post('/register', function (Request $request, Response $response) use ($twig) {
   $postData = $request->getParsedBody();

   if ($postData['password'] !== $postData['confirm_password']) {
      $_SESSION['registerError'] = "As senhas devem ser iguais.";
   }

   if (User::checkUsernameInUse($postData['username'])) {
      $_SESSION['registerError'] = "Este nome de usuário já está em uso.";
   }

   if (User::checkEmailInUse($postData['email'])) {
      $_SESSION['registerError'] = "Este email já está em uso.";
   }

   if ($_SESSION['registerError'] !== null) {
      $_SESSION['formData'] = $postData;
      return $response->withHeader('Location', '/register')->withStatus(302);
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
   } catch (Exception $e) {
      $_SESSION['registerError'] = 'Ocorreu um erro ao realizar o registro, por favor tente novamente mais tarde.';
      return $response->withHeader('Location', '/register')->withStatus(302);
   }

   User::login($postData['username'], $postData['password']);

   return $response->withHeader('Location', '/home')->withStatus(302);
});

$app->get('/home', function (Request $request, Response $response) {

   $response->getBody()->write("Seja bem vindo user!");

   var_dump($_SESSION);

   return $response;

});

$app->run();

