Компонент Отложенные статьи (Отложенный контент) для  InstantCMS 1.10+

Описание
----------------------------------------

Компонент предназначен для публикации отложенных статей на системе InstantCMS 1.10.
Публикация статей осуществляется по крону.
Отложенный контент до публикации нигде не фигирует и не отображается, так как используюется своя таблица в базе.



Установка
----------------------------------------

1. Скопировать в корень сайта
2. Установить из админки как стандартный компонент
3. Для корректной работы на сайте должна быть настроена работа главного крона
Период запуска крона запускается 1 раз в 5 минут

Если будет использована фронтальная часть компонента
4. Выставить права доступа к компоненту - в настройках группы пользователей - право Добавление отложенных статей
5. Для пользователя, которому будет разрешен доступ на добавление отложенных статей - добавить пункты в персональное меню 

1) Название пункта меню - Отложенный контент
Ссылка - /pending    или   Компонент + Отложенный контент
Меню   - Меню пользователя (usermenu)
Выставить показ этого пункта для группы редакторов + администраторов
класс - my_content

2) Название пункта меню - Добавить (написать) - вложенный в пункт меню - Отложенный контент
класс ссылки - add_content
ссылка - /pending/add.html
Меню   - Меню пользователя (usermenu)
Родительская ссылка - Отложенный контент
Выставить показ этого пункта для группы редакторов + администраторов


Использование
----------------------------------------

1. Зайти в компонент Отложенные статьи
2. Добавить отложенную статью как обычную статью
3. Определить дату публикации статьи 
4. После того как статья подготовлена - нажать галочку Опубликовано


----------------------------------------

Разработчик 
AlexG
Профиль на InstantCMS http://www.instantcms.ru/users/AlexG
Еще решения для InstantCMS можно найти у меня на сайте:
http://soft-solution.ru
Компоненты, модули, сайты на заказ