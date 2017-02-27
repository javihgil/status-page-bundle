<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Status\StatusStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StatusFlushListener implements EventSubscriberInterface
{
    /**
     * @var StatusStack
     */
    protected $eventStack;

    /**
     * StatusRecordListener constructor.
     *
     * @param StatusStack $eventStack
     */
    public function __construct(StatusStack $eventStack)
    {
        $this->eventStack = $eventStack;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => [['onTerminateEvent', -4096]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function onTerminateEvent(PostResponseEvent $event)
    {
        $this->eventStack->flush();
    }
}