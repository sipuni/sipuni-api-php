<?php

require_once ('../config/config.php');
require_once ('../lib/httpful.phar');
require_once ('../lib/SipuniApi.class.php');
use sipuni\SipuniApi;


function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);

    try{
        // Получить диапазон номеров "Москва 499"
        $range = $api->findRange('499');
        print("Found range: {$range->title}\n");

        // Выделить статический номер и создать для него перенаправление на номер +749912312312
        $number = $api->allocateStatic($range->id, '+749912312312', 'For newspapers');
        print("Allocated number: {$number}\n");

    }catch (\Exception $e){
        print $e;
    }
}

main();