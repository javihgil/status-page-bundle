<?php

namespace Jhg\StatusPageBundle\Status;

/**
 * Interface StatusInterface
 */
interface StatusInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getPeriod();

    /**
     * @return string
     */
    public function getExpire();

    /**
     * @return int
     */
    public function getIncrement();
}