<?php

namespace Jhg\StatusPageBundle\Status;

class StatusCount implements StatusInterface
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
     * @inheritdoc
     */
    public function getIncrement()
    {
        return 1;
    }
}