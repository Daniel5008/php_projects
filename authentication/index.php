<?php

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->get("/", function () {
    readfile(__DIR__ . '/login.html');
});

$app->run();