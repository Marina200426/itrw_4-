# Лабораторная работа 6

## Описание

Реализация REST API для работы со статьями и комментариями с модульными тестами для класса CreatePost.

## Структура проекта

```
Лабораторная_работа_6/
├── api/
│   └── index.php              # REST API endpoints
├── config/
│   └── database.php           # Конфигурация БД
├── database/
│   ├── schema.sql             # SQL-скрипт для создания таблиц
│   └── app.db                  # Файл базы данных SQLite
├── src/
│   ├── Exceptions/
│   │   ├── PostNotFoundException.php
│   │   ├── UserNotFoundException.php
│   │   └── CommentNotFoundException.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   └── Comment.php
│   ├── Repositories/
│   │   ├── PostsRepository.php
│   │   ├── CommentsRepository.php
│   │   └── UsersRepository.php
│   ├── Services/
│   │   └── CreatePost.php     # Сервис для создания статей
│   └── Utils/
│       ├── UUID.php
│       └── Arguments.php
├── tests/
│   ├── CreatePostTest.php     # Тесты для CreatePost
│   ├── TestRunner.php
│   └── run_tests.php
├── index.php                   # Веб-интерфейс
├── router.php                  # Роутер для PHP сервера
├── styles.css
└── README.md
```

## Требования

- PHP 7.4 или выше
- Расширение PDO с поддержкой SQLite





## Примечания

- База данных SQLite создается автоматически при первом запуске
- Все UUID должны быть в формате: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`
- API возвращает JSON ответы
- Веб-интерфейс автоматически запускает тесты при загрузке страницы


