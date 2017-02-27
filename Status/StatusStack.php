<?php

namespace Jhg\StatusPageBundle\Status;

use Predis\Client;

class StatusStack
{
    const TIME_MARK_FORMATS = [
        'second' => 'YmdHis',
        'minute' => 'YmdHi',
        'hour' => 'YmdH',
        'day' => 'Ymd',
    ];

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var StatusInterface[]
     */
    protected $collection;

    /**
     * @var array
     */
    protected $currentStatuses = [];

    /**
     * StatusStack constructor.
     *
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param StatusInterface $status
     */
    public function registerStatus(StatusInterface $status)
    {
        $this->collection[] = $status;
    }

    /**
     * @return StatusInterface[]
     */
    public function getEvents()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getCurrentStatuses()
    {
        return $this->currentStatuses;
    }

    /**
     * Stores all statuses in redis client
     */
    public function flush()
    {
        foreach ($this->collection as $status) {
            $timeMark = date(self::TIME_MARK_FORMATS[$status->getPeriod()]);
            $statusKey = sprintf('%s:%s', $timeMark, $status->getKey());
            $increment = $status->getIncrement();

            $value = $this->redis->incrby($statusKey, $increment);

            if ($value == $increment) {
                if (is_numeric($status->getExpire())) {
                    $expirationInSeconds = (int) $status->getExpire();
                } else {
                    $expirationInSeconds = strtotime($status->getExpire() , time()) - time();
                }

                $this->redis->expire($statusKey, $expirationInSeconds);
            }

            $this->currentStatuses[$status->getKey()] = $value;
        }
    }
}