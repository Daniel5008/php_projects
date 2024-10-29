<?php

use Psr\Container\ContainerInterface;

return function (ContainerInterface $containerInterface) 
{
    $containerInterface->set('settings', function () {
        return [
            'displayErrorDetails' => true,
            'logErrors' => true,
            'logErrorDetails' => true
        ];
    });
};