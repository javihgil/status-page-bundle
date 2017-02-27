<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusCount;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener extends AbstractStatusListener implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [['registerException', 4096]],
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function registerException(GetResponseForExceptionEvent $event)
    {
        $this->statusStack->registerStatus(new StatusCount($this->eventKey, $this->eventPeriod, $this->eventExpire));
    }
}