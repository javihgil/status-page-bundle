<?php

namespace Jhg\StatusPageBundle\Event;

class ExceptionEvent implements StatusEventInterface
{
    public function getKey()
    {
        return 'exceptions';
    }

    public function getIncrement()
    {
        return 1;
    }
}