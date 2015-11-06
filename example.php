<?php

require_once ('SipuniApi.class.php');
use sipuni\SipuniApi;

define('SIPUNI_API_KEY', 'secret_key');
define('SIPUNI_CUSTOMER_ID', '3');

function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);

    $result = $api->put('/api/calltracking/allocate_static/', array(
        'customer'=>SIPUNI_CUSTOMER_ID,
        'purpose'=>'Client 23',
        'forward_to'=>'+74991231212'
    ));

    print $result;
}

main();