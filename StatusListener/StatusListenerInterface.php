<?php

namespace Jhg\StatusPageBundle\StatusListener;

/**
 * Interface StatusListenerInterface
 */
interface StatusListenerInterface
{
    /**
     * @param string $key
     */
    public function setEventKey($key);

    /**
     * @param string $period
     */
    public function setEventPeriod($period);

    /**
     * @param string $expire
     */
    public function setEventExpire($expire);

    /**
     * @param string|null $condition
     */
    public function setCondition($condition = null);
}