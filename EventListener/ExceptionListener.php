<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Event\EventManager;
use Jhg\StatusPageBundle\Event\ExceptionEvent;
use Jhg\StatusPageBundle\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
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
            KernelEvents::EXCEPTION => [['onKernelException', 4096]],
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->eventStack->registerEvent(new ExceptionEvent());
    }
}