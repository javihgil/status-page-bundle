<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpEvents;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpResponseEvent;
use Jhg\StatusPageBundle\Status\StatusCount;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GuzzleResponseCountListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GuzzleHttpEvents::RESPONSE => [['registerResponse', -4096]],
        ];
    }

    /**
     * @param GuzzleHttpResponseEvent $event
     */
    public function registerResponse(GuzzleHttpResponseEvent $event)
    {
        if ($this->condition) {
            $conditionContext = [
                'request' => $event->getRequest(),
                'response' => $event->getResponse(),
            ];

            if (!$this->evalCondition($conditionContext)) {
                return;
            }
        }

        $this->statusStack->registerStatus(new StatusCount($this->eventKey, $this->eventPeriod, $this->eventExpire));
    }
}