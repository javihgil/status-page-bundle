<?php

namespace Jhg\StatusPageBundle\WatchDogs;

use Predis\Client;

class WatchDogReader
{
    /**
     * @var array
     */
    protected $watchDogs;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * WatchDogProcessor constructor.
     *
     * @param array       $watchDogs
     * @param Client      $redis
     */
    public function __construct(array $watchDogs, Client $redis)
    {
        $this->watchDogs = $watchDogs;
        $this->redis = $redis;
    }

    /**
     * @var array|null
     */
    private $readWatchDogs = null;

    /**
     * @return array
     */
    public function read()
    {
        if ($this->readWatchDogs !== null) {
            return $this->readWatchDogs;
        }

        $this->readWatchDogs = [];

        foreach ($this->watchDogs as $watchDogId => $watchDogConfig) {
            $watchDogKey = 'watchdogs:'.$watchDogId;
            $watchDogValue = (int) $this->redis->get($watchDogKey);

            $this->readWatchDogs[$watchDogId] = $watchDogValue >= $watchDogConfig['threshold'];
        }

        return $this->readWatchDogs;
    }
}