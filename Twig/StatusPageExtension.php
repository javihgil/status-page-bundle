<?php

namespace Jhg\StatusPageBundle\Twig;

class StatusPageExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('status_average', [$this, 'statusAverage']),
            new \Twig_SimpleFilter('status_sum', [$this, 'dataSum']),
        ];
    }

    /**
     * @param array $data
     *
     * @return float|int
     */
    public function statusAverage(array $data)
    {
        return array_sum($data) / (sizeof($data) ? : 1);
    }

    /**
     * @param array $data
     *
     * @return float|int
     */
    public function dataSum(array $data)
    {
        return array_sum($data);
    }
}