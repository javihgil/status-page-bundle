<?php

namespace Jhg\StatusPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatusPageController extends Controller
{
    public function statusAction()
    {
        $date = new \DateTime('-60 minutes');
        $now = new \DateTime('now');

        $requestKeys = [];
        $responseTimeKeys = [];
        $exceptionKeys = [];

        while ($date->format('YmdHi') <= $now->format('YmdHi')) {
            $requestKeys[] = $date->format('YmdHi').':request';
            $responseTimeKeys[] = $date->format('YmdHi').':response-time';
            $exceptionKeys[] = $date->format('YmdHi').':exceptions';
            $date->modify('+1 minute');
        }

        $requestKeysData = $this->get('snc_redis.status')->mget($requestKeys);
        $responseTimeKeysData = $this->get('snc_redis.status')->mget($responseTimeKeys);
        $exceptionData = $this->get('snc_redis.status')->mget($exceptionKeys);

        $requestKeysData = array_map(function ($v) { return (int) $v; }, $requestKeysData);
        $responseTimeKeysData = array_map(function ($v) { return (int) $v; }, $responseTimeKeysData);
        $exceptionData = array_map(function ($v) { return (int) $v; }, $exceptionData);

        $viewData = [
            'requestKeysData' => $requestKeysData,
            'responseTimeKeysData' => $responseTimeKeysData,
            'exceptionData' => $exceptionData,
        ];

        return $this->render('JhgStatusPageBundle:StatusPage:status.html.twig', $viewData);
    }
}