<?php

namespace Jhg\StatusPageBundle\WatchDogs;

use Jhg\StatusPageBundle\Status\StatusStack;
use Predis\Client;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class WatchDogProcessor
{
    /**
     * @var StatusStack
     */
    protected $statusStack;

    /**
     * @var array
     */
    protected $watchDogs;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var ExpressionLanguage
     */
    protected $expresionLanguage;

    /**
     * WatchDogProcessor constructor.
     *
     * @param StatusStack $statusStack
     * @param array       $watchDogs
     * @param Client      $redis
     */
    public function __construct(StatusStack $statusStack, array $watchDogs, Client $redis)
    {
        $this->statusStack = $statusStack;
        $this->watchDogs = $watchDogs;
        $this->redis = $redis;
        $this->expresionLanguage = new ExpressionLanguage();
    }

    /**
     * Process watchdogs
     */
    public function process()
    {
        foreach ($this->watchDogs as $watchDogId => $watchDogConfig) {
            $condition = $watchDogConfig['condition'];
            $expire = $watchDogConfig['expire'];
            $watchDogKey = 'watchdogs:'.$watchDogId;

            $values = [
                'metrics' => $this->statusStack->getCurrentStatuses(),
            ];

            if ($this->expresionLanguage->evaluate($condition, $values)) {
                $this->redis->incr($watchDogKey);

                if ($expire) {
                    $expirationInSeconds = is_numeric($expire) ? (int) $expire : strtotime($expire , time()) - time();
                    $this->redis->expire($watchDogKey, $expirationInSeconds);
                }
            }
        }
    }
}