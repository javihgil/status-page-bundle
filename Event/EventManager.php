<?php

namespace Jhg\StatusPageBundle\Event;

use Predis\Client;

class EventManager
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var StatusEventInterface[]
     */
    protected $collection;

    /**
     * EventManager constructor.
     *
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param StatusEventInterface $event
     */
    public function registerEvent(StatusEventInterface $event)
    {
        $this->collection[] = $event;
    }

    /**
     * @return StatusEventInterface[]
     */
    public function getEvents()
    {
        return $this->collection;
    }

    public function flush()
    {
        $minute = date('YmdHi');

        foreach ($this->collection as $event) {
            $increment = $event->getIncrement();

            $value = $this->redis->incrby(sprintf('%s:%s', $minute, $event->getKey()), $increment);

            if ($value == $increment) {
                // set expiration time
            }
        }
    }
}