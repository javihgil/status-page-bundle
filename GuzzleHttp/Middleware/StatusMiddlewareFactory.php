<?php

namespace Jhg\StatusPageBundle\GuzzleHttp\Middleware;

use Predis\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

class StatusMiddlewareFactory
{
    public static function createStatusMiddleware(EventDispatcher $eventDispatcher)
    {
        return new StatusMiddleware($eventDispatcher);
    }
}