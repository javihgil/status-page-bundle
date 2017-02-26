<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Event\EventManager;
use Jhg\StatusPageBundle\Event\ResponseTimeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseTimeListener implements EventSubscriberInterface
{
    /**
     * @var EventManager
     */
    protected $eventStack;

    /**
     * RequestListener constructor.
     *
     * @param EventManager $eventStack
     */
    public function __construct(EventManager $eventStack)
    {
        $this->eventStack = $eventStack;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 4096]],
            KernelEvents::RESPONSE => [['onKernelResponse', -4096]],
        ];
    }

    /**
     * @var ResponseTimeEvent
     */
    protected $responseTimeEvent;

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->responseTimeEvent = new ResponseTimeEvent();
        $this->responseTimeEvent->start();
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->responseTimeEvent->stop();
        $this->eventStack->registerEvent($this->responseTimeEvent);
    }
}