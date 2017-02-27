<?php

namespace Jhg\StatusPageBundle\EventListener;

use Jhg\StatusPageBundle\Status\StatusStack;
use Jhg\StatusPageBundle\WatchDogs\WatchDogProcessor;
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
     * @var WatchDogProcessor
     */
    protected $watchDogProcessor;

    /**
     * StatusFlushListener constructor.
     *
     * @param StatusStack       $eventStack
     * @param WatchDogProcessor $watchDogProcessor
     */
    public function __construct(StatusStack $eventStack, WatchDogProcessor $watchDogProcessor)
    {
        $this->eventStack = $eventStack;
        $this->watchDogProcessor = $watchDogProcessor;
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
        $this->watchDogProcessor->process();
    }
}