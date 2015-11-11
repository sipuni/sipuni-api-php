# Примеры использование Sipuni API на PHP

### Перед запуском

В личном кабинете Sipuni в разделе Интеграция скопируйте ключ,

В файле config/config.php впишите ваш ключ.

Теперь можно запускать примеры из папки examples.

```
$php allocate_static
```

### Работа с классом-оберткой для Sipuni API

Для работы необходимо подключить архив httpful.phar. Он содержит классы для работы с REST API.

 ```
 require_once ('../lib/httpful.phar');
 require_once ('../lib/SipuniApi.class.php');
 use sipuni\SipuniApi;
 ```

 В конструктор класса подайте API ключ, как его получить читайте в разделе Перед запуском.
 ```
 $api = new SipuniApi($key);
 ```

 Найдите нужный объект range. Например, чтобы найти диапазон для номеров 499
 ```
 $range = $api->findRange('499');
 ```

 Теперь можно выделить статический номер.
 ```
 $result = $api->allocateStatic($range->id, '+749912312312', 'For newspapers');
 ```



