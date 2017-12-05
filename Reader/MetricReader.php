<?php

namespace Jhg\StatusPageBundle\Reader;

use Jhg\StatusPageBundle\Status\StatusStack;
use Predis\Client;

class MetricReader
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * MetricReader constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $since
     * @param array  $metric
     *
     * @return array
     */
    public function getMetricData($since, array $metric)
    {
        return $this->getData($metric['id'], $since, $metric['period']);
    }

    /**
     * @param string $id
     * @param string $since
     * @param string $period
     *
     * @return array
     */
    public function getData($id, $since, $period)
    {
        $date = new \DateTime($since);
        $now = new \DateTime('now');

        $metricKeys = [];

        $metricFormat = StatusStack::TIME_MARK_FORMATS[$period];

        switch ($period) {
            case 'second':
                $dateModify = '+1 second';
                break;
            case 'minute':
                $dateModify = '+1 minute';
                break;
            case 'hour':
                $dateModify = '+1 hour';
                break;
            case 'day':
                $dateModify = '+1 day';
                break;
            default:
                throw new \RuntimeException(sprintf('Invalid period %s for metric render', $period));
        }

        while ($date->format($metricFormat) <= $now->format($metricFormat)) {
            $metricKeys[] = $date->format($metricFormat).':'.$id;
            $date->modify($dateModify);
        }

        $metricData = $this->client->mget($metricKeys);

        $metricData = array_map(function ($v) { return (int) $v; }, $metricData);

        return $metricData;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getTotalData($id)
    {
        $metricData = $this->client->get("total:$id");

        return $metricData;
    }
}