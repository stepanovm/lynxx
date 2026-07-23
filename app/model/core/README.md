# Шаблон DataMapper 
___

<p>Содержание:</p>

1. [Создание новой сущности](#create_entity)
2. [Структура массива с описанием полей бд](#prop1)
3. [Общая структура](#prop2)
4. [Обычные поля](#prop3)
5. [Поля с указанием типа данных](#prop4)
6. [Отношение 'many-to-one'](#prop5)
7. [Отношение 'one-to-many'](#prop6)
8. [Предварительная загрузка](#prop7)

___

## Создание новой сущности <a name="create_entity"></a>

### Автоматическое создание

<p>Нужно создать классы (на примере сущности `User`)</p>
<p>Проще всего ипользовать консольную команду:</p>

```
php bin/app create:entity [имя_сущности]
```

Все, что останется сделать, это прописать имя таблицы в созданном UserMapper:
```php
public function getTable(): string
{
    return 'user';
}
```

и там же (в UserMapper) заполнить массив, который описывает поля базы данных:
```php
public function getProperties(): array
{
    return [
        'columns' => [
            'id' => ['field_name' => 'id',],
            'login' => ['field_name' => 'login',],
            'password' => ['field_name' => 'password',],
        ],
    ];
}
```
подробнее про описание полей ниже.

### Создание новой сущности вручную

<p>Придется создать 4 класса, как в примере ниже</p>

```php
// сущность
// обычный класс. При чтении из бд объект создается минуя конструктор. 
// сеттеры также не обязательно.
class User extends app\model\core\DomainObject

// репозиторий, класс для обеспечения связи "объект - база данных"
class UserMapper extends \app\model\core\Mapper

// Класс - коллекция объектов
class UserCollection extends app\model\core\Collection

// При необходимости можно создать класс для ленивой загрузки. 
// Он наследуется от предыдущего класса.
class UserDefferedCollection extends UserCollection
```

Также в `\app\config\mappers_map.php` нужно создать зависимость сущность-мэппер.
```php
// \app\config\mappers_map.php
<?php
return [
	\app\model\Entity\User\User::class => \app\model\Entity\User\mapper\UserMapper::class,
];
```


## Структура массива с описанием полей бд <a name="prop1"></a>

### Общая структура <a name="prop2"></a>
<p>Возьмем таблицу "user" и разберем на ее примере все возможные случаи.</p>
<p>Сначала общий вид:</p>

```php
return [
    'columns' => [
        'id' => ['field_name' => 'id',],
        'login' => ['field_name' => 'login',],
        'password' => ['field_name' => 'password',],
        'regDate' => ['field_name' => 'reg_date', 'type' => 'dateTime'],
        'userStatus' => [
            'field_name' => 'status_id',
            'targetEntity' => UserStatus::class
        ],
        'person' => [
            'field_name' => 'person_id',
            'targetEntity' => Person::class
        ],
    ],
    'relations' => [
        'userSessions' => [
            'targetEntity' => UserSession::class,
            'byColumn' => 'user_id',
            'mapperMethod' => 'findManyBy',
        ],
    ],
];
```

### Обычные поля <a name="prop3"></a>
Описание всех полей - здесь. Обычные поля записываются просто:
```php
'id' => ['field_name' => 'id',],
'name' => ['field_name' => 'name',],
'date' => ['field_name' => 'date',],
```
<p>Здесь ключ массива должен совпадать с именем поля в классе соответствующей сущности. 
А в значение `field_name` - имя поля в базе данных 
</p>

### Поля с указанием типа данных <a name="prop4"></a>
<p>Если неообходимо, чтобы значение из поля бд не просто попадало в объект "как есть", а как-то преобразовывалось, можно указать полю тип данных:</p>
<p>В этом примере такой тип указан полю reg_date. Допустим нужно, чтобы в объекте User дата представляла собой объект <em>DateTimeImmutable</em>. </p>

```php
'regDate' => ['field_name' => 'reg_date', 'type' => 'dateTime'],
```

Нужно создать свой класс - тип данных здесь, наследуемый от `\app\model\core\dbTypeInterface` например, так:


```php
// app/config/dbTypes/dateTime.php

namespace app\config\dbTypes;

class dateTime implements \app\model\core\dbTypeInterface
{
    public function toPhpValue($value)
    {
        return new \DateTimeImmutable($value);
    }
    
    public function toDataBaseValue($value)
    {
        if ($value instanceof \DateTimeImmutable) {
            return $value->format('Y-m-d h-i-s');
        }
        throw new \DomainException('cannot convert, value is not instance of \DateTimeImmutable');
    }
}

```

### Отношение 'many-to-one' <a name="prop5"></a>
<p>Если значение поля - ID из другой таблицы, то поле описывается как связь с другой сущностью. Другими словами, таким образом описывается связь многие к одному.</p>
<p>В данном случае предполагается, что пользователь может иметь только один статус. При этом одинаковый статус может быть у многих пользователей.</p>

```php
'userStatus' => [
    'field_name' => 'status_id',
    'targetEntity' => UserStatus::class
],
```

<p>
Здесь добавляется ключ `targetEntity`, в значение которого нужно положить имя класса соотв. сущности.
</p>

### Отношение 'one-to-many' <a name="prop6"></a>

<p>Это такое отношение, о котором данная сущность сама по себе не знает. В данном случае это сессии пользователей.</p>
<p>У пользователя может быть много сессий, а может не быть ни одной. Чтобы иметь возможность получить все сессии пользователя, ножно создать отношение:</p>


```php
'relations' => [
    'userSessions' => [
        'targetEntity' => UserSession::class,   // класс - связываемая сущность
        'byColumn' => 'user_id',    // имя поля в бд, по которому осуществляется связь                
        'mapperMethod' => 'findManyBy',     // метод, который используется для получения коллекции объектов связываемой сущности
    ],
],
```

<p>Нужно отметить, что данная связь со стороны сущности "сессия" является связью "many-to-one", и она должна быть там описана.</p>
<p>А вот со стороны пользователя описывать эту связь необязательно.</p>

## Предварительная загрузка <a name="prop7"></a>

<p>По умолчанию при загрузке объекта все связи подгружаются по отдельности. То есть для каждой связи создается запрос к бд, чтобы подтянуть нужные данные.</p>
<p>Если нужно этого избежать, можно подгрузить нужные таблицы, используя <b>JOIN</b>-запрос.</p>

<p>В примере ниже мы получим данные для всех связей сразу, за один запрос</p>

```php
// app/model/Entity/User/mapper/UserMapper.php
public function findAll()
{
    // 1. С помощью построителя запросов формируем запрос:
    $queryBuilder = new QueryBuilder();
    $query = $queryBuilder
        ->select()
        ->from(User::getMapper(), 'u')  // алиас сами придумываем
        ->leftjoin(Person::getMapper(), 'p', 'u')   // здесь второй параметр - алиас, третий - алиас таблицы, с которой есть связь
        ->join(UserStatus::getMapper(), 'st', 'u')
        ->leftjoin(UserSession::getMapper(), 'se', 'u')
        ->create();
    // также можно дописать where, orderby


    // 2. Выполняем запрос и получаем просто одномерный массив с "грязными" данными  в строку:
    $stmt = self::$PDO->prepare($query);
    $stmt->execute();
    $queryResults = $stmt->fetchAll();

    // 3. Теперь грязные данные нужно переложить в многомерный массив
    // Где данные каждой присоединенной таблицы будут лежать в подмассиве с соответствующим ключом
    $data = [];
    $sqlHelper = new SqlResultsHelper();
    foreach ($queryResults as $queryResult) {

        if (!isset($data[$queryResult['u_id']])) {
            // В этом блоке записываем в конечный массив основные данные и связи, которые в "columns"
            // То есть связи многие-к-одному. Эти данные нужно добавить один раз, дальше они могут повторяться
            // если присоединены другие таблицы
            $data[$queryResult['u_id']] = $sqlHelper->getEntityValues(User::getMapper(), $queryResult, 'u');
            $data[$queryResult['u_id']]['person'] = $sqlHelper->getEntityValues(Person::getMapper(), $queryResult, 'p');
            $data[$queryResult['u_id']]['userStatus'] = $sqlHelper->getEntityValues(UserStatus::getMapper(), $queryResult, 'st');
        }

        // А в этом блоке мы записываем в конечный массив связи, определенные в "relations"
        // В данном случае это сессии пользователей. На каждого их может быть много.
        $sqlHelper->parseCollectionRow(UserSession::getMapper(), $queryResult, 'se', $data[$queryResult['u_id']], 'userSessions');

    }
    
    // Коллекция принимает массив, где ключи по порядку, поэтому полученный массив нужно переложить через `array_values`: 
    return new UserCollection(array_values($data), $this);
}
```

<p>
Тогда mapper разберется, что данные уже есть и не будет выполнять второй запрос.
</p>
