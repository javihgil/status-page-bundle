<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusCount;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseCountListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [['registerResponse', -4096]],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function registerResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

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