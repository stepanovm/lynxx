# Обработка исключений, ошибки 404 и пр.
____

Все непойманные исключения обрабатываются здесь: <br />
`\Lynxx\Exception\ExHandler::handle()`

По умолчанию записывается лог и выводится ошибка:

```php
$logger->error($ex->getMessage(), ['throwable' => $ex]);
echo "Неизвестная ошибка...\n" . $ex->getMessage();
echo Lynxx::debugPrint($ex);
die();
```

## Собственная обработка

Можно написать собственный код для обработки исключений.
Для этого нужно добавить новую зависимость в `app/config/dependencies.php`. 

Ключом должен быть имя класса-исключения, значением - функция, принимающая 2 аргумента: `ContainerInterface` и `Throwable`

В этом ниже добавляется зависимость для исключений `ContainerException`. Записывается ошибка, после чего создается экземпляр контроллера `SiteErrorController`и запускается его метод `run()`.

```php
// app/config/dependencies.php
'exceptionDependencies' => [
    // ...
    ContainerException::class => function (ContainerInterface $container, Throwable $ex) {
        $logger = $container->get(LoggerInterface::class);
        $logger->error($ex->getMessage(), ['throwable' => $ex]);
        ($container->get(SiteErrorController::class))->run($ex);
    }
    // ...
],

```

А в этом для исключений класса `PageException` просто выводится сообщение 'Рысь' при вызове

```php
// app/config/dependencies.php
'exceptionDependencies' => [
    // ...
    PageException::class => function (ContainerInterface $container, Throwable $ex) {
        $logger = $container->get(LoggerInterface::class);
        $logger->error($ex->getMessage(), ['throwable' => $ex]);
        ($container->get(SiteErrorController::class))->run($ex);
    },
    // ...
],

```