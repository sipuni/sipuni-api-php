<?php

/**
 * Для каждого выделенного номера можно получить ссылку на страницу статистики.
 * Эта страница будет доступна только, тем у кого есть ссылка.
 * Страница содержит таблицу со звонками на номер.
 */

require_once ('../config/config.php');
require_once ('../lib/httpful.phar');
require_once ('../lib/SipuniApi.class.php');
use sipuni\SipuniApi;


function main(){

    $api = new SipuniApi(SIPUNI_API_KEY);

    try{
        $url = $api->getStatisticsUrl('74996476965');
        print("Statistics url: {$url}\n");

    }catch (\Exception $e){
        print $e;
    }
}

main();