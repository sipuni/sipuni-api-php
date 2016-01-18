# Примеры использования Sipuni API на PHP

### Перед запуском

В личном кабинете Sipuni в разделе Интеграция скопируйте ключ.

В файле config/config.php впишите ваш ключ.

Теперь можно запускать примеры из папки examples.

```
$php allocate_static
```

### Работа с классом-оберткой для Sipuni API

Для работы, помимо файла SipuniApi.class.php необходимо подключить архив httpful.phar.
Это библиотека для работы с REST API. Ее можно подключать также при помощи composer.
Подробнее читайте на сайте библиотеки [phphttpclient.com](http://phphttpclient.com).

 ```
 require_once ('../lib/httpful.phar');
 require_once ('../lib/SipuniApi.class.php');
 use sipuni\SipuniApi;
 ```

 В конструктор класса подайте API ключ, как его получить читайте в разделе Перед запуском.
 ```
 $api = new SipuniApi($key);
 ```

### Статический коллтрекинг

Методы статического коллтрекинга позволяют выделить номер для использования в рекламе и
получать статистику по этому номеру.

При выделении номера требуется задать диапазон номеров, в котором будет выделен номер.
 Например, найдем диапазон номеров с кодом 499.

 ```
 $range = $api->findRange('499');
 ```
 Для получения полного списка диапазонов воспользуйтесь методом `$api->ranges()`.
  (На данный момент есть только номера с кодом 499).

 Результат:
 ```
 stdClass Object
 (
    [id] => 1
    [title] => Москва 499
 )
 ```

 Теперь можно выделить статический номер и сделать переадресацию на номер 74997778899.
 В качестве комментария - 'For newspapers' предположим, что номер будет использоваться для рекламы в газетах.
 ```
 $number = $api->allocateStatic($range->id, '74997778899', 'For newspapers');
 ```
 Результат, выделен номер:
 ```
 '74995550000'
 ```

 Когда номер больше не нужен, его можно освободить.
 ```
 $success = $api->releaseStatic('74995550000');
 ```
 Результат:
  ```
  true
  ```

 Все методы создают исключение \\Exception, в случае ошибок.


### Получение статистики по отслеженным звонкам

 Через API можно получить статистику по отслеженным в коллтрекинге номерам.
 Для этого используется метод getStatisticsHits.
 Первый параметр cid (campaign id) это идентификатор кампании коллтрекинга. Его можно взять
 из JavaScript кода коллтркинга, (переменная ct_cid=NNN)
 Два других параметра - дата с и по которую выдать статистику. Задается в формате unix timestamp.
 ```
 $hits = $api->getStatisticsHits('140', 1441111200, 1441411200);
 ```
 В результате выдается массив со статистикой.


 ```
 Array
 (
     [0] => stdClass Object
         (
             [status] => 3
             [customer] => 140
             [ua_client_id] =>
             [hit_price] => 5.00
             [occurred] => 2015-11-25T20:02:15.448142Z
             [number] => 74996477486
             [visitor_source] => 'https://yandex.ru/yandsearch?clid=2224314&text=sipuni...'
             [visitor_target] => 'http://sipuni.ru/?utm_source=market....'
             [source_id] => 74996479797
             [id] => 1465
         ),
     [1] => stdClass Object
         (
             [status] => 3
             [customer] => 140
             [ua_client_id] =>
             [hit_price] => 5.00
             [occurred] => 2015-11-25T20:02:15.448142Z
             [number] => 74996477486
             [visitor_source] => 'https://yandex.ru/yandsearch?clid=2224314&text=sipuni...'
             [visitor_target] => 'http://sipuni.ru/?utm_source=market....'
             [source_id] => 74996479797
             [id] => 1465
         )

 )
 ```

 * ua_client_id - идентификатор client_id Universal Analtics
 * number - номер телефона на который был звонок
 * source_id - номер абонента
 * visitor_source - сайт-источник перехода посетителя
 * visitor_target - страница, на которую перешел посетитель в начале сессии


### Задать вебхук для обработки событий коллтрекинга

Вы можете задать ULR обратного вызова (Webhook) который будет вызван каждый раз,
когда отслеживается входящий звонок.
```
$api->setPreferences(array('calltracking_webhook'=>'http://abc.com/webhook.php'))
```

В результате, адрес http://abc.com/webhook.php будет вызван при отслеженном звонке,
и он получит следующую информацию в json формате
```
{
      ua_client_id: "123141241.12124124",
      number: "74996477486",
      visitor_source: "http://yandex.ru/yandsearch?clid=2224314&text=sipuni…"
      visitor_target: "http://sipuni.ru/?utm_source=market....",
      source_id: "74996479797"
}
```
 * ua_client_id - идентификатор посетителя Universal (Google) Analytics
 * number - номер, на который произошел звонок,
 * source_id - номер абонента,
 * visitor_source - сайт, с которого пришел посетитель на ваш сайт в первый раз,
 * visitor_target - страница, на которую перешел посетитель на вашем сайте в первый раз

Для удаления вебхука, вызовите setPreferences с пустым адресом вебхука:
```
$api->setPreferences(array('calltracking_webhook'=>''))
```
