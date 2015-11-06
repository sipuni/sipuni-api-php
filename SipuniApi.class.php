<?php
/**
 * Sipuni API helper
 */

namespace sipuni;


class SipuniApi {

    private $apiKey;
    private $apiHost;
    private $apiVersion = 'v1';

    function __construct($apiKey, $apiHost='api.sipuni.com'){
        $this->apiKey = $apiKey;
        $this->apiHost = $apiHost;
    }

    public function getMethodUrl($path){
        return "https://{$this->apiHost}/{$this->apiVersion}{$path}?format=json";
    }

}