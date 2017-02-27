<?php

namespace Jhg\StatusPageBundle\GuzzleHttp\Middleware;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StatusMiddlewareFactory
{
    public static function createStatusMiddleware(EventDispatcherInterface $eventDispatcher)
    {
        return new StatusMiddleware($eventDispatcher);
    }
}