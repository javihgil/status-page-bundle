<?php

namespace Jhg\StatusPageBundle\Event;

interface StatusEventInterface
{
    public function getKey();

    public function getIncrement();
}