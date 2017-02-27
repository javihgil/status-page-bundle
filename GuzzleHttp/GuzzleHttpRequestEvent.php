<?php

namespace Jhg\StatusPageBundle\GuzzleHttp;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

class GuzzleHttpRequestEvent extends Event
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * GuzzleHttpRequestEvent constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}