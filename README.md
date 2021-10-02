
#### Установка пакетов, без скирптов для тестов, возможно позже будут тесты, именно для шаблона
```shell
symfony composer install --no-scripts
```



### Справочная информация.

#### Выполнять в контейнере #microservice_name#

```shell
php bin/console doctrine:database:create
```

```shell
docker exec -it "$microservice_name" bash
```

Создание миграций
```shell
php bin/console doctrine:migrations:diff
```

Применение миграций
```shell
php bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction
```

Создание клиента в базе
```shell
php bin/console app:client:create 'test client' 123456789 http://test.ru
```


Подписчик на очередь amqp_test_rabbit
```shell
php bin/console messenger:consume amqp_test_rabbit
```

Подписчик на очередь amqp_test_rabbit_custom
```shell
php bin/console messenger:consume amqp_test_rabbit_custom
```
