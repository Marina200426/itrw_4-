# Лабораторная работа 4

## Описание

Реализация работы с базой данных SQLite для хранения статей и комментариев с использованием UUID в качестве идентификаторов.

## Структура проекта

```
Лабораторная_работа_4/
├── config/
│   └── database.php          # Конфигурация подключения к БД
├── database/
│   ├── schema.sql            # SQL-скрипт для создания таблиц
│   └── app.db                # Файл базы данных SQLite (создается автоматически)
├── src/
│   ├── Models/
│   │   ├── User.php          # Модель пользователя
│   │   ├── Post.php          # Модель статьи
│   │   └── Comment.php       # Модель комментария
│   └── Repositories/
│       ├── PostsRepositoryInterface.php      # Интерфейс репозитория статей
│       ├── CommentsRepositoryInterface.php   # Интерфейс репозитория комментариев
│       ├── PostsRepository.php               # Реализация репозитория статей
│       └── CommentsRepository.php            # Реализация репозитория комментариев
├── example.php               # Пример использования
├── index.php                 # Веб-интерфейс для тестирования
├── styles.css                # Стили для веб-интерфейса
└── README.md                 # Этот файл
```

## Структура базы данных

### Таблица users
- `uuid` (TEXT, PRIMARY KEY) - UUID пользователя
- `username` (TEXT, UNIQUE) - Имя пользователя
- `first_name` (TEXT) - Имя
- `last_name` (TEXT) - Фамилия

### Таблица posts
- `uuid` (TEXT, PRIMARY KEY) - UUID статьи
- `author_uuid` (TEXT, FOREIGN KEY) - UUID автора статьи
- `title` (TEXT) - Заголовок статьи
- `text` (TEXT) - Текст статьи

### Таблица comments
- `uuid` (TEXT, PRIMARY KEY) - UUID комментария
- `posts_uuid` (TEXT, FOREIGN KEY) - UUID статьи
- `author_uuid` (TEXT, FOREIGN KEY) - UUID автора комментария
- `text` (TEXT) - Текст комментария

## Требования

- PHP 7.4 или выше
- Расширение PDO с поддержкой SQLite



