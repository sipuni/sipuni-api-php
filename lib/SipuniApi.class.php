<?php
/**
 * Sipuni API helper
 */

namespace sipuni;

/*
 *
 */
class SipuniApi {

    private $apiKey;
    private $apiHost;
    private $apiVersion = 'v1';

    function __construct($apiKey, $apiHost='api.sipuni.com'){
        $this->apiKey = $apiKey;
        $this->apiHost = $apiHost;
    }

    /*
     *
     */
    public function getMethodUrl($path){
        return "https://{$this->apiHost}/{$this->apiVersion}{$path}?format=json";
    }

    /*
     *
     */
    public function ranges(){
        $apiMethodUrl = $this->getMethodUrl('/ranges/');
        $response = \Httpful\Request::get($apiMethodUrl)
            ->expectsJson()
            ->send();
        return $response;
    }

    /*
     *
     */
    public function findRange($nameSubstring){
        $ranges = $this->ranges();
        for($i=0; $i<count($ranges->body); $i++){
            if( strpos($ranges->body[$i]->title, $nameSubstring) ){
                return $ranges->body[$i];
            }
        }
        return null;
    }

    /*
     * Allocates a new static number.
     */
    public function allocateStatic($rangeId, $forwardTo){

        $args = array(
            'range'=>$rangeId,
            'forward_to'=>$forwardTo
        );

        $apiMethodUrl = $this->getMethodUrl('/calltracking/allocate_static/');

        $response = \Httpful\Request::put($apiMethodUrl)
            ->sendsJson()
            ->expectsJson()
            ->body(json_encode($args))
            ->send();

        return $response;
    }
}