<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Event\EventManager;
use Jhg\StatusPageBundle\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListener implements EventSubscriberInterface
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
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->eventStack->registerEvent(new RequestEvent());
    }
}