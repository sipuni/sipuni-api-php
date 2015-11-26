<?php

/**
 * Получение статистики по отслеженным номероам.
 * Включает:
 *   ua_client_id: Universal Analytics client_id для связи с аналитикой Google
 *   source_id: номер абонента
 *   hit_price: стоимость отслеженного звонка
 */

require_once ('../config/config.php');
require_once ('../lib/httpful.phar');
require_once ('../lib/SipuniApi.class.php');
use sipuni\SipuniApi;


function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);

    try{
        // даты в формате unix timestamp
        $hits = $api->getStatisticsHits('140', 1441111200, 1441411200);
        print_r($hits);

    }catch (\Exception $e){
        print $e;
    }
}

main();