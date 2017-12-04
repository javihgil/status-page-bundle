<?php

namespace Jhg\StatusPageBundle\Controller;

use Jhg\StatusPageBundle\Status\StatusStack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Response;

class StatusPageController extends Controller
{
    public function viewAction($view, $maxAge = null)
    {
        $viewConfig = $this->getViewConfig($view);
        $metricsConfig = $this->getParameter('jhg_status_page.metrics');

        $metrics = [];
        foreach ($viewConfig['metrics'] as $id => $metric) {
            $metrics[$id] = [
                'title' => $metric['title'],
                'average_by' => !empty($metric['average_by']) ? $metric['average_by'] : null,
                'data' => $this->getMetricData($metric['period'], $metricsConfig[$metric['metric_id']]),
                'type' => $metricsConfig[$metric['metric_id']]['type'],
            ];
        }

        foreach ($metrics as $id => $metric) {
            if ($metric['average_by']) {
                foreach ($metric['data'] as $k => $v) {
                    $metrics[$id]['data'][$k] = round($v / ($metrics[$metric['average_by']]['data'][$k] ? : 1));
                }
            }
//            if ($metric['percentage_by']) {
//                foreach ($metric['data'] as $k => $v) {
//                    $metrics[$id]['data'][$k] = round($v / ($metrics[$metric['average_by']]['data'][$k] ? : 1));
//                }
//            }
        }

        $viewData = [
            'metrics' => $metrics,
        ];

        $response = new Response();

        if ($maxAge) {
            $response->setMaxAge($maxAge);
            $response->setPublic();
        }

        return $this->render($viewConfig['template'], $viewData, $response);
    }

    /**
     * @param string $view
     *
     * @return array
     */
    protected function getViewConfig($view)
    {
        $viewsConfig = $this->getParameter('jhg_status_page.views');

        if (!isset($viewsConfig[$view])) {
            throw new InvalidConfigurationException(sprintf('Invalid view %s configured for status page', $view));
        }

        return $viewsConfig[$view];
    }

    /**
     * @param string $period
     * @param array  $metric
     *
     * @return array
     */
    protected function getMetricData($period, array $metric)
    {
        $date = new \DateTime($period);
        $now = new \DateTime('now');

        $metricKeys = [];

        $metricFormat = StatusStack::TIME_MARK_FORMATS[$metric['period']];

        switch ($metric['period']) {
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
                throw new \RuntimeException(sprintf('Invalid period %s for metric render', $metric['period']));
        }

        while ($date->format($metricFormat) <= $now->format($metricFormat)) {
            $metricKeys[] = $date->format($metricFormat).':'.$metric['id'];
            $date->modify($dateModify);
        }

        $metricData = $this->get($this->getParameter('jhg_status_page.predis_client_id'))->mget($metricKeys);

        $metricData = array_map(function ($v) { return (int) $v; }, $metricData);

        return $metricData;
    }
}