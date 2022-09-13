# Регистрация и авторизация
____

Есть встроенный класс авторизации `Lynxx\Auth\Auth`
Он доступен через статический метод базового класса:

```php
\Lynxx\Lynxx::Auth();
```

Экземпляр класса `Lynxx\Auth\Auth` создается через контейнер.
Для успешного создания необходимо:

1. Создать 2 класса и реализовать их интерфейсы:

```php
// 1. Класс связи Lynxx\Auth\Auth с бд, должен быть наследован от \Lynxx\Auth\UserDbManagerInterface
class UserDbManager implements \Lynxx\Auth\UserDbManagerInterface

// 2. Собственно класс пользователя, должен наследоваться от \Lynxx\Auth\UserInterface
class User extends DomainObject implements \Lynxx\Auth\UserInterface
```

2. Прокинуть новый класс в зависимости, чтобы фреймворк знал, что дергать при обращении к UserDbManagerInterface:

```php
// app/config/dependencies.php
return [
    // ...
    UserDbManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(\app\Service\Auth\UserDbManager::class);
    },
    // ...
];
```

## Пример использования

На этом, все, можно пользоваться классом. Например, при авторизации через ajax контроллер может быть таким:

```php
public function auth_by_pass(): JsonResponse
{
    try {
        $requestData = $this->request->getParsedBody();

        if(Lynxx::Auth()->authByPassword($requestData['login'], $requestData['pass'])) {
            return new JsonResponse([]);
        }
        throw new \Exception(Lynxx::Auth()->getLastError());

    } catch (\Throwable $ex) {
        return new JsonResponse(['error' => $ex->getMessage()]);
    }
}
```