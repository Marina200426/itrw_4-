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

## Установка и запуск

### 1. Запуск через встроенный веб-сервер PHP

1. Откройте терминал в директории проекта:
   ```bash
   cd "Лабораторная_работа_6"
   ```

2. Запустите встроенный веб-сервер PHP:
   ```bash
   php -S localhost:8000 router.php
   ```

3. Откройте браузер и перейдите по адресу:
   ```
   http://localhost:8000
   ```

### 2. Запуск тестов через командную строку

```bash
cd "Лабораторная_работа_6"
php tests/run_tests.php
```

## API Endpoints

### POST /api/posts/comment

Создание комментария к статье.

**Request:**
```json
POST http://127.0.0.1:8000/api/posts/comment
Content-Type: application/json

{
  "author_uuid": "550e8400-e29b-41d4-a716-446655440000",
  "post_uuid": "660e8400-e29b-41d4-a716-446655440001",
  "text": "Отличная статья!"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Comment created successfully",
  "data": {
    "uuid": "770e8400-e29b-41d4-a716-446655440002",
    "post_uuid": "660e8400-e29b-41d4-a716-446655440001",
    "author_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "text": "Отличная статья!"
  }
}
```

**Ошибки:**
- `400` - Неверный формат UUID или отсутствуют обязательные поля
- `404` - Пользователь или статья не найдены
- `500` - Внутренняя ошибка сервера

### DELETE /api/posts?uuid=<UUID>

Удаление статьи по UUID.

**Request:**
```
DELETE http://127.0.0.1:8000/api/posts?uuid=660e8400-e29b-41d4-a716-446655440001
```

**Response (200):**
```json
{
  "success": true,
  "message": "Post deleted successfully"
}
```

**Ошибки:**
- `400` - Неверный формат UUID или отсутствует параметр uuid
- `404` - Статья не найдена

## Модульные тесты для CreatePost

### Реализованные тесты:

1. ✅ **testCreatePostReturnsSuccess** - класс возвращает успешный ответ
2. ✅ **testCreatePostReturnsErrorForInvalidUUID** - класс возвращает ошибку, если запрос содержит UUID в неверном формате
3. ✅ **testCreatePostReturnsErrorForNonExistentUser** - класс возвращает ошибку, если пользователь не найден по этому UUID
4. ✅ **testCreatePostReturnsErrorForMissingFields** - класс возвращает ошибку, если запрос не содержит всех данных, необходимых для создания статьи

## Использование API

### Пример с curl:

```bash
# Создание комментария
curl -X POST http://127.0.0.1:8000/api/posts/comment \
  -H "Content-Type: application/json" \
  -d '{
    "author_uuid": "550e8400-e29b-41d4-a716-446655440000",
    "post_uuid": "660e8400-e29b-41d4-a716-446655440001",
    "text": "Отличная статья!"
  }'

# Удаление статьи
curl -X DELETE "http://127.0.0.1:8000/api/posts?uuid=660e8400-e29b-41d4-a716-446655440001"
```

### Пример с JavaScript (fetch):

```javascript
// Создание комментария
fetch('http://127.0.0.1:8000/api/posts/comment', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    author_uuid: '550e8400-e29b-41d4-a716-446655440000',
    post_uuid: '660e8400-e29b-41d4-a716-446655440001',
    text: 'Отличная статья!'
  })
})
.then(response => response.json())
.then(data => console.log(data));

// Удаление статьи
fetch('http://127.0.0.1:8000/api/posts?uuid=660e8400-e29b-41d4-a716-446655440001', {
  method: 'DELETE'
})
.then(response => response.json())
.then(data => console.log(data));
```

## Веб-интерфейс

Веб-интерфейс доступен по адресу `http://localhost:8000` и предоставляет:

- Результаты модульных тестов
- Документацию API endpoints
- Интерактивное тестирование API через формы

## Примечания

- База данных SQLite создается автоматически при первом запуске
- Все UUID должны быть в формате: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`
- API возвращает JSON ответы
- Веб-интерфейс автоматически запускает тесты при загрузке страницы

