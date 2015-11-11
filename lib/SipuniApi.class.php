<?php
/**
 * Sipuni API wrapper.
 * @version 0.9
 * @author Aliaksei Harabchuk <ah@sipuni.com>
 */

namespace sipuni;

/**
 * Wraps Sipuni API calls
 * @package sipuni
 */
class SipuniApi {

    private $apiKey;
    private $apiHost;
    private $apiVersion = 'v1';

    function __construct($apiKey, $apiHost='api.sipuni.com'){
        $this->apiKey = $apiKey;
        $this->apiHost = $apiHost;
    }

    /**
     * Builds an absolute URL for making a request to Sipuni API
     * @param string $path API method URI path
     * @return string string with an absoulte url
     */
    public function getMethodUrl($path){
        return "https://{$this->apiHost}/{$this->apiVersion}{$path}?format=json";
    }


    /**
     * Gets a list of available number ranges
     * @return array with ranges of null if failed to get ranges.
     * Array contains objects with id and title properties.
     */
    public function ranges(){
        $apiMethodUrl = $this->getMethodUrl('/ranges/');
        $response = \Httpful\Request::get($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        if(property_exists($response, 'body')){
            return $response->body;
        }
        return null;
    }

    /**
     * Finds a single range containing the search substring in the title
     * @param string $nameSubstring a search substring, usually an area code, e.g. 495 for Moscow
     * @return string|null range object or null. A range object contains id and title properties.
     */
    public function findRange($nameSubstring){
        $ranges = $this->ranges();
        if(!$ranges){
            return null;
        }
        for($i=0; $i < count($ranges); $i++){
            if( strpos($ranges[$i]->title, $nameSubstring) ){
                return $ranges[$i];
            }
        }
        return null;
    }

    /**
     * Allocates a new static number.
     * @param integer $rangeId identifier of range
     * @param string $forwardTo a phone number to forward calls to
     * @param string $description a comment with purpose of the number
     * @return object|null object with a number property containing a newly allocated number or null if failed to allocate.
     */
    public function allocateStatic($rangeId, $forwardTo, $description){

        $args = array(
            'range'=>$rangeId,
            'forward_to'=>$forwardTo,
            'description'=>$description
        );

        $apiMethodUrl = $this->getMethodUrl('/calltracking/static/number/');

        $response = \Httpful\Request::put($apiMethodUrl)
            ->sendsJson()
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->body(json_encode($args))
            ->send();

        if($this->isSuccess($response)){
            return $response->body;
        }else{
            return null;
        }
    }

    /**
     * Release a static number.
     * @param string $number a number to release
     * @return object|null
     */
    public function releaseStatic($number){

        $apiMethodUrl = $this->getMethodUrl("/calltracking/static/number/{$number}/");

        $response = \Httpful\Request::delete($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        if($this->isSuccess($response)){
            return $response->body;
        }else{
            return null;
        }
    }

    protected function getAuthHeader(){
        return array('Authorization'=>"Token {$this->apiKey}");
    }

    protected function isSuccess($response){
        print_r($response->body);
        return $response && $response->body && property_exists($response->body, 'success') && $response->body->success;
    }
}