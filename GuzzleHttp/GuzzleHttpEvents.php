<?php

namespace Jhg\StatusPageBundle\GuzzleHttp;

class GuzzleHttpEvents
{
    /**
     * The REQUEST event occurs when a request is launched
     */
    const REQUEST = 'guzzlehttp.request';

    /**
     * The RESPONSE event occurs whean a request is success and a response is provided
     */
    const RESPONSE = 'guzzlehttp.response';
}