<?php

require_once ('httpful.phar');
require_once ('SipuniApi.class.php');
use sipuni\SipuniApi;


define('SIPUNI_API_KEY', 'secret_key');
define('SIPUNI_CUSTOMER_ID', '3');


function allocate_number($purpose, $forward_to){
    $api = new SipuniApi(SIPUNI_API_KEY);

    $args = array(
        'customer'=>SIPUNI_CUSTOMER_ID,
        'purpose'=>$purpose,
        'forward_to'=>$forward_to
    );

    $apiMethodUrl = $api->getMethodUrl('/calltracking/allocate_static/');

    $response = \Httpful\Request::put($apiMethodUrl)
        ->sendsJson()
        ->expectsJson()
        ->body(json_encode($args))
        ->send();

    return $response;
}

function main(){
    $result = allocate_number('Client 23', '+74991231212' );
    if($result->code==200){
        if($result->body->success){
            print ("Allocated number ".$result->body->number);
        }else{
            print ("Error: ".$result->body->msg);
        }
    }
}

main();