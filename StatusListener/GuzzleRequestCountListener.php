<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpEvents;
use Jhg\StatusPageBundle\GuzzleHttp\GuzzleHttpRequestEvent;
use Jhg\StatusPageBundle\Status\StatusCount;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GuzzleRequestCountListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GuzzleHttpEvents::REQUEST => [['registerGuzzleRequest', 4096]],
        ];
    }

    /**
     * @param GuzzleHttpRequestEvent $event
     */
    public function registerGuzzleRequest(GuzzleHttpRequestEvent $event)
    {
        if ($this->condition) {
            $conditionContext = [
                'request' => $event->getRequest(),
            ];

            if (!$this->evalCondition($conditionContext)) {
                return;
            }
        }

        $this->statusStack->registerStatus(new StatusCount($this->eventKey, $this->eventPeriod, $this->eventExpire));
    }
}