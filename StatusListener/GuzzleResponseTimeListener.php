<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpEvents;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpRequestEvent;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpResponseEvent;
use Jhg\StatusPageBundle\Status\StatusChrono;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GuzzleResponseTimeListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @var StatusChrono[]
     */
    protected $responseTimeEvents = [];

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GuzzleHttpEvents::REQUEST => [['startWatchResponse', 4096]],
            GuzzleHttpEvents::RESPONSE => [['stopWatchResponse', -4096]],
        ];
    }

    /**
     * @param GuzzleHttpRequestEvent $event
     */
    public function startWatchResponse(GuzzleHttpRequestEvent $event)
    {
        $requestId = spl_object_hash($event->getRequest());
        $this->responseTimeEvents[$requestId] = new StatusChrono($this->eventKey, $this->eventPeriod, $this->eventExpire);
        $this->responseTimeEvents[$requestId]->start();
    }

    /**
     * @param GuzzleHttpResponseEvent $event
     */
    public function stopWatchResponse(GuzzleHttpResponseEvent $event)
    {
        $requestId = spl_object_hash($event->getRequest());

        $this->responseTimeEvents[$requestId]->stop();

        if ($this->condition) {
            $conditionContext = [
                'request' => $event->getRequest(),
                'response' => $event->getResponse(),
                'duration' => $this->responseTimeEvents[$requestId]->getDuration(),
            ];

            if (!$this->evalCondition($conditionContext)) {
                return;
            }
        }

        $this->statusStack->registerStatus($this->responseTimeEvents[$requestId]);
    }
}