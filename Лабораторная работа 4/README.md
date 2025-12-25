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

## Установка и запуск

### 1. Проверка наличия PHP

Откройте терминал (командную строку) и выполните:

```bash
php -v
```

Должна отобразиться версия PHP. Если PHP не установлен, скачайте его с [официального сайта](https://www.php.net/downloads.php).

### 2. Переход в директорию проекта

```bash
cd "Лабораторная_работа_4"
```

### 3. Инициализация базы данных

База данных будет создана автоматически при первом запуске скрипта. Файл базы данных `database/app.db` будет создан автоматически.

### 4. Запуск примера (консольный)

```bash
php example.php
```

Этот скрипт:
- Создаст базу данных (если её нет)
- Создаст таблицы (если их нет)
- Создаст пример пользователя, статьи и комментария
- Продемонстрирует работу репозиториев

### 5. Запуск веб-интерфейса (рекомендуется)

#### Вариант A: Встроенный веб-сервер PHP

1. Откройте терминал в директории проекта:
   ```bash
   cd "Лабораторная_работа_4"
   ```

2. Запустите встроенный веб-сервер PHP:
   ```bash
   php -S localhost:8000
   ```

3. Откройте браузер и перейдите по адресу:
   ```
   http://localhost:8000
   ```

#### Вариант B: Через существующий веб-сервер

Если у вас установлен Apache, Nginx или другой веб-сервер:

1. Скопируйте папку проекта в директорию веб-сервера
2. Откройте в браузере: `http://localhost/Лабораторная_работа_4/`

#### Возможности веб-интерфейса:

- **Вкладка "Создание"**:
  - Создание пользователей с генератором UUID
  - Создание статей с выбором автора из списка
  - Создание комментариев с выбором статьи и автора
  
- **Вкладка "Просмотр"**:
  - Таблица всех пользователей
  - Карточки статей с информацией об авторе
  - Список комментариев с привязкой к статьям
  
- **Вкладка "Поиск"**:
  - Поиск статьи по UUID
  - Поиск комментария по UUID
  - Отображение результатов поиска

Веб-интерфейс автоматически создаст базу данных и таблицы при первом запуске.

## Использование в своем коде

```php
<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/Post.php';
require_once __DIR__ . '/src/Repositories/PostsRepository.php';

// Инициализация базы данных
$dbConfig = new DatabaseConfig();
$dbConfig->initializeDatabase();
$pdo = $dbConfig->getConnection();

// Создание репозитория
$postsRepository = new PostsRepository($pdo);

// Создание статьи
$post = new Post(
    'uuid-статьи',
    'uuid-автора',
    'Заголовок',
    'Текст статьи'
);

// Сохранение статьи
$postsRepository->save($post);

// Получение статьи
$retrievedPost = $postsRepository->get('uuid-статьи');
```

## Интерфейсы репозиториев

### PostsRepositoryInterface

```php
interface PostsRepositoryInterface
{
    public function get(string $uuid): ?Post;
    public function save(Post $post): void;
}
```

### CommentsRepositoryInterface

```php
interface CommentsRepositoryInterface
{
    public function get(string $uuid): ?Comment;
    public function save(Comment $comment): void;
}
```

## Примечания

- Все идентификаторы используют тип UUID (строка)
- База данных SQLite создается автоматически при первом запуске
- Для генерации UUID можно использовать функцию `uniqid()` или библиотеку для генерации UUID
- В Windows пути могут содержать кириллицу, убедитесь, что PHP настроен правильно

