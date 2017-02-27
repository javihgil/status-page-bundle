<?php

namespace Jhg\StatusPageBundle\Twig;

use Jhg\StatusPageBundle\WatchDogs\WatchDogReader;

class WatchDogExtension extends \Twig_Extension
{
    /**
     * @var WatchDogReader
     */
    protected $watchDogReader;

    /**
     * WatchDogExtension constructor.
     *
     * @param WatchDogReader $watchDogReader
     */
    public function __construct(WatchDogReader $watchDogReader)
    {
        $this->watchDogReader = $watchDogReader;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('watchdog', [$this, 'checkWatchdog']),
        ];
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function checkWatchDog($id)
    {
        $watchdogs = $this->watchDogReader->read();

        if (!isset($watchdogs[$id])) {
            return false;
        }

        return $watchdogs[$id];
    }
}