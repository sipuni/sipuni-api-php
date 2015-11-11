<?php

require_once ('../config/config.php');
require_once ('../lib/httpful.phar');
require_once ('../lib/SipuniApi.class.php');
use sipuni\SipuniApi;


function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);

    // Получить диапазон номеров "Москва 499"
    $range = $api->findRange('499');
    print("Found range:\n");
    print_r($range);

    // Выделить статический номер и создать для него перенаправление на номер +749912312312
    $result = $api->allocateStatic($range->id, '+749912312312', 'For newspapers');
    print("Result of allocation:\n");
    print_r($result);
}

main();