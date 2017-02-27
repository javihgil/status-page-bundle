<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusChrono;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseTimeListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @var StatusChrono
     */
    protected $responseTimeEvent;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['startWatchResponse', 4096]],
            KernelEvents::RESPONSE => [['stopWatchResponse', -4096]],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function startWatchResponse(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->responseTimeEvent = new StatusChrono($this->eventKey, $this->eventPeriod, $this->eventExpire);
        $this->responseTimeEvent->start();
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function stopWatchResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->responseTimeEvent->stop();

        if ($this->condition) {
            $conditionContext = [
                'request' => $event->getRequest(),
                'response' => $event->getResponse(),
                'duration' => $this->responseTimeEvent->getDuration(),
            ];

            if (!$this->evalCondition($conditionContext)) {
                return;
            }
        }

        $this->statusStack->registerStatus($this->responseTimeEvent);
    }
}