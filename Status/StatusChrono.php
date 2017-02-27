<?php

namespace Jhg\StatusPageBundle\Status;

use Symfony\Component\Stopwatch\Stopwatch;

class StatusChrono implements StatusInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $period;

    /**
     * @var string
     */
    protected $expire;

    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * ExceptionStatus constructor.
     *
     * @param string $key
     * @param string $period
     * @param string $expire
     */
    public function __construct($key, $period, $expire)
    {
        $this->key = $key;
        $this->period = $period;
        $this->expire = $expire;
        $this->stopwatch = new Stopwatch();
    }

    /**
     * @inheritdoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @inheritdoc
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Starts crono
     */
    public function start()
    {
        $this->stopwatch->start($this->key);
    }

    /**
     * Stops
     */
    public function stop()
    {
        if ($this->stopwatch->isStarted($this->key)) {
            $this->stopwatch->stop($this->key);
        }
    }

    /**
     * @inheritdoc
     */
    public function getIncrement()
    {
        $event = $this->stopwatch->getEvent($this->key);

        return $event->getDuration();
    }
}