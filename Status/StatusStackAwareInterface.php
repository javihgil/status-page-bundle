<?php

namespace Jhg\StatusPageBundle\Status;

interface StatusStackAwareInterface
{
    /**
     * @param StatusStack $statusStack
     */
    public function setStatusStack(StatusStack $statusStack);
}