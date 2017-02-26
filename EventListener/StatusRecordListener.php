<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Event\EventManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StatusRecordListener implements EventSubscriberInterface
{
    /**
     * @var EventManager
     */
    protected $eventStack;

    /**
     * StatusRecordListener constructor.
     *
     * @param EventManager $eventStack
     */
    public function __construct(EventManager $eventStack)
    {
        $this->eventStack = $eventStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => [['onTerminateEvent', -4096]],
        ];
    }

    public function onTerminateEvent(PostResponseEvent $event)
    {
        $this->eventStack->flush();
    }
}