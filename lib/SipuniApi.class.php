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
     * @throws \Exception
     * @return array with ranges.
     * Array contains objects with id and title properties.
     */
    public function ranges(){
        $apiMethodUrl = $this->getMethodUrl('/ranges/');
        $response = \Httpful\Request::get($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        $msg = 'Unable to get ranges';
        if(property_exists($response, 'body')){
            if(is_array($response->body)){
                return $response->body;
            }else{
                $msg = $response->raw_body;
            }
        }
        throw new \Exception($msg);
    }

    /**
     * Finds a single range containing the search substring in the title
     * @throws \Exception
     * @param string $nameSubstring a search substring, usually an area code, e.g. 495 for Moscow
     * @return string|null range object or null. A range object contains id and title properties.
     */
    public function findRange($nameSubstring){
        $ranges = $this->ranges();
        for($i=0; $i < count($ranges); $i++){
            if( strpos($ranges[$i]->title, $nameSubstring) ){
                return $ranges[$i];
            }
        }
        return null;
    }

    /**
     * Allocates a new static number.
     * @throws \Exception
     * @param integer $rangeId identifier of range
     * @param string $forwardTo a phone number to forward calls to
     * @param string $description a comment with purpose of the number
     * @return string a newly allocated number or null if failed to allocate.
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

        $msg = 'Unable to allocate';
        if(property_exists($response, 'body')){
            if($response->body->success){
                return $response->body->number;
            }else{
                $msg = $response->raw_body;
            }
        }
        throw new \Exception($msg);
    }

    /**
     * Release a static number.
     * @throws \Exception
     * @param string $number a number to release
     * @return object|null
     */
    public function releaseStatic($number){

        $apiMethodUrl = $this->getMethodUrl("/calltracking/static/number/{$number}/");

        $response = \Httpful\Request::delete($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        $msg = 'Unable to release';
        if(property_exists($response, 'body')){
            if(property_exists($response->body, 'success') && $response->body->success){
                return true;
            }else{
                $msg = $response->raw_body;
            }
        }
        throw new \Exception($msg);
    }

    /**
     * Gets a public url for the number statistics
     * @param $number
     * @throws \Exception
     */
    public function getStatisticsUrl($number){
        $apiMethodUrl = $this->getMethodUrl("/numbers/statistics/url/");
        $args = array('number'=>$number);

        $response = \Httpful\Request::put($apiMethodUrl)
            ->sendsJson()
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->body(json_encode($args))
            ->send();

        $msg = 'Unable to get url';
        if(property_exists($response, 'body')){
            if($response->body->success){
                return $response->body->url;
            }else{
                $msg = $response->raw_body;
            }
        }
        throw new \Exception($msg);
    }

    public function getStatisticsHits($campaignId, $dateFrom, $dateTo, $sourceId=null){
        $apiMethodUrl = $this->getMethodUrl("/calltracking/statistics/hits/");
        $apiMethodUrl = $this->appendUr($apiMethodUrl, 'cid', $campaignId);
        $apiMethodUrl= $this->appendUr($apiMethodUrl, 'date_from', $dateFrom);
        $apiMethodUrl = $this->appendUr($apiMethodUrl, 'date_to', $dateTo);
        $apiMethodUrl = $this->appendUr($apiMethodUrl, 'source_id', $sourceId);

        $response = \Httpful\Request::get($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        $msg = 'Unable to get statistics';
        if(property_exists($response, 'body')){
            if(is_array($response->body)){
                return $response->body;
            }else{
                $msg = $response->raw_body;
            }
        }
        throw new \Exception($msg);
    }

    /**
     * Sets preferences.
     * @param array $preferences
     * Example of $preferences
     * {
     *   'calltracking_webhook':'<url>'
     * }
     * @return True or exception
     * @throws \Exception
     */
    public function setPreferences(array $preferences){

        $apiMethodUrl = $this->getMethodUrl('/preferences/');

        $response = \Httpful\Request::put($apiMethodUrl)
            ->sendsJson()
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->body(json_encode($preferences))
            ->send();

        if(property_exists($response, 'body')){
            return $response->body;
        }else{
            throw new \Exception($response->raw_body);
        }
    }

    /**
     * Returns an array with current preferences
     * @return array with current preferences
     * @throws \Exception
     */
    public function getPreferences(){

        $apiMethodUrl = $this->getMethodUrl('/preferences/');

        $response = \Httpful\Request::get($apiMethodUrl)
            ->expectsJson()
            ->addHeaders($this->getAuthHeader())
            ->send();

        if(property_exists($response, 'body')){
            return $response->body;
        }else{
            throw new \Exception($response->raw_body);
        }
    }


    protected function getAuthHeader(){
        return array('Authorization'=>"Token {$this->apiKey}");
    }

    protected function isSuccess($response){
        print_r($response->body);
        return $response && $response->body && property_exists($response->body, 'success') && $response->body->success;
    }

    protected function appendUr($url, $key, $value) {
        $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
        $url = substr($url, 0, -1);
        if (strpos($url, '?') === false) {
            return ($url . '?' . $key . '=' . $value);
        } else {
            return ($url . '&' . $key . '=' . $value);
        }
    }

}