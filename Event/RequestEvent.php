<?php

namespace Jhg\StatusPageBundle\Event;

class RequestEvent implements StatusEventInterface
{
    public function getKey()
    {
        return 'request';
    }

    public function getIncrement()
    {
        return 1;
    }
}