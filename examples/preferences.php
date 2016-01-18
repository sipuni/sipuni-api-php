<?php

require_once ('../config/config.php');
require_once ('../lib/httpful.phar');
require_once ('../lib/SipuniApi.class.php');
use sipuni\SipuniApi;


function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);
    try{

        print("\nSettings preferences\n");
        $r=$api->setPreferences(array('calltracking_webhook'=>'http://abc.com/webhook.php'));
        print_r($r);

        print("\nGetting preferences\n");
        $r=$api->getPreferences();
        print_r($r);

    }catch(\Exception $e){
        print $e;
    }
}

main();