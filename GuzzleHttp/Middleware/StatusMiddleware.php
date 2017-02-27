<?php

namespace Jhg\StatusPageBundle\GuzzleHttp\Middleware;

use GuzzleHttp\Promise\RejectedPromise;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpEvents;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpRequestEvent;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpResponseEvent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StatusMiddleware
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * StatusMiddleware constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $this->eventDispatcher->dispatch(GuzzleHttpEvents::REQUEST, new GuzzleHttpRequestEvent($request));

            return $handler($request, $options)->then(
                function (ResponseInterface $response) use ($request) {
                    $this->eventDispatcher->dispatch(GuzzleHttpEvents::RESPONSE, new GuzzleHttpResponseEvent($request, $response));

                    return $response;
                },
                function ($reason) {
                    // TODO dispatch error event

                    return new RejectedPromise($reason);
                }
            );
        };
    }
}