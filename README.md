# Модуль для управления пользователями для 1С Битрикс: управление сайтом
## Установка :
Скопировать  в папку с модулями:
`local/modules` и установить через админку

Для запуска новой системы роутинга нужно перенаправить обработку 404 ошибок на файл routing_index.php в файле .htaccess:

```
#RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
#RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]

RewriteCond %{REQUEST_FILENAME} !/bitrix/routing_index.php$
RewriteRule ^(.*)$ /bitrix/routing_index.php [L]
```
## Использвание:
#### Получение пользователя по id

<details>
 <summary><code>GET</code>  <code><b>/</b>users<b>/</b>{id}</code></summary>

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | int    |     id of user    |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `application/json`        | json object  |
> | `400`         | `application/json`        | error message  |
</details>

#### Добавление нового пользователя

<details>
 <summary><code>POST</code> <code><b>/</b>users<b>/</b>add</code></summary>

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `login` |  required | string    |     login   |
> | `email` |  required | string    |     email    |
> | `password` |  required | string    |     password   |
> | `name` |            | string    |     name    |
> | `last_name` |       | string    |     last name     |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `application/json`        | id of created user                                |
> | `400`         | `application/json`        | error message                               |

</details>

#### Редактирование пользователя

<details>
 <summary><code>PUT</code> <code><b>/</b>users<b>/</b>update</code>
 </summary>

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | int    |     id of user   |
> | `login` |       | string   |     login   |
> | `email` |        | string    |     email    |
> | `password` |     | string    |     password   |
> | `name` |            | string    |     name    |
> | `last_name` |       | string    |     last name     |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `application/json`        | success message                              |
> | `400`         | `application/json`        | error message                               |

</details>

#### Удаление пользователя

<details>
 <summary><code>DELETE</code> <code><b>/</b>users<b>/</b>delete<b>/</b>{id}</code>
 </summary>

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | int    |     id of user   |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `application/json`        | success message                              |
> | `400`         | `application/json`        | error message                               |

</details>

#### Авторизация пользователя

<details>
 <summary><code>POST</code> <code><b>/</b>users<b>/</b>authorize</code>
 </summary>

##### Parameters

> | name              |  type     | data type      | description                         |
> |-------------------|-----------|----------------|-------------------------------------|
> | `id` |  required | int    |     id of user   |
> | `group` |  required | int    |     group to add the user to   |

##### Responses

> | http code     | content-type                      | response                                                            |
> |---------------|-----------------------------------|---------------------------------------------------------------------|
> | `200`         | `application/json`        | success message                              |
> | `400`         | `application/json`        | error message                               |

</details>
