<?php

use Psr\Http\Message\ResponseInterface as Response;

function redirect(Response $response, string $location, int $status = 302)
{
    return $response->withHeader('Location', $location)->withStatus($status);
} 

?>