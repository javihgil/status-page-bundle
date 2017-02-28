<?php

namespace Jhg\StatusPageBundle\StatusListener;

use Jhg\StatusPageBundle\Status\StatusStack;
use Jhg\StatusPageBundle\Status\StatusStackAwareInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

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
     * @throws Exception\InvalidConditionException
     */
    protected function evalCondition(array $context)
    {
        if (!$this->condition) {
            return true;
        }

        if (!$this->expresionLanguage) {
            $this->expresionLanguage = new ExpressionLanguage();
        }

        try {
            return $this->expresionLanguage->evaluate($this->condition, $context);
        } catch (SyntaxError $e) {
            throw new Exception\InvalidConditionException(sprintf('Invalid condition syntax for %s status page metric.', $this->eventKey), 0, $e);
        }
    }
}