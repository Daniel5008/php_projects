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
require __DIR__ . '/../src/functions/helpers.php';

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
   $loginError = isset($_SESSION['loginError']) ? $_SESSION['loginError'] : null;
   $_SESSION['loginError'] = null;

   return $twig->render($response, 'login.twig', ['error' => $loginError]);
});

$app->post('/login', function (Request $request, Response $response) {
   $postData = $request->getParsedBody();
   $ipAddress = $request->getServerParams()['REMOTE_ADDR'];
   
   User::login($postData['username'], $postData['password'], $ipAddress);

   if ($_SESSION['loginError'] !== null) {
      return redirect($response, '/login');
   }

   return redirect($response, '/home');

});

$app->get('/register', function (Request $request, Response $response) use ($twig) {
   $registerError = isset($_SESSION['registerError']) ? $_SESSION['registerError'] : null;
   $formData = isset($_SESSION['formData']) ? $_SESSION['formData'] : null;

   $_SESSION['registerError'] = null;
   $_SESSION['formData'] = null;

   return $twig->render($response, 'register.twig', [
      'error' => $registerError,
      'formData' => $formData
   ]);
});

$app->post('/register', function (Request $request, Response $response) {
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
      // return $response->withHeader('Location', '/register')->withStatus(302);
      return redirect($response, '/register');
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
      return redirect($response, '/register');
   }

   $ipAddress = $request->getServerParams()['REMOTE_ADDR'];
   User::login($postData['username'], $postData['password'], $ipAddress);

   return redirect($response, '/home');

});

$app->get('/home', function (Request $request, Response $response) use ($twig) {

   if (!User::isUserLoggedIn()) {
      return redirect($response, '/login');
   }

   $user = User::getFromSession();
   $userData = $user->getValues();

   return $twig->render($response, 'home.twig', ['user' => $userData]);

});

$app->post('/logout', function (Request $request, Response $response) {
   session_unset();
   session_destroy();

   return redirect($response, '/login');
});

$app->get('/erro', function (Request $request, Response $response)  use ($twig) {
   
   return $twig->render($response, 'error.twig');
});

$app->run();

