# gtree

Простой сайт для построения генеалогического дерева

## Разворачивание и обновление базы данных

1. Подготовить пустую базу данных и и изменить конфигурацию подключения в www/conf.d/config.php
Например использую консольный клиент (mysql):

```
> CREATE DATABASE `gtree` CHARACTER SET utf8 COLLATE utf8_general_ci;
> CREATE USER 'gtreeu'@'localhost' IDENTIFIED BY 'jET3E4W9vm';
> GRANT ALL PRIVILEGES ON gtree.* TO 'gtreeu'@'localhost' WITH GRANT OPTION;
> FLUSH PRIVILEGES;
```

2. Вызвать скрипт:

```
$ cd www
$ php update_db.php
```

## Первый вход в систему

Дефолтный логин пароль для админа  (`%WHERE_GTREE%/admin/`):

admin/admin

1. Требуется создать нового административного пользователя
2. Зайти под новым пользоватлем
3. Удалить пользователя admin
