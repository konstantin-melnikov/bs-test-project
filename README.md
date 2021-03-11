# Тестовое задание

## Часть 1
Используя открытые методы (`XML_daily` и `XML_dynamic`) Центробанка РФ (http://www.cbr.ru/development/SXML/) создать и заполнить Базу Данных.
БД заполняем данными минимум за 30 дней начиная с текущего дня.
В БД должна быть таблица currency c обязательными колонками:

* `currencyID` - идентификатор валюты, который возвращает метод (пример: R01010)
* `numCode` -  числовой код валюты (пример: 036)
* `сharCode` - буквенный код валюты (пример: AUD)
* `name` - имя валюты (пример: Австралийский доллар)
* `value` - значение курса (пример: 43,9538)
* `date` - дата публикации курса (может быть в UNIX-формате или ISO 8601)

## Часть 2
2.1.  Реализовать REST API метод (использовать фреймворк Laravel), который вернет курс(ы) валюты для переданного currencyID за указанный период date (from&to) используя данные из таблицы currency. Параметры передаем методом GET. 

## Часть 3

Реализовать 2 веб страницы:

1. Страница авторизации. Авторизация по логину и паролю. Учетные данные могут быть статичными.

2. Главная страница (доступна только после авторизации). На странице размещается таблица со списком валют и данными по этим валютам за указанную в поле/селекторе дату.

Оформление страниц не имеет значения, но любая попытка стилизации (в том числе с использованием фреймворков) будет плюсом для соискателя.

## My TODO list
* Validate response in homeController@index.
* Move all static content to i18n.
* Refactoring blade templates - move some block to component, etc.
* Refactoring works with date fields.
* Create job for update currency every day.