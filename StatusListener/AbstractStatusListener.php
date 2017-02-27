<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusStack;
use Jhg\StatusPageBundle\Status\StatusStackAwareInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class AbstractStatusListener
 */
abstract class AbstractStatusListener implements StatusStackAwareInterface, StatusListenerInterface
{
    /**
     * @var string
     */
    protected $eventKey;

    /**
     * @var string
     */
    protected $eventPeriod;

    /**
     * @var string
     */
    protected $eventExpire;

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var StatusStack
     */
    protected $statusStack;

    /**
     * @var ExpressionLanguage
     */
    protected $expresionLanguage;

    /**
     * @inheritdoc
     */
    public function setEventKey($key)
    {
        $this->eventKey = $key;
    }

    /**
     * @inheritdoc
     */
    public function setEventPeriod($period)
    {
        $this->eventPeriod = $period;
    }

    /**
     * @inheritdoc
     */
    public function setEventExpire($expire)
    {
        $this->eventExpire = $expire;
    }

    /**
     * @inheritdoc
     */
    public function setCondition($condition = null)
    {
        $this->condition = $condition;
    }

    /**
     * @inheritdoc
     */
    public function setStatusStack(StatusStack $statusStack)
    {
        $this->statusStack = $statusStack;
    }

    /**
     * @param array $context
     *
     * @return bool
     */
    protected function evalCondition(array $context)
    {
        if (!$this->condition) {
            return true;
        }

        if (!$this->expresionLanguage) {
            $this->expresionLanguage = new ExpressionLanguage();
        }

        return $this->expresionLanguage->evaluate($this->condition, $context);
    }
}