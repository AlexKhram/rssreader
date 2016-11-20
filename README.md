# RSS reader
## Описание
Приложение использует базу данных MySql. Фреймворк Silex. Doctrine Dbal для доступа к БД. Шаблонизатор Twig.
Разрабатывалось и тестировалось на Windows 10, на готовой сборке AMP от OpenServer.
## Порядок установки
* Клонировать репозиторий ```git clone https://github.com/AlexKhram/rssreader.git``` или скачать zip файлом и разархивировать.
* Зайти в папку проекта и выполнить команду ```composer install``` (для получения идентичных версий пакетов согласно composer.lock) или выполнить ```composer update```
* В phpMyAdmin (или аналоге) импортировать файл *tables.sql* для создание БД и таблиц с необходимыми связями.
* В файле */app/config.php* указать настройки доступа к БД (хост, логин, пароль)
* Указать Apache (или другому серверу) корневую папку проекта. (файл .htaccess со входом на index.php уже присутсвует впроекте)
* В браузере перейти по адресу *http://localhost/*
* Для обновления фидов добавить в планировщик заданий посещение эндпоинта http://localhost/update каждые 5 минут, например ```cron */5 * * * * /usr/bin/wget -O - -q -t 1 http://localhost/update```

### Спасибо за внимание
![alt tag](https://raw.githubusercontent.com/AlexKhram/rssreader/master/printscreen.jpg)
