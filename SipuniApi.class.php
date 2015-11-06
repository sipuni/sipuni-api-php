<?php
/**
 * Sipuni API helper
 */

namespace sipuni;


class SipuniApi {

    private $apiKey;
    private $apiHost;

    function __construct($apiKey, $apiHost='api.sipuni.com'){
        $this->apiKey = $apiKey;
        $this->apiHost = $apiHost;
    }

    public function get($apiMethodPath, array $args){
        return array();
    }

    public function put($apiMethodPath, array $args){
        return array();
    }

    public function post($apiMethodPath, array $args){
        return array();
    }

    protected function getMethodUrl(){
        return array();
    }

    protected function getHeaders(){
        return array();
    }

}