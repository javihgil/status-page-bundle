<?php

namespace Jhg\StatusPageBundle\Event;

use Symfony\Component\Stopwatch\Stopwatch;

class ResponseTimeEvent implements StatusEventInterface
{
    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
    }

    public function start()
    {
        $this->stopwatch->start('response-time');
    }

    public function stop()
    {
        if ($this->stopwatch->isStarted('response-time')) {
            $this->stopwatch->stop('response-time');
        }
    }

    public function getKey()
    {
        return 'response-time';
    }

    public function getIncrement()
    {
        $event = $this->stopwatch->getEvent('response-time');

        return $event->getDuration();
    }
}