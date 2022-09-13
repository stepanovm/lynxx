# Общие сведения
___

## Контроллер

### Получение данных из запроса

Если нужны какие-то особые данные из запроса (например, данные из POST 
или GET-параметры строки и пр.), они доступны в объекте ServerRequest

Этот объект можно подгрузить в класс через конструктор:

```php
class MyController extends AbstractController
{
    private ServerRequestInterface $request;

    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    // ...
}
```

Теперь в методах класса (actions) можно использовать этот объект, например:

```php
// Параметры из роутера: ('/auth/(?<action>(auth_by_pass)|(check_token))')
$this->request->getAttribute('action');
$this->request->getAttributes();

// параметры из запроса (/?foo=bar)
$this->request->getQueryParams();

// данные, переданные методом POST:
$this->request->getParsedBody();
```