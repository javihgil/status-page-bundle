<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusCount;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestCountListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['registerRequest', 4096]],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function registerRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

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