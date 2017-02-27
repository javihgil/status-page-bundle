<?php

namespace Jhg\StatusPageBundle\Controller;

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

        while ($date->format('YmdHi') <= $now->format('YmdHi')) {
            $metricKeys[] = $date->format('YmdHi').':'.$metric['id'];
            $date->modify('+1 minute');
        }

        $metricData = $this->get('snc_redis.status')->mget($metricKeys);

        $metricData = array_map(function ($v) { return (int) $v; }, $metricData);

        return $metricData;
    }
}